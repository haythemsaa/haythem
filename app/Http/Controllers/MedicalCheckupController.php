<?php

namespace App\Http\Controllers;

use App\Models\MedicalCheckup;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalCheckupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_employees')->only(['index', 'show', 'alerts']);
        $this->middleware('permission:create_employees')->only(['create', 'store']);
        $this->middleware('permission:edit_employees')->only(['edit', 'update']);
        $this->middleware('permission:delete_employees')->only(['destroy']);
    }

    /**
     * Display a listing of medical checkups
     */
    public function index(Request $request)
    {
        $query = MedicalCheckup::with('employee');

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by result
        if ($request->filled('result')) {
            $query->byResult($request->result);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'expiring_soon') {
                $query->expiringSoon();
            } elseif ($request->status === 'valid') {
                $query->valid();
            }
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('medical_center', 'like', "%{$search}%")
                  ->orWhere('doctor_name', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $checkups = $query->latest('checkup_date')->paginate(15);

        // Statistics
        $totalCheckups = MedicalCheckup::count();
        $expiredCheckups = MedicalCheckup::expired()->count();
        $expiringSoonCheckups = MedicalCheckup::expiringSoon()->count();
        $validCheckups = MedicalCheckup::valid()->count();
        $fitEmployees = MedicalCheckup::where('result', 'fit')->count();
        $unfitEmployees = MedicalCheckup::whereIn('result', ['temporarily_unfit', 'unfit'])->count();
        $withRestrictions = MedicalCheckup::withRestrictions()->count();

        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        $checkupTypes = $this->getCheckupTypes();
        $results = $this->getResults();

        return view('medical-checkups.index', compact(
            'checkups',
            'employees',
            'checkupTypes',
            'results',
            'totalCheckups',
            'expiredCheckups',
            'expiringSoonCheckups',
            'validCheckups',
            'fitEmployees',
            'unfitEmployees',
            'withRestrictions'
        ));
    }

    /**
     * Show alerts for expired and expiring medical checkups
     */
    public function alerts()
    {
        $expiredCheckups = MedicalCheckup::with('employee')
            ->expired()
            ->orderBy('next_checkup_date')
            ->get();

        $expiringSoonCheckups = MedicalCheckup::with('employee')
            ->expiringSoon()
            ->orderBy('next_checkup_date')
            ->get();

        $unfitEmployees = MedicalCheckup::with('employee')
            ->whereIn('result', ['temporarily_unfit', 'unfit'])
            ->latest('checkup_date')
            ->get();

        return view('medical-checkups.alerts', compact(
            'expiredCheckups',
            'expiringSoonCheckups',
            'unfitEmployees'
        ));
    }

    /**
     * Show the form for creating a new medical checkup
     */
    public function create()
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $checkupTypes = $this->getCheckupTypes();
        $results = $this->getResults();

        return view('medical-checkups.create', compact('employees', 'checkupTypes', 'results'));
    }

    /**
     * Store a newly created medical checkup
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'checkup_type' => 'required|string|max:255',
            'checkup_date' => 'required|date',
            'next_checkup_date' => 'nullable|date|after:checkup_date',
            'medical_center' => 'required|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'result' => 'required|string|max:255',
            'restrictions' => 'nullable|string',
            'notes' => 'nullable|string',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/medical-checkups', $fileName, 'public');
            $validated['certificate_file'] = $filePath;
        }

        $checkup = MedicalCheckup::create($validated);

        return redirect()
            ->route('medical-checkups.show', $checkup)
            ->with('success', 'Visite médicale enregistrée avec succès.');
    }

    /**
     * Display the specified medical checkup
     */
    public function show(MedicalCheckup $medicalCheckup)
    {
        $medicalCheckup->load('employee');

        return view('medical-checkups.show', compact('medicalCheckup'));
    }

    /**
     * Show the form for editing the medical checkup
     */
    public function edit(MedicalCheckup $medicalCheckup)
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $checkupTypes = $this->getCheckupTypes();
        $results = $this->getResults();

        return view('medical-checkups.edit', compact('medicalCheckup', 'employees', 'checkupTypes', 'results'));
    }

    /**
     * Update the specified medical checkup
     */
    public function update(Request $request, MedicalCheckup $medicalCheckup)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'checkup_type' => 'required|string|max:255',
            'checkup_date' => 'required|date',
            'next_checkup_date' => 'nullable|date|after:checkup_date',
            'medical_center' => 'required|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'result' => 'required|string|max:255',
            'restrictions' => 'nullable|string',
            'notes' => 'nullable|string',
            'certificate_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('certificate_file')) {
            // Delete old file if exists
            if ($medicalCheckup->certificate_file && Storage::disk('public')->exists($medicalCheckup->certificate_file)) {
                Storage::disk('public')->delete($medicalCheckup->certificate_file);
            }

            $file = $request->file('certificate_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/medical-checkups', $fileName, 'public');
            $validated['certificate_file'] = $filePath;
        }

        $medicalCheckup->update($validated);

        return redirect()
            ->route('medical-checkups.show', $medicalCheckup)
            ->with('success', 'Visite médicale mise à jour avec succès.');
    }

    /**
     * Remove the specified medical checkup
     */
    public function destroy(MedicalCheckup $medicalCheckup)
    {
        try {
            // Delete file if exists
            if ($medicalCheckup->certificate_file && Storage::disk('public')->exists($medicalCheckup->certificate_file)) {
                Storage::disk('public')->delete($medicalCheckup->certificate_file);
            }

            $medicalCheckup->delete();

            return redirect()
                ->route('medical-checkups.index')
                ->with('success', 'Visite médicale supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette visite médicale.');
        }
    }

    /**
     * Download certificate file
     */
    public function download(MedicalCheckup $medicalCheckup)
    {
        if (!$medicalCheckup->certificate_file || !Storage::disk('public')->exists($medicalCheckup->certificate_file)) {
            return back()->with('error', 'Fichier non trouvé.');
        }

        return Storage::disk('public')->download($medicalCheckup->certificate_file);
    }

    /**
     * Get checkup types
     */
    private function getCheckupTypes(): array
    {
        return [
            'initial' => 'Visite Initiale',
            'periodic' => 'Visite Périodique',
            'renewal' => 'Visite de Reprise',
            'pre_assignment' => 'Visite Pré-Affectation',
            'spontaneous' => 'Visite à la Demande',
        ];
    }

    /**
     * Get result options
     */
    private function getResults(): array
    {
        return [
            'fit' => 'Apte',
            'fit_with_restrictions' => 'Apte avec Restrictions',
            'temporarily_unfit' => 'Inapte Temporaire',
            'unfit' => 'Inapte',
        ];
    }
}
