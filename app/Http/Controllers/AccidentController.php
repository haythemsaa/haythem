<?php

namespace App\Http\Controllers;

use App\Models\Accident;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\AccidentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccidentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_accidents')->only(['index', 'show']);
        $this->middleware('permission:create_accidents')->only(['create', 'store']);
        $this->middleware('permission:edit_accidents')->only(['edit', 'update']);
        $this->middleware('permission:delete_accidents')->only(['destroy']);
        $this->middleware('permission:close_accidents')->only(['close']);
    }

    /**
     * Display a listing of accidents
     */
    public function index(Request $request)
    {
        $query = Accident::with(['vehicle', 'employee']);

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by severity
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter critical accidents only
        if ($request->filled('critical_only') && $request->critical_only == '1') {
            $query->critical();
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
                  ->orWhere('police_report_number', 'like', "%{$search}%")
                  ->orWhere('insurance_claim_number', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $accidents = $query->latest('accident_date')->paginate(15);

        // Statistics
        $totalAccidents = Accident::count();
        $criticalAccidents = Accident::critical()->count();
        $inProgressAccidents = Accident::inProgress()->count();
        $closedAccidents = Accident::closed()->count();
        $totalInjuries = Accident::sum('injured_count');
        $totalFatalities = Accident::sum('fatalities_count');
        $totalEstimatedCost = Accident::sum('estimated_cost');

        $vehicles = Vehicle::select('id', 'registration_number')->orderBy('registration_number')->get();
        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        $severities = $this->getSeverities();
        $statuses = $this->getStatuses();

        return view('accidents.index', compact(
            'accidents',
            'vehicles',
            'employees',
            'severities',
            'statuses',
            'totalAccidents',
            'criticalAccidents',
            'inProgressAccidents',
            'closedAccidents',
            'totalInjuries',
            'totalFatalities',
            'totalEstimatedCost'
        ));
    }

    /**
     * Show the form for creating a new accident
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

        $severities = $this->getSeverities();
        $statuses = $this->getStatuses();

        return view('accidents.create', compact(
            'vehicles',
            'employees',
            'severities',
            'statuses'
        ));
    }

    /**
     * Store a newly created accident
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'accident_date' => 'required|date',
            'accident_time' => 'nullable|date_format:H:i',
            'location' => 'required|string',
            'description' => 'required|string',
            'severity' => 'required|in:mineure,serieuse,grave,critique',
            'injured_count' => 'nullable|integer|min:0',
            'fatalities_count' => 'nullable|integer|min:0',
            'material_damage' => 'nullable|string',
            'third_parties' => 'nullable|string',
            'police_report_number' => 'nullable|string|max:255',
            'insurance_claim_number' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:en_cours,cloture',
        ]);

        $accident = Accident::create($validated);

        return redirect()
            ->route('accidents.show', $accident)
            ->with('success', 'Accident enregistré avec succès.');
    }

    /**
     * Display the specified accident
     */
    public function show(Accident $accident)
    {
        $accident->load([
            'vehicle.brand',
            'vehicle.vehicleModel',
            'employee',
            'accidentDocuments'
        ]);

        return view('accidents.show', compact('accident'));
    }

    /**
     * Show the form for editing the accident
     */
    public function edit(Accident $accident)
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id')
            ->orderBy('registration_number')
            ->get();

        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $severities = $this->getSeverities();
        $statuses = $this->getStatuses();

        return view('accidents.edit', compact(
            'accident',
            'vehicles',
            'employees',
            'severities',
            'statuses'
        ));
    }

    /**
     * Update the specified accident
     */
    public function update(Request $request, Accident $accident)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'accident_date' => 'required|date',
            'accident_time' => 'nullable|date_format:H:i',
            'location' => 'required|string',
            'description' => 'required|string',
            'severity' => 'required|in:mineure,serieuse,grave,critique',
            'injured_count' => 'nullable|integer|min:0',
            'fatalities_count' => 'nullable|integer|min:0',
            'material_damage' => 'nullable|string',
            'third_parties' => 'nullable|string',
            'police_report_number' => 'nullable|string|max:255',
            'insurance_claim_number' => 'nullable|string|max:255',
            'estimated_cost' => 'nullable|numeric|min:0',
            'status' => 'required|in:en_cours,cloture',
        ]);

        $accident->update($validated);

        return redirect()
            ->route('accidents.show', $accident)
            ->with('success', 'Accident mis à jour avec succès.');
    }

    /**
     * Remove the specified accident
     */
    public function destroy(Accident $accident)
    {
        try {
            // Delete associated documents and files
            foreach ($accident->accidentDocuments as $document) {
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }
                $document->delete();
            }

            $accident->delete();

            return redirect()
                ->route('accidents.index')
                ->with('success', 'Accident supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cet accident.');
        }
    }

    /**
     * Close an accident
     */
    public function close(Accident $accident)
    {
        $accident->update(['status' => 'cloture']);

        return redirect()
            ->route('accidents.show', $accident)
            ->with('success', 'Accident clôturé avec succès.');
    }

    /**
     * Upload accident document
     */
    public function uploadDocument(Request $request, Accident $accident)
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/accidents', $fileName, 'public');

            AccidentDocument::create([
                'accident_id' => $accident->id,
                'document_type' => $validated['document_type'],
                'file_path' => $filePath,
                'description' => $validated['description'] ?? null,
            ]);
        }

        return back()->with('success', 'Document ajouté avec succès.');
    }

    /**
     * Delete accident document
     */
    public function deleteDocument(AccidentDocument $accidentDocument)
    {
        try {
            if ($accidentDocument->file_path && Storage::disk('public')->exists($accidentDocument->file_path)) {
                Storage::disk('public')->delete($accidentDocument->file_path);
            }

            $accidentDocument->delete();

            return back()->with('success', 'Document supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer ce document.');
        }
    }

    /**
     * Get severity options
     */
    private function getSeverities(): array
    {
        return [
            'mineure' => 'Mineure',
            'serieuse' => 'Sérieuse',
            'grave' => 'Grave',
            'critique' => 'Critique',
        ];
    }

    /**
     * Get status options
     */
    private function getStatuses(): array
    {
        return [
            'en_cours' => 'En Cours',
            'cloture' => 'Clôturé',
        ];
    }
}
