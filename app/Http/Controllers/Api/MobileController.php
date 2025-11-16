<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\FuelConsumption;
use App\Models\Intervention;
use App\Models\GpsLocation;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MobileController extends Controller
{
    /**
     * Get mobile dashboard data
     */
    public function dashboard(): JsonResponse
    {
        $user = auth()->user();

        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar ?? null,
            ],
            'stats' => [
                'total_vehicles' => Vehicle::count(),
                'available_vehicles' => Vehicle::where('status', 'disponible')->count(),
                'urgent_interventions' => Intervention::where('urgency', 'tres_urgent')->count(),
                'unread_notifications' => Notification::where('notifiable_id', $user->id)
                    ->whereNull('read_at')
                    ->count(),
            ],
            'recent_alerts' => Notification::where('notifiable_id', $user->id)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get quick statistics
     */
    public function quickStats(): JsonResponse
    {
        $stats = [
            'vehicles' => Vehicle::count(),
            'available' => Vehicle::where('status', 'disponible')->count(),
            'in_mission' => Vehicle::where('status', 'en_mission')->count(),
            'interventions_today' => Intervention::whereDate('created_at', today())->count(),
            'fuel_entries_today' => FuelConsumption::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Update vehicle GPS location
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|integer|between:0,360',
            'altitude' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $location = GpsLocation::create([
            'vehicle_id' => $request->vehicle_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'altitude' => $request->altitude,
            'accuracy' => $request->accuracy,
            'recorded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => $location,
        ]);
    }

    /**
     * Get nearby vehicles
     */
    public function nearbyVehicles(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0|max:100', // km
        ]);

        $radius = $request->get('radius', 10); // Default 10km

        // This is a simplified version - in production, use spatial queries
        $vehicles = Vehicle::with(['latestGpsLocation', 'brand', 'vehicleModel'])
            ->whereHas('latestGpsLocation')
            ->get()
            ->filter(function ($vehicle) use ($request, $radius) {
                if (!$vehicle->latestGpsLocation) {
                    return false;
                }

                $distance = $this->calculateDistance(
                    $request->latitude,
                    $request->longitude,
                    $vehicle->latestGpsLocation->latitude,
                    $vehicle->latestGpsLocation->longitude
                );

                return $distance <= $radius;
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => $vehicles,
            'count' => $vehicles->count(),
        ]);
    }

    /**
     * Get mobile notifications
     */
    public function notifications(): JsonResponse
    {
        $notifications = Notification::where('notifiable_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id): JsonResponse
    {
        $notification = Notification::where('notifiable_id', auth()->id())
            ->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Search vehicles
     */
    public function searchVehicles(Request $request): JsonResponse
    {
        $query = $request->get('q');

        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->where('registration_number', 'like', "%{$query}%")
            ->orWhere('internal_code', 'like', "%{$query}%")
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vehicles,
        ]);
    }

    /**
     * Get vehicle quick info
     */
    public function vehicleQuickInfo(Vehicle $vehicle): JsonResponse
    {
        $vehicle->load(['brand', 'vehicleModel', 'latestGpsLocation']);

        $data = [
            'id' => $vehicle->id,
            'registration' => $vehicle->registration_number,
            'brand' => $vehicle->brand?->name,
            'model' => $vehicle->vehicleModel?->name,
            'status' => $vehicle->status,
            'mileage' => $vehicle->current_mileage,
            'location' => $vehicle->latestGpsLocation ? [
                'latitude' => $vehicle->latestGpsLocation->latitude,
                'longitude' => $vehicle->latestGpsLocation->longitude,
                'address' => $vehicle->latestGpsLocation->address,
                'updated_at' => $vehicle->latestGpsLocation->recorded_at,
            ] : null,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Quick fuel entry (simplified form)
     */
    public function quickFuelEntry(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'quantity_liters' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'mileage' => 'required|integer|min:0',
        ]);

        $fuelEntry = FuelConsumption::create([
            'vehicle_id' => $request->vehicle_id,
            'date' => now(),
            'quantity_liters' => $request->quantity_liters,
            'unit_price' => $request->unit_price,
            'total_cost' => $request->quantity_liters * $request->unit_price,
            'mileage' => $request->mileage,
            'fuel_type' => $request->get('fuel_type', 'diesel'),
            'supplier_id' => $request->get('supplier_id'),
            'notes' => $request->get('notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fuel entry added successfully',
            'data' => $fuelEntry,
        ], 201);
    }

    /**
     * Quick intervention report (simplified form)
     */
    public function quickInterventionReport(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'urgency' => 'required|in:tres_urgent,urgent,normal,faible',
        ]);

        $intervention = Intervention::create([
            'vehicle_id' => $request->vehicle_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->get('type', 'curative'),
            'urgency' => $request->urgency,
            'status' => 'en_attente',
            'request_date' => now(),
            'requested_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Intervention reported successfully',
            'data' => $intervention,
        ], 201);
    }

    /**
     * Calculate distance between two GPS coordinates (Haversine formula)
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
