<?php

namespace App\Services;

use App\Models\Vehicle;
use Carbon\Carbon;

class TcoCalculator
{
    /**
     * Calculate Total Cost of Ownership for a vehicle
     */
    public function calculate(Vehicle $vehicle, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? $vehicle->purchase_date ?? now()->subYear();
        $endDate = $endDate ?? now();

        // Calculate days in period
        $daysInPeriod = $startDate->diffInDays($endDate);
        $monthsInPeriod = $startDate->diffInMonths($endDate);

        // 1. Acquisition Costs
        $acquisitionCosts = $this->calculateAcquisitionCosts($vehicle);

        // 2. Depreciation
        $depreciation = $this->calculateDepreciation($vehicle, $startDate, $endDate);

        // 3. Fuel Costs
        $fuelCosts = $this->calculateFuelCosts($vehicle, $startDate, $endDate);

        // 4. Maintenance Costs
        $maintenanceCosts = $this->calculateMaintenanceCosts($vehicle, $startDate, $endDate);

        // 5. Insurance Costs
        $insuranceCosts = $this->calculateInsuranceCosts($vehicle, $startDate, $endDate);

        // 6. Tax and Registration
        $taxCosts = $this->calculateTaxCosts($vehicle, $monthsInPeriod);

        // 7. Accident Costs
        $accidentCosts = $this->calculateAccidentCosts($vehicle, $startDate, $endDate);

        // 8. Rental/Leasing Costs
        $rentalCosts = $this->calculateRentalCosts($vehicle, $startDate, $endDate);

        // Calculate totals
        $totalCost = $acquisitionCosts + $depreciation + $fuelCosts + $maintenanceCosts
                    + $insuranceCosts + $taxCosts + $accidentCosts + $rentalCosts;

        // Calculate per-day and per-km costs
        $mileageInPeriod = $this->calculateMileageInPeriod($vehicle, $startDate, $endDate);
        $costPerDay = $daysInPeriod > 0 ? $totalCost / $daysInPeriod : 0;
        $costPerKm = $mileageInPeriod > 0 ? $totalCost / $mileageInPeriod : 0;

        return [
            'vehicle_id' => $vehicle->id,
            'vehicle_registration' => $vehicle->registration_number,
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'days' => $daysInPeriod,
                'months' => $monthsInPeriod,
            ],
            'costs' => [
                'acquisition' => round($acquisitionCosts, 2),
                'depreciation' => round($depreciation, 2),
                'fuel' => round($fuelCosts, 2),
                'maintenance' => round($maintenanceCosts, 2),
                'insurance' => round($insuranceCosts, 2),
                'tax' => round($taxCosts, 2),
                'accidents' => round($accidentCosts, 2),
                'rental_leasing' => round($rentalCosts, 2),
            ],
            'total_cost' => round($totalCost, 2),
            'metrics' => [
                'mileage_in_period' => $mileageInPeriod,
                'cost_per_day' => round($costPerDay, 2),
                'cost_per_km' => round($costPerKm, 2),
                'cost_per_month' => round($costPerDay * 30, 2),
            ],
            'breakdown_percentage' => [
                'acquisition' => $totalCost > 0 ? round(($acquisitionCosts / $totalCost) * 100, 1) : 0,
                'depreciation' => $totalCost > 0 ? round(($depreciation / $totalCost) * 100, 1) : 0,
                'fuel' => $totalCost > 0 ? round(($fuelCosts / $totalCost) * 100, 1) : 0,
                'maintenance' => $totalCost > 0 ? round(($maintenanceCosts / $totalCost) * 100, 1) : 0,
                'insurance' => $totalCost > 0 ? round(($insuranceCosts / $totalCost) * 100, 1) : 0,
                'tax' => $totalCost > 0 ? round(($taxCosts / $totalCost) * 100, 1) : 0,
                'accidents' => $totalCost > 0 ? round(($accidentCosts / $totalCost) * 100, 1) : 0,
                'rental_leasing' => $totalCost > 0 ? round(($rentalCosts / $totalCost) * 100, 1) : 0,
            ],
        ];
    }

    protected function calculateAcquisitionCosts(Vehicle $vehicle): float
    {
        return $vehicle->purchase_price ?? 0;
    }

    protected function calculateDepreciation(Vehicle $vehicle, Carbon $startDate, Carbon $endDate): float
    {
        if (!$vehicle->purchase_price || !$vehicle->purchase_date) {
            return 0;
        }

        // Assume 20% annual depreciation rate
        $annualDepreciationRate = 0.20;
        $vehicleAge = $vehicle->purchase_date->diffInYears(now());
        $monthsInPeriod = $startDate->diffInMonths($endDate);

        // Calculate depreciation for the period
        $currentValue = $vehicle->purchase_price * pow((1 - $annualDepreciationRate), $vehicleAge);
        $depreciation = ($currentValue * $annualDepreciationRate * $monthsInPeriod) / 12;

        return max(0, $depreciation);
    }

    protected function calculateFuelCosts(Vehicle $vehicle, Carbon $startDate, Carbon $endDate): float
    {
        return $vehicle->fuelConsumptions()
            ->whereBetween('refuel_date', [$startDate, $endDate])
            ->sum('total_cost') ?? 0;
    }

    protected function calculateMaintenanceCosts(Vehicle $vehicle, Carbon $startDate, Carbon $endDate): float
    {
        // Get work orders for the period
        $maintenanceCost = $vehicle->interventions()
            ->whereBetween('request_date', [$startDate, $endDate])
            ->with('workOrders')
            ->get()
            ->flatMap->workOrders
            ->sum('total_cost') ?? 0;

        return $maintenanceCost;
    }

    protected function calculateInsuranceCosts(Vehicle $vehicle, Carbon $startDate, Carbon $endDate): float
    {
        $insurances = $vehicle->insurances()
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->get();

        $totalCost = 0;
        foreach ($insurances as $insurance) {
            // Calculate pro-rata cost for the period
            $insuranceStart = max($insurance->start_date, $startDate);
            $insuranceEnd = min($insurance->end_date, $endDate);
            $daysInPeriod = $insuranceStart->diffInDays($insuranceEnd);
            $totalInsuranceDays = $insurance->start_date->diffInDays($insurance->end_date);

            if ($totalInsuranceDays > 0) {
                $totalCost += ($insurance->annual_premium * $daysInPeriod) / $totalInsuranceDays;
            }
        }

        return $totalCost;
    }

    protected function calculateTaxCosts(Vehicle $vehicle, int $monthsInPeriod): float
    {
        // Estimate annual tax (can be customized)
        $annualTax = setting('fleet.annual_vehicle_tax', 1000);
        return ($annualTax * $monthsInPeriod) / 12;
    }

    protected function calculateAccidentCosts(Vehicle $vehicle, Carbon $startDate, Carbon $endDate): float
    {
        return $vehicle->accidents()
            ->whereBetween('accident_date', [$startDate, $endDate])
            ->sum('estimated_cost') ?? 0;
    }

    protected function calculateRentalCosts(Vehicle $vehicle, Carbon $startDate, Carbon $endDate): float
    {
        // Get acquisition contracts (LLD, Leasing)
        $contracts = $vehicle->acquisitionContracts()
            ->where('status', 'actif')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                    });
            })
            ->get();

        $totalCost = 0;
        foreach ($contracts as $contract) {
            $contractStart = max($contract->start_date, $startDate);
            $contractEnd = min($contract->end_date, $endDate);
            $monthsInPeriod = $contractStart->diffInMonths($contractEnd);

            $totalCost += $contract->monthly_payment * $monthsInPeriod;
        }

        return $totalCost;
    }

    protected function calculateMileageInPeriod(Vehicle $vehicle, Carbon $startDate, Carbon $endDate): int
    {
        // Try to get mileage from fuel consumptions in the period
        $firstReading = $vehicle->fuelConsumptions()
            ->where('refuel_date', '>=', $startDate)
            ->orderBy('refuel_date', 'asc')
            ->first();

        $lastReading = $vehicle->fuelConsumptions()
            ->where('refuel_date', '<=', $endDate)
            ->orderBy('refuel_date', 'desc')
            ->first();

        if ($firstReading && $lastReading) {
            return max(0, $lastReading->mileage - $firstReading->mileage);
        }

        // Fallback: estimate based on vehicle's current mileage and age
        $vehicleAge = $vehicle->purchase_date ? $vehicle->purchase_date->diffInDays(now()) : 365;
        $daysInPeriod = $startDate->diffInDays($endDate);

        if ($vehicleAge > 0) {
            $avgDailyMileage = $vehicle->current_mileage / $vehicleAge;
            return (int) round($avgDailyMileage * $daysInPeriod);
        }

        return 0;
    }

    /**
     * Compare TCO across multiple vehicles
     */
    public function compareVehicles(array $vehicleIds, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $vehicles = Vehicle::whereIn('id', $vehicleIds)->get();
        $comparisons = [];

        foreach ($vehicles as $vehicle) {
            $comparisons[] = $this->calculate($vehicle, $startDate, $endDate);
        }

        return $comparisons;
    }
}
