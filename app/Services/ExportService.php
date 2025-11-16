<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class ExportService
{
    /**
     * Export data to PDF
     */
    public function exportToPdf(string $view, array $data, string $filename = 'export.pdf', string $orientation = 'portrait'): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper('a4', $orientation);

        return $pdf->download($filename);
    }

    /**
     * Export data to Excel
     */
    public function exportToExcel($export, string $filename = 'export.xlsx', string $writerType = \Maatwebsite\Excel\Excel::XLSX)
    {
        return Excel::download($export, $filename, $writerType);
    }

    /**
     * Export data to CSV
     */
    public function exportToCsv($export, string $filename = 'export.csv')
    {
        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    /**
     * Export collection to CSV (simple)
     */
    public function collectionToCsv(Collection $collection, string $filename = 'export.csv', array $headers = [])
    {
        $csvData = [];

        // Add headers
        if (!empty($headers)) {
            $csvData[] = $headers;
        } else if ($collection->isNotEmpty()) {
            $csvData[] = array_keys($collection->first()->toArray());
        }

        // Add data
        foreach ($collection as $item) {
            $csvData[] = array_values($item->toArray());
        }

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate vehicle report PDF
     */
    public function exportVehicleReport($vehicle, string $format = 'pdf')
    {
        $data = [
            'vehicle' => $vehicle,
            'fuel_consumptions' => $vehicle->fuelConsumptions()->latest()->limit(10)->get(),
            'interventions' => $vehicle->interventions()->latest()->limit(10)->get(),
            'accidents' => $vehicle->accidents()->latest()->limit(5)->get(),
            'insurances' => $vehicle->insurances()->latest()->get(),
        ];

        if ($format === 'pdf') {
            return $this->exportToPdf('exports.vehicle-report', $data,
                "vehicle-report-{$vehicle->registration_number}.pdf");
        }

        // For Excel/CSV, create a custom export class would be needed
        return $this->collectionToCsv(
            collect([$vehicle]),
            "vehicle-{$vehicle->registration_number}.csv"
        );
    }

    /**
     * Generate fleet summary report
     */
    public function exportFleetSummary(Collection $vehicles, string $format = 'pdf')
    {
        $data = [
            'vehicles' => $vehicles,
            'stats' => [
                'total' => $vehicles->count(),
                'available' => $vehicles->where('status', 'disponible')->count(),
                'in_service' => $vehicles->where('status', 'en_mission')->count(),
                'maintenance' => $vehicles->where('status', 'en_maintenance')->count(),
            ],
        ];

        if ($format === 'pdf') {
            return $this->exportToPdf('exports.fleet-summary', $data,
                'fleet-summary-' . now()->format('Y-m-d') . '.pdf', 'landscape');
        }

        return $this->collectionToCsv($vehicles,
            'fleet-summary-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Generate maintenance report
     */
    public function exportMaintenanceReport(Collection $interventions, string $format = 'pdf')
    {
        $data = [
            'interventions' => $interventions,
            'total_cost' => $interventions->sum(function($intervention) {
                return $intervention->workOrders->sum('total_cost');
            }),
            'stats' => [
                'total' => $interventions->count(),
                'pending' => $interventions->where('status', 'en_attente')->count(),
                'in_progress' => $interventions->where('status', 'en_reparation')->count(),
                'completed' => $interventions->where('status', 'cloture')->count(),
            ],
        ];

        if ($format === 'pdf') {
            return $this->exportToPdf('exports.maintenance-report', $data,
                'maintenance-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return $this->collectionToCsv($interventions,
            'maintenance-report-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Generate fuel consumption report
     */
    public function exportFuelReport(Collection $consumptions, string $format = 'pdf')
    {
        $data = [
            'consumptions' => $consumptions,
            'total_cost' => $consumptions->sum('total_cost'),
            'total_quantity' => $consumptions->sum('quantity'),
            'avg_price' => $consumptions->avg('unit_price'),
        ];

        if ($format === 'pdf') {
            return $this->exportToPdf('exports.fuel-report', $data,
                'fuel-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return $this->collectionToCsv($consumptions,
            'fuel-report-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Generate insurance expiry report
     */
    public function exportInsuranceReport(Collection $insurances, string $format = 'pdf')
    {
        $data = [
            'insurances' => $insurances,
            'expiring_soon' => $insurances->filter(function($insurance) {
                return $insurance->end_date->diffInDays(now()) <= 30 && $insurance->end_date->isFuture();
            }),
            'expired' => $insurances->filter(function($insurance) {
                return $insurance->end_date->isPast();
            }),
            'total_premium' => $insurances->sum('annual_premium'),
        ];

        if ($format === 'pdf') {
            return $this->exportToPdf('exports.insurance-report', $data,
                'insurance-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return $this->collectionToCsv($insurances,
            'insurance-report-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Generate rental revenue report
     */
    public function exportRentalReport(Collection $rentals, string $format = 'pdf')
    {
        $data = [
            'rentals' => $rentals,
            'total_revenue' => $rentals->sum('total_cost'),
            'total_days' => $rentals->sum('total_days'),
            'avg_daily_rate' => $rentals->avg('daily_rate'),
            'stats' => [
                'total' => $rentals->count(),
                'reserved' => $rentals->where('status', 'reserved')->count(),
                'ongoing' => $rentals->where('status', 'ongoing')->count(),
                'completed' => $rentals->where('status', 'completed')->count(),
            ],
        ];

        if ($format === 'pdf') {
            return $this->exportToPdf('exports.rental-report', $data,
                'rental-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return $this->collectionToCsv($rentals,
            'rental-report-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Generate TCO report
     */
    public function exportTcoReport(array $tcoData, string $format = 'pdf')
    {
        $data = ['tco' => $tcoData];

        if ($format === 'pdf') {
            return $this->exportToPdf('exports.tco-report', $data,
                'tco-report-' . $tcoData['vehicle_registration'] . '.pdf');
        }

        // Convert TCO data to flat structure for CSV
        $flatData = [
            'Vehicle' => $tcoData['vehicle_registration'],
            'Period Start' => $tcoData['period']['start'],
            'Period End' => $tcoData['period']['end'],
            'Total Days' => $tcoData['period']['days'],
            'Acquisition Cost' => $tcoData['costs']['acquisition'],
            'Depreciation' => $tcoData['costs']['depreciation'],
            'Fuel Cost' => $tcoData['costs']['fuel'],
            'Maintenance Cost' => $tcoData['costs']['maintenance'],
            'Insurance Cost' => $tcoData['costs']['insurance'],
            'Tax Cost' => $tcoData['costs']['tax'],
            'Accident Cost' => $tcoData['costs']['accidents'],
            'Rental/Leasing Cost' => $tcoData['costs']['rental_leasing'],
            'Total Cost' => $tcoData['total_cost'],
            'Cost per Day' => $tcoData['metrics']['cost_per_day'],
            'Cost per KM' => $tcoData['metrics']['cost_per_km'],
        ];

        return $this->collectionToCsv(collect([$flatData]),
            'tco-report-' . $tcoData['vehicle_registration'] . '.csv',
            array_keys($flatData));
    }
}
