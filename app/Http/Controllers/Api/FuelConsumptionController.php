<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FuelConsumption;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class FuelConsumptionController extends Controller
{
    /**
     * Display a listing of fuel consumptions
     */
    public function index(Request $request): JsonResponse
    {
        $query = FuelConsumption::with(['vehicle', 'fuelType', 'employee']);

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('refuel_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $consumptions = $query->latest('refuel_date')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $consumptions
        ]);
    }

    /**
     * Store a newly created fuel consumption
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'employee_id' => 'nullable|exists:employees,id',
            'refuel_date' => 'required|date',
            'refuel_time' => 'nullable|date_format:H:i',
            'quantity' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'total_cost' => 'required|numeric|min:0',
            'mileage' => 'required|integer|min:0',
            'station' => 'nullable|string|max:255',
            'voucher_number' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $consumption = FuelConsumption::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fuel consumption recorded successfully',
            'data' => $consumption
        ], 201);
    }

    /**
     * Display the specified fuel consumption
     */
    public function show(FuelConsumption $fuelConsumption): JsonResponse
    {
        $fuelConsumption->load(['vehicle', 'fuelType', 'employee']);

        return response()->json([
            'success' => true,
            'data' => $fuelConsumption
        ]);
    }

    /**
     * Update the specified fuel consumption
     */
    public function update(Request $request, FuelConsumption $fuelConsumption): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'fuel_type_id' => 'sometimes|exists:fuel_types,id',
            'employee_id' => 'nullable|exists:employees,id',
            'refuel_date' => 'sometimes|date',
            'refuel_time' => 'nullable|date_format:H:i',
            'quantity' => 'sometimes|numeric|min:0',
            'unit_price' => 'sometimes|numeric|min:0',
            'total_cost' => 'sometimes|numeric|min:0',
            'mileage' => 'sometimes|integer|min:0',
            'station' => 'nullable|string|max:255',
            'voucher_number' => 'nullable|string|max:255',
            'card_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $fuelConsumption->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Fuel consumption updated successfully',
            'data' => $fuelConsumption
        ]);
    }

    /**
     * Remove the specified fuel consumption
     */
    public function destroy(FuelConsumption $fuelConsumption): JsonResponse
    {
        $fuelConsumption->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fuel consumption deleted successfully'
        ]);
    }

    /**
     * Get fuel consumption statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $query = FuelConsumption::query();

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('refuel_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $stats = [
            'total_consumptions' => $query->count(),
            'total_quantity' => round($query->sum('quantity'), 2),
            'total_cost' => round($query->sum('total_cost'), 2),
            'average_unit_price' => round($query->avg('unit_price'), 2),
            'average_quantity' => round($query->avg('quantity'), 2),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get fuel consumption trend
     */
    public function trend(Request $request): JsonResponse
    {
        $vehicleId = $request->input('vehicle_id');
        $months = $request->input('months', 12);

        $startDate = now()->subMonths($months);

        $query = FuelConsumption::where('refuel_date', '>=', $startDate);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        $trend = $query->selectRaw('
                DATE_FORMAT(refuel_date, "%Y-%m") as month,
                SUM(quantity) as total_quantity,
                SUM(total_cost) as total_cost,
                AVG(unit_price) as avg_price,
                COUNT(*) as count
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $trend
        ]);
    }
}
