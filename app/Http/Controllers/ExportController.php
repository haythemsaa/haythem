<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Intervention;
use App\Models\FuelConsumption;
use App\Models\Insurance;
use App\Models\Rental;
use App\Services\ExportService;
use App\Services\TcoCalculator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExportController extends Controller
{
    protected $exportService;
    protected $tcoCalculator;

    public function __construct(ExportService $exportService, TcoCalculator $tcoCalculator)
    {
        $this->exportService = $exportService;
        $this->tcoCalculator = $tcoCalculator;
    }

    /**
     * Export vehicle report
     */
    public function vehicle(Request $request, Vehicle $vehicle)
    {
        $format = $request->input('format', 'pdf');

        return $this->exportService->exportVehicleReport($vehicle, $format);
    }

    /**
     * Export fleet summary
     */
    public function fleet(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = Vehicle::with(['brand', 'vehicleModel', 'vehicleCategory']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('vehicle_category_id', $request->category_id);
        }

        $vehicles = $query->get();

        return $this->exportService->exportFleetSummary($vehicles, $format);
    }

    /**
     * Export maintenance report
     */
    public function maintenance(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = Intervention::with(['vehicle', 'employee', 'interventionCategory', 'workOrders']);

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('request_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Vehicle filter
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $interventions = $query->latest('request_date')->get();

        return $this->exportService->exportMaintenanceReport($interventions, $format);
    }

    /**
     * Export fuel consumption report
     */
    public function fuel(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = FuelConsumption::with(['vehicle', 'fuelType', 'employee']);

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('refuel_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        // Vehicle filter
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $consumptions = $query->latest('refuel_date')->get();

        return $this->exportService->exportFuelReport($consumptions, $format);
    }

    /**
     * Export insurance report
     */
    public function insurance(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = Insurance::with(['vehicle', 'supplier']);

        // Filter by expiring soon
        if ($request->input('expiring_soon')) {
            $days = $request->input('days', 30);
            $query->where('end_date', '>=', now())
                  ->where('end_date', '<=', now()->addDays($days));
        }

        // Filter by expired
        if ($request->input('expired')) {
            $query->where('end_date', '<', now());
        }

        // Vehicle filter
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $insurances = $query->latest('end_date')->get();

        return $this->exportService->exportInsuranceReport($insurances, $format);
    }

    /**
     * Export rental revenue report
     */
    public function rental(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = Rental::with(['vehicle', 'customer']);

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Vehicle filter
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $rentals = $query->latest('start_date')->get();

        return $this->exportService->exportRentalReport($rentals, $format);
    }

    /**
     * Export TCO report
     */
    public function tco(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'format' => 'required|in:pdf,csv',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $tcoData = $this->tcoCalculator->calculate($vehicle, $startDate, $endDate);

        return $this->exportService->exportTcoReport($tcoData, $request->format);
    }

    /**
     * Export accidents report
     */
    public function accidents(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = \App\Models\Accident::with(['vehicle', 'employee']);

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('accident_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        // Severity filter
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $accidents = $query->latest('accident_date')->get();

        $data = [
            'accidents' => $accidents,
            'total_cost' => $accidents->sum('estimated_cost'),
            'total_injuries' => $accidents->sum('injured_count'),
            'stats' => [
                'total' => $accidents->count(),
                'minor' => $accidents->where('severity', 'mineure')->count(),
                'serious' => $accidents->where('severity', 'serieuse')->count(),
                'critical' => $accidents->where('severity', 'critique')->count(),
            ],
        ];

        if ($format === 'pdf') {
            return $this->exportService->exportToPdf('exports.accidents-report', $data,
                'accidents-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return $this->exportService->collectionToCsv($accidents,
            'accidents-report-' . now()->format('Y-m-d') . '.csv');
    }

    /**
     * Export traffic violations report
     */
    public function violations(Request $request)
    {
        $format = $request->input('format', 'pdf');

        $query = \App\Models\TrafficViolation::with(['vehicle', 'employee']);

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('violation_date', [
                Carbon::parse($request->start_date),
                Carbon::parse($request->end_date)
            ]);
        }

        $violations = $query->latest('violation_date')->get();

        $data = [
            'violations' => $violations,
            'total_fines' => $violations->sum('fine_amount'),
            'total_points' => $violations->sum('points_deducted'),
        ];

        if ($format === 'pdf') {
            return $this->exportService->exportToPdf('exports.violations-report', $data,
                'violations-report-' . now()->format('Y-m-d') . '.pdf');
        }

        return $this->exportService->collectionToCsv($violations,
            'violations-report-' . now()->format('Y-m-d') . '.csv');
    }
}
