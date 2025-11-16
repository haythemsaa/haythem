<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache TTL in minutes
     */
    protected int $ttl = 60;

    /**
     * Get dashboard statistics with caching
     */
    public function getDashboardStats()
    {
        return Cache::remember('dashboard.stats', $this->ttl, function () {
            return [
                'vehicles' => \App\Models\Vehicle::count(),
                'employees' => \App\Models\Employee::count(),
                'interventions' => \App\Models\Intervention::count(),
                'fuel_consumptions' => \App\Models\FuelConsumption::count(),
            ];
        });
    }

    /**
     * Get fleet statistics with caching
     */
    public function getFleetStats()
    {
        return Cache::remember('fleet.stats', $this->ttl, function () {
            return [
                'total' => \App\Models\Vehicle::count(),
                'available' => \App\Models\Vehicle::where('status', 'disponible')->count(),
                'in_mission' => \App\Models\Vehicle::where('status', 'en_mission')->count(),
                'in_maintenance' => \App\Models\Vehicle::where('status', 'en_maintenance')->count(),
                'out_of_service' => \App\Models\Vehicle::where('status', 'hors_service')->count(),
            ];
        });
    }

    /**
     * Clear all dashboard caches
     */
    public function clearDashboardCache()
    {
        Cache::forget('dashboard.stats');
        Cache::forget('fleet.stats');
        Cache::forget('settings');
    }

    /**
     * Clear cache for specific vehicle
     */
    public function clearVehicleCache($vehicleId)
    {
        Cache::forget("vehicle.{$vehicleId}");
        Cache::forget("vehicle.{$vehicleId}.stats");
        $this->clearDashboardCache();
    }

    /**
     * Get or cache complex query results
     */
    public function rememberQuery(string $key, callable $callback, ?int $ttl = null)
    {
        return Cache::remember($key, $ttl ?? $this->ttl, $callback);
    }

    /**
     * Invalidate cache by tag (if using Redis or Memcached)
     */
    public function invalidateByTag(string $tag)
    {
        try {
            Cache::tags([$tag])->flush();
        } catch (\Exception $e) {
            // Tags not supported, clear all cache
            Cache::flush();
        }
    }
}
