<?php

namespace App\Http\Controllers;

use App\Models\TrafficViolation;
use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrafficViolationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_violations')->only(['index', 'show']);
        $this->middleware('permission:create_violations')->only(['create', 'store']);
        $this->middleware('permission:edit_violations')->only(['edit', 'update']);
        $this->middleware('permission:delete_violations')->only(['destroy']);
    }

    /**
     * Display a listing of traffic violations
     */
    public function index(Request $request)
    {
        $query = TrafficViolation::with(['vehicle', 'employee']);

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by violation type
        if ($request->filled('violation_type')) {
            $query->where('violation_type', $request->violation_type);
        }

        // Filter high severity only
        if ($request->filled('high_severity_only') && $request->high_severity_only == '1') {
            $query->highSeverity();
        }

        // Filter with points only
        if ($request->filled('with_points_only') && $request->with_points_only == '1') {
            $query->withPoints();
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('location', 'like', "%{$search}%")
                  ->orWhere('violation_type', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $violations = $query->latest('violation_date')->paginate(15);

        // Statistics
        $totalViolations = TrafficViolation::count();
        $totalFines = TrafficViolation::sum('fine_amount');
        $totalAdditionalCosts = TrafficViolation::sum('additional_costs');
        $totalPoints = TrafficViolation::sum('points_deducted');
        $highSeverityViolations = TrafficViolation::highSeverity()->count();
        $withPointsViolations = TrafficViolation::withPoints()->count();

        $vehicles = Vehicle::select('id', 'registration_number')->orderBy('registration_number')->get();
        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        $violationTypes = $this->getViolationTypes();

        return view('traffic-violations.index', compact(
            'violations',
            'vehicles',
            'employees',
            'violationTypes',
            'totalViolations',
            'totalFines',
            'totalAdditionalCosts',
            'totalPoints',
            'highSeverityViolations',
            'withPointsViolations'
        ));
    }

    /**
     * Show the form for creating a new traffic violation
     */
    public function create()
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id')
            ->orderBy('registration_number')
            ->get();

        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $violationTypes = $this->getViolationTypes();

        return view('traffic-violations.create', compact(
            'vehicles',
            'employees',
            'violationTypes'
        ));
    }

    /**
     * Store a newly created traffic violation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'violation_date' => 'required|date',
            'violation_type' => 'required|string|max:255',
            'location' => 'nullable|string',
            'fine_amount' => 'nullable|numeric|min:0',
            'points_deducted' => 'nullable|integer|min:0|max:30',
            'additional_costs' => 'nullable|numeric|min:0',
            'consequences' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/violations', $fileName, 'public');
            $validated['attachment'] = $filePath;
        }

        $violation = TrafficViolation::create($validated);

        return redirect()
            ->route('traffic-violations.show', $violation)
            ->with('success', 'Infraction enregistrée avec succès.');
    }

    /**
     * Display the specified traffic violation
     */
    public function show(TrafficViolation $trafficViolation)
    {
        $trafficViolation->load([
            'vehicle.brand',
            'vehicle.vehicleModel',
            'employee'
        ]);

        return view('traffic-violations.show', compact('trafficViolation'));
    }

    /**
     * Show the form for editing the traffic violation
     */
    public function edit(TrafficViolation $trafficViolation)
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id')
            ->orderBy('registration_number')
            ->get();

        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $violationTypes = $this->getViolationTypes();

        return view('traffic-violations.edit', compact(
            'trafficViolation',
            'vehicles',
            'employees',
            'violationTypes'
        ));
    }

    /**
     * Update the specified traffic violation
     */
    public function update(Request $request, TrafficViolation $trafficViolation)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'violation_date' => 'required|date',
            'violation_type' => 'required|string|max:255',
            'location' => 'nullable|string',
            'fine_amount' => 'nullable|numeric|min:0',
            'points_deducted' => 'nullable|integer|min:0|max:30',
            'additional_costs' => 'nullable|numeric|min:0',
            'consequences' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($trafficViolation->attachment && Storage::disk('public')->exists($trafficViolation->attachment)) {
                Storage::disk('public')->delete($trafficViolation->attachment);
            }

            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/violations', $fileName, 'public');
            $validated['attachment'] = $filePath;
        }

        $trafficViolation->update($validated);

        return redirect()
            ->route('traffic-violations.show', $trafficViolation)
            ->with('success', 'Infraction mise à jour avec succès.');
    }

    /**
     * Remove the specified traffic violation
     */
    public function destroy(TrafficViolation $trafficViolation)
    {
        try {
            // Delete file if exists
            if ($trafficViolation->attachment && Storage::disk('public')->exists($trafficViolation->attachment)) {
                Storage::disk('public')->delete($trafficViolation->attachment);
            }

            $trafficViolation->delete();

            return redirect()
                ->route('traffic-violations.index')
                ->with('success', 'Infraction supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette infraction.');
        }
    }

    /**
     * Get violation types
     */
    private function getViolationTypes(): array
    {
        return [
            'speeding' => 'Excès de Vitesse',
            'red_light' => 'Feu Rouge',
            'parking' => 'Stationnement Interdit',
            'phone' => 'Téléphone au Volant',
            'seatbelt' => 'Ceinture de Sécurité',
            'drunk_driving' => 'Conduite en État d\'Ivresse',
            'dangerous_driving' => 'Conduite Dangereuse',
            'no_insurance' => 'Défaut d\'Assurance',
            'no_license' => 'Défaut de Permis',
            'overload' => 'Surcharge',
            'technical_fault' => 'Défaut Technique',
            'other' => 'Autre',
        ];
    }
}
