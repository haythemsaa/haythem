<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_trainings')->only(['index', 'show']);
        $this->middleware('permission:create_trainings')->only(['create', 'store']);
        $this->middleware('permission:edit_trainings')->only(['edit', 'update']);
        $this->middleware('permission:delete_trainings')->only(['destroy']);
    }

    /**
     * Display a listing of trainings
     */
    public function index(Request $request)
    {
        $query = Training::with('employee');

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->completed();
            } elseif ($request->status === 'ongoing') {
                $query->ongoing();
            } elseif ($request->status === 'upcoming') {
                $query->upcoming();
            }
        }

        // Filter by result
        if ($request->filled('result')) {
            $query->byResult($request->result);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('training_type', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $trainings = $query->latest('start_date')->paginate(15);

        // Statistics
        $totalTrainings = Training::count();
        $completedTrainings = Training::completed()->count();
        $ongoingTrainings = Training::ongoing()->count();
        $upcomingTrainings = Training::upcoming()->count();
        $totalCost = Training::sum('cost');
        $totalHours = Training::sum('duration_hours');
        $successfulTrainings = Training::where('result', 'like', '%reussi%')
            ->orWhere('result', 'like', '%passed%')
            ->orWhere('result', 'like', '%success%')
            ->count();

        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        $trainingTypes = $this->getTrainingTypes();
        $results = $this->getResults();

        return view('trainings.index', compact(
            'trainings',
            'employees',
            'trainingTypes',
            'results',
            'totalTrainings',
            'completedTrainings',
            'ongoingTrainings',
            'upcomingTrainings',
            'totalCost',
            'totalHours',
            'successfulTrainings'
        ));
    }

    /**
     * Show the form for creating a new training
     */
    public function create()
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $trainingTypes = $this->getTrainingTypes();
        $results = $this->getResults();

        return view('trainings.create', compact('employees', 'trainingTypes', 'results'));
    }

    /**
     * Store a newly created training
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'training_type' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_hours' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'result' => 'nullable|string|max:255',
            'evaluation' => 'nullable|string',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/trainings', $fileName, 'public');
            $validated['certificate'] = $filePath;
        }

        $training = Training::create($validated);

        return redirect()
            ->route('trainings.show', $training)
            ->with('success', 'Formation enregistrée avec succès.');
    }

    /**
     * Display the specified training
     */
    public function show(Training $training)
    {
        $training->load('employee');

        return view('trainings.show', compact('training'));
    }

    /**
     * Show the form for editing the training
     */
    public function edit(Training $training)
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $trainingTypes = $this->getTrainingTypes();
        $results = $this->getResults();

        return view('trainings.edit', compact('training', 'employees', 'trainingTypes', 'results'));
    }

    /**
     * Update the specified training
     */
    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'training_type' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_hours' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'result' => 'nullable|string|max:255',
            'evaluation' => 'nullable|string',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('certificate')) {
            // Delete old file if exists
            if ($training->certificate && Storage::disk('public')->exists($training->certificate)) {
                Storage::disk('public')->delete($training->certificate);
            }

            $file = $request->file('certificate');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/trainings', $fileName, 'public');
            $validated['certificate'] = $filePath;
        }

        $training->update($validated);

        return redirect()
            ->route('trainings.show', $training)
            ->with('success', 'Formation mise à jour avec succès.');
    }

    /**
     * Remove the specified training
     */
    public function destroy(Training $training)
    {
        try {
            // Delete file if exists
            if ($training->certificate && Storage::disk('public')->exists($training->certificate)) {
                Storage::disk('public')->delete($training->certificate);
            }

            $training->delete();

            return redirect()
                ->route('trainings.index')
                ->with('success', 'Formation supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette formation.');
        }
    }

    /**
     * Get training types
     */
    private function getTrainingTypes(): array
    {
        return [
            'Conduite Économique' => 'Conduite Économique',
            'Sécurité Routière' => 'Sécurité Routière',
            'Conduite Défensive' => 'Conduite Défensive',
            'Éco-Conduite' => 'Éco-Conduite',
            'Premiers Secours' => 'Premiers Secours',
            'Prévention des Risques' => 'Prévention des Risques',
            'Manutention' => 'Manutention',
            'Gestes et Postures' => 'Gestes et Postures',
            'Management' => 'Management',
            'Informatique' => 'Informatique',
            'Langues' => 'Langues',
            'Technique' => 'Technique',
            'Réglementation' => 'Réglementation',
            'Autre' => 'Autre',
        ];
    }

    /**
     * Get result options
     */
    private function getResults(): array
    {
        return [
            'Réussi' => 'Réussi',
            'Échoué' => 'Échoué',
            'En Cours' => 'En Cours',
            'Abandonné' => 'Abandonné',
        ];
    }
}
