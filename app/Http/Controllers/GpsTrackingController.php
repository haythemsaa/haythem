<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\GpsLocation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GpsTrackingController extends Controller
{
    /**
     * Display GPS tracking dashboard
     */
    public function index()
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel', 'latestGpsLocation'])
            ->whereIn('status', ['disponible', 'en_mission'])
            ->get();

        return view('gps.index', compact('vehicles'));
    }

    /**
     * Display specific vehicle tracking
     */
    public function vehicleTracking(Vehicle $vehicle)
    {
        $locations = GpsLocation::where('vehicle_id', $vehicle->id)
            ->orderBy('recorded_at', 'desc')
            ->limit(100)
            ->get();

        return view('gps.vehicle', compact('vehicle', 'locations'));
    }

    /**
     * Display live tracking map
     */
    public function liveTracking()
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel', 'latestGpsLocation'])
            ->whereIn('status', ['disponible', 'en_mission'])
            ->get();

        return view('gps.live', compact('vehicles'));
    }

    /**
     * Display vehicle history
     */
    public function history(Vehicle $vehicle, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfDay());
        $endDate = $request->get('end_date', now()->endOfDay());

        $locations = GpsLocation::where('vehicle_id', $vehicle->id)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'asc')
            ->get();

        $totalDistance = $this->calculateTotalDistance($locations);
        $averageSpeed = $locations->avg('speed') ?? 0;
        $maxSpeed = $locations->max('speed') ?? 0;

        $stats = [
            'total_distance' => round($totalDistance, 2),
            'average_speed' => round($averageSpeed, 2),
            'max_speed' => round($maxSpeed, 2),
            'duration' => $this->calculateDuration($locations),
            'stops' => $this->detectStops($locations),
        ];

        return view('gps.history', compact('vehicle', 'locations', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Calculate total distance from GPS points
     */
    protected function calculateTotalDistance($locations)
    {
        if ($locations->count() < 2) {
            return 0;
        }

        $totalDistance = 0;
        $previous = null;

        foreach ($locations as $location) {
            if ($previous) {
                $totalDistance += $this->haversineDistance(
                    $previous->latitude,
                    $previous->longitude,
                    $location->latitude,
                    $location->longitude
                );
            }
            $previous = $location;
        }

        return $totalDistance;
    }

    /**
     * Haversine formula for distance calculation
     */
    protected function haversineDistance($lat1, $lon1, $lat2, $lon2)
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

    /**
     * Calculate trip duration
     */
    protected function calculateDuration($locations)
    {
        if ($locations->count() < 2) {
            return 0;
        }

        $first = $locations->first();
        $last = $locations->last();

        return $first->recorded_at->diffInMinutes($last->recorded_at);
    }

    /**
     * Detect stops in the route
     */
    protected function detectStops($locations, $speedThreshold = 5, $durationThreshold = 5)
    {
        $stops = [];
        $currentStop = null;

        foreach ($locations as $location) {
            if ($location->speed < $speedThreshold) {
                if (!$currentStop) {
                    $currentStop = [
                        'start' => $location->recorded_at,
                        'location' => $location,
                    ];
                }
            } else {
                if ($currentStop) {
                    $duration = $currentStop['start']->diffInMinutes($location->recorded_at);
                    if ($duration >= $durationThreshold) {
                        $stops[] = [
                            'latitude' => $currentStop['location']->latitude,
                            'longitude' => $currentStop['location']->longitude,
                            'start' => $currentStop['start'],
                            'duration' => $duration,
                        ];
                    }
                    $currentStop = null;
                }
            }
        }

        return $stops;
    }
}
