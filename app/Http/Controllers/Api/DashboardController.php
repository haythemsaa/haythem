<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Intervention;
use App\Models\FuelConsumption;
use App\Models\Rental;
use App\Models\Accident;
use App\Services\AlertService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Get main dashboard statistics
     */
    public function index(): JsonResponse
    {
        $stats = [
            'fleet' => $this->getFleetStats(),
            'maintenance' => $this->getMaintenanceStats(),
            'fuel' => $this->getFuelStats(),
            'financial' => $this->getFinancialStats(),
            'alerts' => $this->alertService->getAlertStats(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get fleet overview statistics
     */
    protected function getFleetStats(): array
    {
        return [
            'total_vehicles' => Vehicle::count(),
            'available' => Vehicle::where('status', 'disponible')->count(),
            'in_service' => Vehicle::where('status', 'en_mission')->count(),
            'in_maintenance' => Vehicle::where('status', 'en_maintenance')->count(),
            'out_of_service' => Vehicle::where('status', 'en_panne')->count(),
            'utilization_rate' => $this->calculateUtilizationRate(),
        ];
    }

    /**
     * Get maintenance statistics
     */
    protected function getMaintenanceStats(): array
    {
        $thisMonth = Intervention::whereMonth('request_date', now()->month)
            ->whereYear('request_date', now()->year);

        $lastMonth = Intervention::whereMonth('request_date', now()->subMonth()->month)
            ->whereYear('request_date', now()->subMonth()->year);

        return [
            'total_interventions' => Intervention::count(),
            'pending' => Intervention::where('status', 'en_attente')->count(),
            'in_progress' => Intervention::where('status', 'en_reparation')->count(),
            'completed_this_month' => (clone $thisMonth)->where('status', 'cloture')->count(),
            'completed_last_month' => (clone $lastMonth)->where('status', 'cloture')->count(),
            'urgent' => Intervention::where('urgency', 'tres_urgent')
                ->whereNotIn('status', ['cloture', 'annule'])
                ->count(),
        ];
    }

    /**
     * Get fuel consumption statistics
     */
    protected function getFuelStats(): array
    {
        $thisMonth = FuelConsumption::whereMonth('refuel_date', now()->month)
            ->whereYear('refuel_date', now()->year);

        $lastMonth = FuelConsumption::whereMonth('refuel_date', now()->subMonth()->month)
            ->whereYear('refuel_date', now()->subMonth()->year);

        return [
            'total_cost_this_month' => round((clone $thisMonth)->sum('total_cost'), 2),
            'total_cost_last_month' => round((clone $lastMonth)->sum('total_cost'), 2),
            'total_liters_this_month' => round((clone $thisMonth)->sum('quantity'), 2),
            'avg_price_per_liter' => round((clone $thisMonth)->avg('unit_price'), 2),
            'trend' => $this->getFuelTrend(),
        ];
    }

    /**
     * Get financial statistics
     */
    protected function getFinancialStats(): array
    {
        $thisMonth = now();

        // Rental revenue
        $rentalRevenue = Rental::whereMonth('start_date', $thisMonth->month)
            ->whereYear('start_date', $thisMonth->year)
            ->sum('total_cost');

        // Maintenance costs
        $maintenanceCosts = Intervention::whereMonth('request_date', $thisMonth->month)
            ->whereYear('request_date', $thisMonth->year)
            ->with('workOrders')
            ->get()
            ->flatMap->workOrders
            ->sum('total_cost');

        // Fuel costs
        $fuelCosts = FuelConsumption::whereMonth('refuel_date', $thisMonth->month)
            ->whereYear('refuel_date', $thisMonth->year)
            ->sum('total_cost');

        return [
            'rental_revenue' => round($rentalRevenue, 2),
            'maintenance_costs' => round($maintenanceCosts, 2),
            'fuel_costs' => round($fuelCosts, 2),
            'net_income' => round($rentalRevenue - $maintenanceCosts - $fuelCosts, 2),
        ];
    }

    /**
     * Get fleet alerts
     */
    public function alerts(): JsonResponse
    {
        $alerts = $this->alertService->getAllAlerts();

        return response()->json([
            'success' => true,
            'data' => $alerts
        ]);
    }

    /**
     * Get fleet performance metrics
     */
    public function performance(): JsonResponse
    {
        $metrics = [
            'utilization_rate' => $this->calculateUtilizationRate(),
            'maintenance_efficiency' => $this->calculateMaintenanceEfficiency(),
            'fuel_efficiency' => $this->calculateFleetFuelEfficiency(),
            'safety_score' => $this->calculateSafetyScore(),
        ];

        return response()->json([
            'success' => true,
            'data' => $metrics
        ]);
    }

    /**
     * Get trend data for charts
     */
    public function trends(Request $request): JsonResponse
    {
        $months = $request->input('months', 12);
        $type = $request->input('type', 'fuel'); // fuel, maintenance, revenue

        $data = match($type) {
            'fuel' => $this->getFuelTrend($months),
            'maintenance' => $this->getMaintenanceTrend($months),
            'revenue' => $this->getRevenueTrend($months),
            default => [],
        };

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Calculate fleet utilization rate
     */
    protected function calculateUtilizationRate(): float
    {
        $total = Vehicle::count();
        if ($total == 0) return 0;

        $inUse = Vehicle::whereIn('status', ['en_mission', 'en_maintenance'])->count();

        return round(($inUse / $total) * 100, 1);
    }

    /**
     * Calculate maintenance efficiency
     */
    protected function calculateMaintenanceEfficiency(): float
    {
        $total = Intervention::count();
        if ($total == 0) return 100;

        $completed = Intervention::where('status', 'cloture')->count();

        return round(($completed / $total) * 100, 1);
    }

    /**
     * Calculate fleet average fuel efficiency
     */
    protected function calculateFleetFuelEfficiency(): float
    {
        // Average L/100km for fleet (simplified)
        $vehicles = Vehicle::whereHas('fuelConsumptions', function($q) {
            $q->where('refuel_date', '>=', now()->subDays(90));
        })->with(['fuelConsumptions' => function($q) {
            $q->where('refuel_date', '>=', now()->subDays(90));
        }])->get();

        if ($vehicles->count() == 0) return 0;

        $totalEfficiency = 0;
        $count = 0;

        foreach ($vehicles as $vehicle) {
            $consumptions = $vehicle->fuelConsumptions->sortBy('refuel_date');
            if ($consumptions->count() < 2) continue;

            $totalLiters = 0;
            $totalKm = 0;

            for ($i = 1; $i < $consumptions->count(); $i++) {
                $km = $consumptions[$i]->mileage - $consumptions[$i-1]->mileage;
                if ($km > 0) {
                    $totalLiters += $consumptions[$i]->quantity;
                    $totalKm += $km;
                }
            }

            if ($totalKm > 0) {
                $totalEfficiency += ($totalLiters / $totalKm) * 100;
                $count++;
            }
        }

        return $count > 0 ? round($totalEfficiency / $count, 2) : 0;
    }

    /**
     * Calculate safety score based on accidents
     */
    protected function calculateSafetyScore(): float
    {
        $accidents = Accident::whereYear('accident_date', now()->year)->count();
        $vehicles = Vehicle::count();

        if ($vehicles == 0) return 100;

        // Score formula: 100 - (accidents_per_vehicle * 10)
        $accidentsPerVehicle = $accidents / $vehicles;
        $score = max(0, 100 - ($accidentsPerVehicle * 10));

        return round($score, 1);
    }

    /**
     * Get fuel consumption trend
     */
    protected function getFuelTrend(int $months = 6): array
    {
        $trend = [];
        $startDate = now()->subMonths($months);

        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($months - $i - 1);

            $data = FuelConsumption::whereYear('refuel_date', $date->year)
                ->whereMonth('refuel_date', $date->month)
                ->selectRaw('
                    SUM(quantity) as total_liters,
                    SUM(total_cost) as total_cost,
                    AVG(unit_price) as avg_price
                ')
                ->first();

            $trend[] = [
                'month' => $date->format('M Y'),
                'total_liters' => round($data->total_liters ?? 0, 2),
                'total_cost' => round($data->total_cost ?? 0, 2),
                'avg_price' => round($data->avg_price ?? 0, 2),
            ];
        }

        return $trend;
    }

    /**
     * Get maintenance trend
     */
    protected function getMaintenanceTrend(int $months = 6): array
    {
        $trend = [];

        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($months - $i - 1);

            $interventions = Intervention::whereYear('request_date', $date->year)
                ->whereMonth('request_date', $date->month)
                ->with('workOrders')
                ->get();

            $totalCost = $interventions->flatMap->workOrders->sum('total_cost');

            $trend[] = [
                'month' => $date->format('M Y'),
                'count' => $interventions->count(),
                'total_cost' => round($totalCost, 2),
            ];
        }

        return $trend;
    }

    /**
     * Get revenue trend
     */
    protected function getRevenueTrend(int $months = 6): array
    {
        $trend = [];

        for ($i = 0; $i < $months; $i++) {
            $date = now()->subMonths($months - $i - 1);

            $revenue = Rental::whereYear('start_date', $date->year)
                ->whereMonth('start_date', $date->month)
                ->sum('total_cost');

            $trend[] = [
                'month' => $date->format('M Y'),
                'revenue' => round($revenue, 2),
            ];
        }

        return $trend;
    }
}
