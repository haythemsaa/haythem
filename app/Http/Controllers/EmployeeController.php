<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_employees')->only(['index', 'show']);
        $this->middleware('permission:create_employees')->only(['create', 'store']);
        $this->middleware('permission:edit_employees')->only(['edit', 'update']);
        $this->middleware('permission:delete_employees')->only(['destroy']);
    }

    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $query = Employee::with(['site', 'user', 'supervisor', 'drivingLicense']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by site
        if ($request->filled('site_id')) {
            $query->where('site_id', $request->site_id);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter drivers only
        if ($request->filled('drivers_only') && $request->drivers_only == '1') {
            $query->drivers();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest()->paginate(15);

        $sites = Site::active()->get();
        $departments = Employee::select('department')
            ->distinct()
            ->whereNotNull('department')
            ->pluck('department');

        return view('employees.index', compact('employees', 'sites', 'departments'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $sites = Site::active()->get();
        $supervisors = Employee::active()
            ->select('id', 'first_name', 'last_name')
            ->get();

        $contractTypes = ['cdi', 'cdd', 'interim', 'stage', 'freelance'];
        $positions = ['conducteur', 'mecanicien', 'gestionnaire', 'responsable', 'directeur'];
        $departments = ['exploitation', 'maintenance', 'administration', 'commercial', 'direction'];

        return view('employees.create', compact(
            'sites',
            'supervisors',
            'contractTypes',
            'positions',
            'departments'
        ));
    }

    /**
     * Store a newly created employee in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_code' => 'required|string|unique:employees,employee_code',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'site_id' => 'nullable|exists:sites,id',
            'supervisor_id' => 'nullable|exists:employees,id',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:cdi,cdd,interim,stage,freelance',
            'base_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:actif,suspendu,conge,demission,licencie',
            'create_user_account' => 'nullable|boolean',
            'user_password' => 'required_if:create_user_account,1|nullable|min:8',
        ]);

        // Create user account if requested
        $userId = null;
        if ($request->create_user_account) {
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($request->user_password),
            ]);
            $userId = $user->id;

            // Assign default role
            $user->assignRole('Conducteur');
        }

        $validated['user_id'] = $userId;

        $employee = Employee::create($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employé créé avec succès.');
    }

    /**
     * Display the specified employee
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'site',
            'user',
            'supervisor',
            'subordinates',
            'drivingLicense',
            'certifications',
            'trafficViolations',
            'accidents',
            'trainings',
            'medicalCheckups',
            'vehicleAssignments.vehicle',
            'interventions',
            'transportMissions'
        ]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit(Employee $employee)
    {
        $sites = Site::active()->get();
        $supervisors = Employee::active()
            ->where('id', '!=', $employee->id)
            ->select('id', 'first_name', 'last_name')
            ->get();

        $contractTypes = ['cdi', 'cdd', 'interim', 'stage', 'freelance'];
        $positions = ['conducteur', 'mecanicien', 'gestionnaire', 'responsable', 'directeur'];
        $departments = ['exploitation', 'maintenance', 'administration', 'commercial', 'direction'];

        return view('employees.edit', compact(
            'employee',
            'sites',
            'supervisors',
            'contractTypes',
            'positions',
            'departments'
        ));
    }

    /**
     * Update the specified employee in database
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_code' => 'required|string|unique:employees,employee_code,' . $employee->id,
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'site_id' => 'nullable|exists:sites,id',
            'supervisor_id' => 'nullable|exists:employees,id',
            'hire_date' => 'nullable|date',
            'contract_type' => 'nullable|in:cdi,cdd,interim,stage,freelance',
            'base_salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:actif,suspendu,conge,demission,licencie',
        ]);

        // Prevent self-supervision
        if ($validated['supervisor_id'] == $employee->id) {
            return back()->withErrors(['supervisor_id' => 'Un employé ne peut pas être son propre superviseur.']);
        }

        $employee->update($validated);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employé mis à jour avec succès.');
    }

    /**
     * Remove the specified employee from database
     */
    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();

            return redirect()
                ->route('employees.index')
                ->with('success', 'Employé supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cet employé. Il est peut-être lié à d\'autres enregistrements.');
        }
    }
}
