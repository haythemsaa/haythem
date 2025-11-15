<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\InterventionCategory;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_interventions')->only(['index', 'show']);
        $this->middleware('permission:create_interventions')->only(['create', 'store']);
        $this->middleware('permission:edit_interventions')->only(['edit', 'update']);
        $this->middleware('permission:delete_interventions')->only(['destroy']);
    }

    /**
     * Display a listing of interventions
     */
    public function index(Request $request)
    {
        $query = Intervention::with(['vehicle', 'requester', 'category']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type (preventive/curative)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by urgency
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter urgent only
        if ($request->filled('urgent_only') && $request->urgent_only == '1') {
            $query->urgent();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_number', 'like', "%{$search}%");
                  });
            });
        }

        $interventions = $query->latest()->paginate(15);

        $vehicles = Vehicle::select('id', 'registration_number', 'internal_code')
            ->orderBy('registration_number')
            ->get();

        $categories = InterventionCategory::active()->get();

        return view('interventions.index', compact('interventions', 'vehicles', 'categories'));
    }

    /**
     * Show the form for creating a new intervention
     */
    public function create()
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id', 'current_mileage')
            ->orderBy('registration_number')
            ->get();

        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $categories = InterventionCategory::active()->get();

        $types = ['preventive', 'curative', 'predictive'];
        $urgencies = ['faible', 'normal', 'urgent', 'tres_urgent'];
        $severities = ['faible', 'moyen', 'critique'];

        return view('interventions.create', compact(
            'vehicles',
            'employees',
            'categories',
            'types',
            'urgencies',
            'severities'
        ));
    }

    /**
     * Store a newly created intervention
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'intervention_category_id' => 'nullable|exists:intervention_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:preventive,curative,predictive',
            'urgency' => 'required|in:faible,normal,urgent,tres_urgent',
            'severity' => 'nullable|in:faible,moyen,critique',
            'request_date' => 'required|date',
            'mileage_at_request' => 'nullable|integer|min:0',
            'status' => 'nullable|in:en_attente,diagnostique,en_reparation,cloture,annule',
        ]);

        // Auto-set mileage if not provided
        if (!$validated['mileage_at_request']) {
            $vehicle = Vehicle::find($validated['vehicle_id']);
            $validated['mileage_at_request'] = $vehicle->current_mileage ?? 0;
        }

        // Default status
        $validated['status'] = $validated['status'] ?? 'en_attente';

        $intervention = Intervention::create($validated);

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', 'Intervention créée avec succès.');
    }

    /**
     * Display the specified intervention
     */
    public function show(Intervention $intervention)
    {
        $intervention->load([
            'vehicle.brand',
            'vehicle.vehicleModel',
            'requester',
            'category',
            'diagnostic',
            'workOrders.mechanic',
            'stockOutputs.article'
        ]);

        return view('interventions.show', compact('intervention'));
    }

    /**
     * Show the form for editing the intervention
     */
    public function edit(Intervention $intervention)
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id', 'current_mileage')
            ->orderBy('registration_number')
            ->get();

        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $categories = InterventionCategory::active()->get();

        $types = ['preventive', 'curative', 'predictive'];
        $urgencies = ['faible', 'normal', 'urgent', 'tres_urgent'];
        $severities = ['faible', 'moyen', 'critique'];
        $statuses = ['en_attente', 'diagnostique', 'en_reparation', 'cloture', 'annule'];

        return view('interventions.edit', compact(
            'intervention',
            'vehicles',
            'employees',
            'categories',
            'types',
            'urgencies',
            'severities',
            'statuses'
        ));
    }

    /**
     * Update the specified intervention
     */
    public function update(Request $request, Intervention $intervention)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'intervention_category_id' => 'nullable|exists:intervention_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:preventive,curative,predictive',
            'urgency' => 'required|in:faible,normal,urgent,tres_urgent',
            'severity' => 'nullable|in:faible,moyen,critique',
            'request_date' => 'required|date',
            'mileage_at_request' => 'nullable|integer|min:0',
            'status' => 'required|in:en_attente,diagnostique,en_reparation,cloture,annule',
        ]);

        $intervention->update($validated);

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', 'Intervention mise à jour avec succès.');
    }

    /**
     * Remove the specified intervention
     */
    public function destroy(Intervention $intervention)
    {
        try {
            $intervention->delete();

            return redirect()
                ->route('interventions.index')
                ->with('success', 'Intervention supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette intervention. Elle est peut-être liée à d\'autres enregistrements.');
        }
    }

    /**
     * Mark intervention as urgent
     */
    public function markAsUrgent(Intervention $intervention)
    {
        $intervention->update(['urgency' => 'tres_urgent']);

        return back()->with('success', 'Intervention marquée comme très urgente.');
    }

    /**
     * Close intervention
     */
    public function close(Intervention $intervention)
    {
        $intervention->update(['status' => 'cloture']);

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', 'Intervention clôturée avec succès.');
    }
}
