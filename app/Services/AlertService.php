<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Insurance;
use App\Models\Intervention;
use App\Models\FuelConsumption;
use App\Models\InventoryPart;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AlertService
{
    /**
     * Get all fleet alerts
     */
    public function getAllAlerts(): array
    {
        return [
            'critical' => $this->getCriticalAlerts(),
            'warnings' => $this->getWarningAlerts(),
            'info' => $this->getInfoAlerts(),
            'stats' => $this->getAlertStats(),
        ];
    }

    /**
     * Get critical alerts (urgent action required)
     */
    public function getCriticalAlerts(): array
    {
        $alerts = [];

        // Expired insurances
        $expiredInsurances = Insurance::with('vehicle')
            ->where('end_date', '<', now())
            ->get();

        foreach ($expiredInsurances as $insurance) {
            $alerts[] = [
                'type' => 'insurance_expired',
                'level' => 'critical',
                'title' => 'Assurance Expirée',
                'message' => "L'assurance du véhicule {$insurance->vehicle->registration_number} a expiré le " . $insurance->end_date->format('d/m/Y'),
                'data' => $insurance,
                'action_url' => route('insurances.edit', $insurance),
            ];
        }

        // Vehicles out of service
        $outOfService = Vehicle::where('status', 'en_panne')->get();
        foreach ($outOfService as $vehicle) {
            $alerts[] = [
                'type' => 'vehicle_breakdown',
                'level' => 'critical',
                'title' => 'Véhicule en Panne',
                'message' => "Le véhicule {$vehicle->registration_number} est en panne",
                'data' => $vehicle,
                'action_url' => route('vehicles.show', $vehicle),
            ];
        }

        // Urgent interventions not addressed
        $urgentInterventions = Intervention::with('vehicle')
            ->where('urgency', 'tres_urgent')
            ->where('status', 'en_attente')
            ->where('request_date', '<', now()->subHours(24))
            ->get();

        foreach ($urgentInterventions as $intervention) {
            $alerts[] = [
                'type' => 'urgent_intervention',
                'level' => 'critical',
                'title' => 'Intervention Urgente Non Traitée',
                'message' => "Intervention urgente pour {$intervention->vehicle->registration_number} depuis " . $intervention->request_date->diffForHumans(),
                'data' => $intervention,
                'action_url' => route('interventions.show', $intervention),
            ];
        }

        // Out of stock critical parts
        $outOfStock = InventoryPart::where('quantity_in_stock', '=', 0)
            ->where('is_critical', true)
            ->get();

        foreach ($outOfStock as $part) {
            $alerts[] = [
                'type' => 'critical_part_out_of_stock',
                'level' => 'critical',
                'title' => 'Pièce Critique Épuisée',
                'message' => "La pièce critique '{$part->part_name}' est en rupture de stock",
                'data' => $part,
                'action_url' => route('inventory-parts.show', $part),
            ];
        }

        return $alerts;
    }

    /**
     * Get warning alerts (need attention soon)
     */
    public function getWarningAlerts(): array
    {
        $alerts = [];

        // Insurances expiring soon (30 days)
        $expiringSoon = Insurance::with('vehicle')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->get();

        foreach ($expiringSoon as $insurance) {
            $daysLeft = now()->diffInDays($insurance->end_date);
            $alerts[] = [
                'type' => 'insurance_expiring',
                'level' => 'warning',
                'title' => 'Assurance Expire Bientôt',
                'message' => "L'assurance du véhicule {$insurance->vehicle->registration_number} expire dans {$daysLeft} jours",
                'data' => $insurance,
                'action_url' => route('insurances.edit', $insurance),
            ];
        }

        // Vehicles due for preventive maintenance
        $maintenanceDue = Vehicle::with('preventiveMaintenancePlans')
            ->whereHas('preventiveMaintenancePlans', function($q) {
                $q->where('next_execution_date', '<=', now()->addDays(7))
                  ->where('status', 'planifie');
            })
            ->get();

        foreach ($maintenanceDue as $vehicle) {
            $alerts[] = [
                'type' => 'maintenance_due',
                'level' => 'warning',
                'title' => 'Maintenance Préventive Due',
                'message' => "Le véhicule {$vehicle->registration_number} nécessite une maintenance préventive",
                'data' => $vehicle,
                'action_url' => route('vehicles.show', $vehicle),
            ];
        }

        // Low stock parts
        $lowStock = InventoryPart::whereColumn('quantity_in_stock', '<=', 'minimum_stock')
            ->where('quantity_in_stock', '>', 0)
            ->get();

        foreach ($lowStock as $part) {
            $alerts[] = [
                'type' => 'low_stock',
                'level' => 'warning',
                'title' => 'Stock Bas',
                'message' => "Le stock de '{$part->part_name}' est bas ({$part->quantity_in_stock} restant)",
                'data' => $part,
                'action_url' => route('inventory-parts.show', $part),
            ];
        }

        // High fuel consumption anomalies
        $highConsumption = $this->detectHighFuelConsumption();
        foreach ($highConsumption as $item) {
            $alerts[] = [
                'type' => 'high_fuel_consumption',
                'level' => 'warning',
                'title' => 'Consommation Carburant Élevée',
                'message' => "Le véhicule {$item['vehicle']->registration_number} a une consommation anormalement élevée",
                'data' => $item,
                'action_url' => route('vehicles.show', $item['vehicle']),
            ];
        }

        return $alerts;
    }

    /**
     * Get informational alerts
     */
    public function getInfoAlerts(): array
    {
        $alerts = [];

        // Vehicles available for rental
        $availableCount = Vehicle::where('status', 'disponible')->count();
        if ($availableCount > 0) {
            $alerts[] = [
                'type' => 'vehicles_available',
                'level' => 'info',
                'title' => 'Véhicules Disponibles',
                'message' => "{$availableCount} véhicule(s) disponible(s) pour location",
                'data' => ['count' => $availableCount],
            ];
        }

        // Recently completed interventions
        $recentlyCompleted = Intervention::where('status', 'cloture')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        if ($recentlyCompleted > 0) {
            $alerts[] = [
                'type' => 'interventions_completed',
                'level' => 'info',
                'title' => 'Interventions Complétées',
                'message' => "{$recentlyCompleted} intervention(s) complétée(s) cette semaine",
                'data' => ['count' => $recentlyCompleted],
            ];
        }

        return $alerts;
    }

    /**
     * Get alert statistics
     */
    public function getAlertStats(): array
    {
        $critical = count($this->getCriticalAlerts());
        $warnings = count($this->getWarningAlerts());
        $info = count($this->getInfoAlerts());

        return [
            'critical_count' => $critical,
            'warning_count' => $warnings,
            'info_count' => $info,
            'total_count' => $critical + $warnings + $info,
        ];
    }

    /**
     * Detect vehicles with high fuel consumption
     */
    protected function detectHighFuelConsumption(): array
    {
        $anomalies = [];

        // Get vehicles with recent fuel consumption data
        $vehicles = Vehicle::whereHas('fuelConsumptions', function($q) {
            $q->where('refuel_date', '>=', now()->subDays(90));
        })->with(['fuelConsumptions' => function($q) {
            $q->where('refuel_date', '>=', now()->subDays(90))
              ->orderBy('refuel_date');
        }])->get();

        foreach ($vehicles as $vehicle) {
            if ($vehicle->fuelConsumptions->count() < 3) continue;

            // Calculate average consumption (liters per 100km)
            $avgConsumption = $this->calculateAverageFuelConsumption($vehicle->fuelConsumptions);

            // If consumption is > 20% above expected, flag it
            $expectedConsumption = 8.0; // Default expected L/100km
            if ($avgConsumption > $expectedConsumption * 1.2) {
                $anomalies[] = [
                    'vehicle' => $vehicle,
                    'current_consumption' => round($avgConsumption, 2),
                    'expected_consumption' => $expectedConsumption,
                    'excess_percentage' => round((($avgConsumption / $expectedConsumption) - 1) * 100, 1),
                ];
            }
        }

        return $anomalies;
    }

    /**
     * Calculate average fuel consumption from records
     */
    protected function calculateAverageFuelConsumption(Collection $consumptions): float
    {
        if ($consumptions->count() < 2) return 0;

        $totalLiters = 0;
        $totalKm = 0;

        for ($i = 1; $i < $consumptions->count(); $i++) {
            $current = $consumptions[$i];
            $previous = $consumptions[$i - 1];

            $liters = $current->quantity;
            $km = $current->mileage - $previous->mileage;

            if ($km > 0) {
                $totalLiters += $liters;
                $totalKm += $km;
            }
        }

        if ($totalKm == 0) return 0;

        return ($totalLiters / $totalKm) * 100; // L/100km
    }

    /**
     * Check if vehicle needs maintenance based on mileage and date
     */
    public function checkMaintenanceNeeded(Vehicle $vehicle): ?array
    {
        $plans = $vehicle->preventiveMaintenancePlans()
            ->where('status', 'planifie')
            ->get();

        foreach ($plans as $plan) {
            $needsMaintenance = false;
            $reason = '';

            // Check mileage-based
            if ($plan->frequency_type in ['mileage', 'both'] && $plan->next_execution_mileage) {
                if ($vehicle->current_mileage >= $plan->next_execution_mileage) {
                    $needsMaintenance = true;
                    $reason = "Kilométrage atteint ({$vehicle->current_mileage} >= {$plan->next_execution_mileage})";
                }
            }

            // Check date-based
            if ($plan->frequency_type in ['date', 'both'] && $plan->next_execution_date) {
                if (now()->gte($plan->next_execution_date)) {
                    $needsMaintenance = true;
                    $reason .= ($reason ? ' et ' : '') . "Date dépassée";
                }
            }

            if ($needsMaintenance) {
                return [
                    'plan' => $plan,
                    'reason' => $reason,
                    'overdue_days' => $plan->next_execution_date ? now()->diffInDays($plan->next_execution_date) : 0,
                    'overdue_km' => $plan->next_execution_mileage ? $vehicle->current_mileage - $plan->next_execution_mileage : 0,
                ];
            }
        }

        return null;
    }
}
