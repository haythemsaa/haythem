<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_vehicles')->only(['index', 'show', 'alerts']);
        $this->middleware('permission:create_vehicles')->only(['create', 'store']);
        $this->middleware('permission:edit_vehicles')->only(['edit', 'update']);
        $this->middleware('permission:delete_vehicles')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Contract::with('vehicle');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contracts = $query->latest('start_date')->paginate(15);

        $totalContracts = Contract::count();
        $activeContracts = Contract::active()->count();
        $expiredContracts = Contract::expired()->count();
        $expiringSoonContracts = Contract::expiringSoon()->count();
        $totalMonthlyCost = Contract::active()->sum('monthly_cost');

        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();

        return view('contracts.index', compact(
            'contracts',
            'vehicles',
            'totalContracts',
            'activeContracts',
            'expiredContracts',
            'expiringSoonContracts',
            'totalMonthlyCost'
        ));
    }

    public function alerts()
    {
        $expiredContracts = Contract::with('vehicle')->expired()->orderBy('end_date')->get();
        $expiringSoonContracts = Contract::with('vehicle')->expiringSoon()->orderBy('end_date')->get();

        return view('contracts.alerts', compact('expiredContracts', 'expiringSoonContracts'));
    }

    public function create()
    {
        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();
        return view('contracts.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'contract_type' => 'required|string|max:255',
            'contract_number' => 'required|string|max:255|unique:contracts',
            'supplier_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_cost' => 'required|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|string',
            'auto_renewal' => 'nullable|boolean',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'contract_document' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('contract_document')) {
            $file = $request->file('contract_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/contracts', $fileName, 'public');
            $validated['contract_document'] = $filePath;
        }

        $contract = Contract::create($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contrat enregistré avec succès.');
    }

    public function show(Contract $contract)
    {
        $contract->load('vehicle');
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();
        return view('contracts.edit', compact('contract', 'vehicles'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'contract_type' => 'required|string|max:255',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number,'.$contract->id,
            'supplier_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_cost' => 'required|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'payment_frequency' => 'required|string',
            'auto_renewal' => 'nullable|boolean',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'contract_document' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('contract_document')) {
            if ($contract->contract_document && Storage::disk('public')->exists($contract->contract_document)) {
                Storage::disk('public')->delete($contract->contract_document);
            }
            $file = $request->file('contract_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/contracts', $fileName, 'public');
            $validated['contract_document'] = $filePath;
        }

        $contract->update($validated);

        return redirect()->route('contracts.show', $contract)
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(Contract $contract)
    {
        try {
            if ($contract->contract_document && Storage::disk('public')->exists($contract->contract_document)) {
                Storage::disk('public')->delete($contract->contract_document);
            }
            $contract->delete();
            return redirect()->route('contracts.index')
                ->with('success', 'Contrat supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer ce contrat.');
        }
    }
}
