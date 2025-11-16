<?php

namespace App\Http\Controllers;

use App\Models\PersonalProtectiveEquipment;
use App\Models\Employee;
use Illuminate\Http\Request;

class PersonalProtectiveEquipmentController extends Controller
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
     * Display a listing of PPE
     */
    public function index(Request $request)
    {
        $query = PersonalProtectiveEquipment::with('employee');

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by equipment type
        if ($request->filled('equipment_type')) {
            $query->byType($request->equipment_type);
        }

        // Filter by condition
        if ($request->filled('condition')) {
            $query->byCondition($request->condition);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'in_use') {
                $query->inUse();
            } elseif ($request->status === 'retired') {
                $query->retired();
            } elseif ($request->status === 'lost') {
                $query->lost();
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('equipment_name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $equipment = $query->latest('issue_date')->paginate(15);

        // Statistics
        $totalEquipment = PersonalProtectiveEquipment::count();
        $inUseEquipment = PersonalProtectiveEquipment::inUse()->count();
        $retiredEquipment = PersonalProtectiveEquipment::retired()->count();
        $lostEquipment = PersonalProtectiveEquipment::lost()->count();
        $expiredEquipment = PersonalProtectiveEquipment::expired()->count();
        $expiringSoonEquipment = PersonalProtectiveEquipment::expiringSoon()->count();
        $damagedEquipment = PersonalProtectiveEquipment::damaged()->count();
        $totalCost = PersonalProtectiveEquipment::sum('cost');

        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        $equipmentTypes = $this->getEquipmentTypes();
        $conditions = $this->getConditions();
        $statuses = $this->getStatuses();

        return view('ppe.index', compact(
            'equipment',
            'employees',
            'equipmentTypes',
            'conditions',
            'statuses',
            'totalEquipment',
            'inUseEquipment',
            'retiredEquipment',
            'lostEquipment',
            'expiredEquipment',
            'expiringSoonEquipment',
            'damagedEquipment',
            'totalCost'
        ));
    }

    /**
     * Show alerts for expired and expiring PPE
     */
    public function alerts()
    {
        $expiredEquipment = PersonalProtectiveEquipment::with('employee')
            ->expired()
            ->orderBy('expiry_date')
            ->get();

        $expiringSoonEquipment = PersonalProtectiveEquipment::with('employee')
            ->expiringSoon()
            ->orderBy('expiry_date')
            ->get();

        $damagedEquipment = PersonalProtectiveEquipment::with('employee')
            ->damaged()
            ->where('status', 'in_use')
            ->latest()
            ->get();

        return view('ppe.alerts', compact(
            'expiredEquipment',
            'expiringSoonEquipment',
            'damagedEquipment'
        ));
    }

    /**
     * Show the form for creating new PPE
     */
    public function create()
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $equipmentTypes = $this->getEquipmentTypes();
        $conditions = $this->getConditions();
        $statuses = $this->getStatuses();

        return view('ppe.create', compact('employees', 'equipmentTypes', 'conditions', 'statuses'));
    }

    /**
     * Store newly created PPE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'equipment_type' => 'required|string|max:255',
            'equipment_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'replacement_frequency_months' => 'nullable|integer|min:1|max:120',
            'condition' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $ppe = PersonalProtectiveEquipment::create($validated);

        return redirect()
            ->route('ppe.show', $ppe)
            ->with('success', 'EPI enregistré avec succès.');
    }

    /**
     * Display the specified PPE
     */
    public function show(PersonalProtectiveEquipment $ppe)
    {
        $ppe->load('employee');

        return view('ppe.show', compact('ppe'));
    }

    /**
     * Show the form for editing PPE
     */
    public function edit(PersonalProtectiveEquipment $ppe)
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $equipmentTypes = $this->getEquipmentTypes();
        $conditions = $this->getConditions();
        $statuses = $this->getStatuses();

        return view('ppe.edit', compact('ppe', 'employees', 'equipmentTypes', 'conditions', 'statuses'));
    }

    /**
     * Update the specified PPE
     */
    public function update(Request $request, PersonalProtectiveEquipment $ppe)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'equipment_type' => 'required|string|max:255',
            'equipment_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:50',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'replacement_frequency_months' => 'nullable|integer|min:1|max:120',
            'condition' => 'required|string|max:255',
            'status' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $ppe->update($validated);

        return redirect()
            ->route('ppe.show', $ppe)
            ->with('success', 'EPI mis à jour avec succès.');
    }

    /**
     * Remove the specified PPE
     */
    public function destroy(PersonalProtectiveEquipment $ppe)
    {
        try {
            $ppe->delete();

            return redirect()
                ->route('ppe.index')
                ->with('success', 'EPI supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cet EPI.');
        }
    }

    /**
     * Get equipment types
     */
    private function getEquipmentTypes(): array
    {
        return [
            'Casque de Sécurité' => 'Casque de Sécurité',
            'Lunettes de Protection' => 'Lunettes de Protection',
            'Gants de Protection' => 'Gants de Protection',
            'Chaussures de Sécurité' => 'Chaussures de Sécurité',
            'Gilet Haute Visibilité' => 'Gilet Haute Visibilité',
            'Masque Respiratoire' => 'Masque Respiratoire',
            'Bouchons d\'Oreilles' => 'Bouchons d\'Oreilles',
            'Casque Anti-Bruit' => 'Casque Anti-Bruit',
            'Vêtements de Travail' => 'Vêtements de Travail',
            'Harnais de Sécurité' => 'Harnais de Sécurité',
            'Genouillères' => 'Genouillères',
            'Protection Dorsale' => 'Protection Dorsale',
            'Autre' => 'Autre',
        ];
    }

    /**
     * Get conditions
     */
    private function getConditions(): array
    {
        return [
            'new' => 'Neuf',
            'good' => 'Bon',
            'fair' => 'Acceptable',
            'worn' => 'Usé',
            'damaged' => 'Endommagé',
        ];
    }

    /**
     * Get statuses
     */
    private function getStatuses(): array
    {
        return [
            'in_use' => 'En Service',
            'retired' => 'Retiré',
            'lost' => 'Perdu',
            'replaced' => 'Remplacé',
        ];
    }
}
