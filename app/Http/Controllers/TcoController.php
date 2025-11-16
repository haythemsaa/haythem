<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\TcoCalculator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TcoController extends Controller
{
    protected $tcoCalculator;

    public function __construct(TcoCalculator $tcoCalculator)
    {
        $this->tcoCalculator = $tcoCalculator;
    }

    /**
     * Display TCO calculator page
     */
    public function index()
    {
        $vehicles = Vehicle::select('id', 'registration_number', 'internal_code')
            ->orderBy('registration_number')
            ->get();

        return view('tco.index', compact('vehicles'));
    }

    /**
     * Calculate TCO for a vehicle
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $tcoData = $this->tcoCalculator->calculate($vehicle, $startDate, $endDate);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tcoData
            ]);
        }

        return view('tco.result', compact('tcoData', 'vehicle'));
    }

    /**
     * Compare TCO across multiple vehicles
     */
    public function compare(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array|min:2',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $comparisons = $this->tcoCalculator->compareVehicles(
            $request->vehicle_ids,
            $startDate,
            $endDate
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $comparisons
            ]);
        }

        $vehicles = Vehicle::whereIn('id', $request->vehicle_ids)->get()->keyBy('id');

        return view('tco.compare', compact('comparisons', 'vehicles'));
    }

    /**
     * Export TCO report
     */
    public function export(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'format' => 'required|in:pdf,csv,excel',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $tcoData = $this->tcoCalculator->calculate($vehicle, $startDate, $endDate);

        // Export logic will be implemented with export functionality
        // For now, return JSON
        return response()->json([
            'success' => true,
            'message' => 'Export functionality coming soon',
            'data' => $tcoData
        ]);
    }
}
