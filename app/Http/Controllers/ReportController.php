<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Intervention;
use App\Models\FuelConsumption;
use App\Models\Accident;
use App\Models\ScheduledReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $predefinedReports = [
            [
                'name' => 'Rapport Flotte Mensuel',
                'description' => 'Vue d\'ensemble complète de la flotte',
                'icon' => 'car',
                'route' => 'export.fleet',
            ],
            [
                'name' => 'Rapport Maintenance',
                'description' => 'Historique et coûts de maintenance',
                'icon' => 'tools',
                'route' => 'export.maintenance',
            ],
            [
                'name' => 'Analyse Carburant',
                'description' => 'Consommation et coûts carburant',
                'icon' => 'gas-pump',
                'route' => 'export.fuel',
            ],
            [
                'name' => 'Rapport Assurances',
                'description' => 'État des polices d\'assurance',
                'icon' => 'shield-alt',
                'route' => 'export.insurance',
            ],
            [
                'name' => 'Rapport Accidents',
                'description' => 'Historique des sinistres',
                'icon' => 'car-crash',
                'route' => 'export.accidents',
            ],
            [
                'name' => 'Rapport TCO',
                'description' => 'Coût total de possession',
                'icon' => 'chart-line',
                'route' => 'tco.index',
            ],
        ];

        $scheduledReports = ScheduledReport::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports.index', compact('predefinedReports', 'scheduledReports'));
    }

    /**
     * Display custom report builder
     */
    public function custom()
    {
        return view('reports.custom');
    }

    /**
     * Generate custom report
     */
    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:fleet,maintenance,fuel,financial,custom',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel,csv',
            'filters' => 'sometimes|array',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        switch ($request->report_type) {
            case 'fleet':
                return $this->generateFleetReport($startDate, $endDate, $request->format, $request->filters ?? []);
            case 'maintenance':
                return $this->generateMaintenanceReport($startDate, $endDate, $request->format, $request->filters ?? []);
            case 'fuel':
                return $this->generateFuelReport($startDate, $endDate, $request->format, $request->filters ?? []);
            case 'financial':
                return $this->generateFinancialReport($startDate, $endDate, $request->format, $request->filters ?? []);
            default:
                return redirect()->back()->with('error', 'Type de rapport non supporté.');
        }
    }

    /**
     * Display scheduled reports
     */
    public function scheduled()
    {
        $scheduledReports = ScheduledReport::where('user_id', auth()->id())
            ->orWhereNull('user_id')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reports.scheduled', compact('scheduledReports'));
    }

    /**
     * Schedule a report
     */
    public function schedule(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'report_type' => 'required|in:fleet,maintenance,fuel,financial',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly',
            'format' => 'required|in:pdf,excel',
            'recipients' => 'required|array',
            'recipients.*' => 'email',
        ]);

        ScheduledReport::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'report_type' => $request->report_type,
            'frequency' => $request->frequency,
            'format' => $request->format,
            'recipients' => $request->recipients,
            'filters' => $request->filters ?? [],
            'is_active' => true,
            'next_run_at' => $this->calculateNextRun($request->frequency),
        ]);

        return redirect()->back()->with('success', 'Rapport planifié créé avec succès.');
    }

    /**
     * Generate fleet report
     */
    protected function generateFleetReport($startDate, $endDate, $format, $filters)
    {
        $query = Vehicle::with(['brand', 'vehicleModel', 'category', 'site']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['site_id'])) {
            $query->where('site_id', $filters['site_id']);
        }

        $vehicles = $query->get();

        $exportService = app(\App\Services\ExportService::class);

        if ($format === 'pdf') {
            return $exportService->exportFleetSummaryPdf($vehicles);
        } else {
            return $exportService->exportToExcel($vehicles->toArray(), 'fleet_report_' . $startDate->format('Y-m-d'));
        }
    }

    /**
     * Generate maintenance report
     */
    protected function generateMaintenanceReport($startDate, $endDate, $format, $filters)
    {
        $query = Intervention::with(['vehicle', 'workOrders'])
            ->whereBetween('request_date', [$startDate, $endDate]);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['urgency'])) {
            $query->where('urgency', $filters['urgency']);
        }

        $interventions = $query->get();

        $exportService = app(\App\Services\ExportService::class);

        $data = [
            'interventions' => $interventions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_cost' => $interventions->sum(function ($intervention) {
                return $intervention->workOrders->sum('total_cost');
            }),
            'total_interventions' => $interventions->count(),
        ];

        if ($format === 'pdf') {
            return $exportService->exportMaintenanceReport($data);
        } else {
            return $exportService->exportToExcel($data, 'maintenance_report_' . $startDate->format('Y-m-d'));
        }
    }

    /**
     * Generate fuel report
     */
    protected function generateFuelReport($startDate, $endDate, $format, $filters)
    {
        $query = FuelConsumption::with('vehicle')
            ->whereBetween('date', [$startDate, $endDate]);

        if (isset($filters['vehicle_id'])) {
            $query->where('vehicle_id', $filters['vehicle_id']);
        }

        $consumptions = $query->get();

        $exportService = app(\App\Services\ExportService::class);

        $data = [
            'consumptions' => $consumptions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_liters' => $consumptions->sum('quantity_liters'),
            'total_cost' => $consumptions->sum('total_cost'),
            'average_price' => $consumptions->avg('unit_price'),
        ];

        if ($format === 'pdf') {
            return $exportService->exportFuelReport($data);
        } else {
            return $exportService->exportToExcel($data, 'fuel_report_' . $startDate->format('Y-m-d'));
        }
    }

    /**
     * Generate financial report
     */
    protected function generateFinancialReport($startDate, $endDate, $format, $filters)
    {
        $maintenanceCosts = Intervention::whereBetween('request_date', [$startDate, $endDate])
            ->with('workOrders')
            ->get()
            ->sum(function ($intervention) {
                return $intervention->workOrders->sum('total_cost');
            });

        $fuelCosts = FuelConsumption::whereBetween('date', [$startDate, $endDate])
            ->sum('total_cost');

        $accidentCosts = Accident::whereBetween('accident_date', [$startDate, $endDate])
            ->sum('estimated_cost');

        $data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'maintenance_costs' => $maintenanceCosts,
            'fuel_costs' => $fuelCosts,
            'accident_costs' => $accidentCosts,
            'total_costs' => $maintenanceCosts + $fuelCosts + $accidentCosts,
        ];

        $exportService = app(\App\Services\ExportService::class);

        if ($format === 'pdf') {
            return view('exports.financial-report', compact('data'));
        } else {
            return $exportService->exportToExcel($data, 'financial_report_' . $startDate->format('Y-m-d'));
        }
    }

    /**
     * Calculate next run date based on frequency
     */
    protected function calculateNextRun($frequency)
    {
        return match ($frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            default => now()->addMonth(),
        };
    }
}
