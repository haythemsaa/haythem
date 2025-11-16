<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InsuranceController extends Controller
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
        $query = Insurance::with('vehicle');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $insurances = $query->latest('start_date')->paginate(15);

        $totalInsurances = Insurance::count();
        $activeInsurances = Insurance::active()->count();
        $expiredInsurances = Insurance::expired()->count();
        $expiringSoonInsurances = Insurance::expiringSoon()->count();
        $totalPremiums = Insurance::active()->sum('premium_amount');

        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();

        return view('insurances.index', compact(
            'insurances',
            'vehicles',
            'totalInsurances',
            'activeInsurances',
            'expiredInsurances',
            'expiringSoonInsurances',
            'totalPremiums'
        ));
    }

    public function alerts()
    {
        $expiredInsurances = Insurance::with('vehicle')->expired()->orderBy('end_date')->get();
        $expiringSoonInsurances = Insurance::with('vehicle')->expiringSoon()->orderBy('end_date')->get();

        return view('insurances.alerts', compact('expiredInsurances', 'expiringSoonInsurances'));
    }

    public function create()
    {
        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();
        return view('insurances.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'insurance_company' => 'required|string|max:255',
            'policy_number' => 'required|string|max:255',
            'insurance_type' => 'required|string|max:255',
            'coverage_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'premium_amount' => 'required|numeric|min:0',
            'deductible' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'policy_document' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('policy_document')) {
            $file = $request->file('policy_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/insurances', $fileName, 'public');
            $validated['policy_document'] = $filePath;
        }

        $insurance = Insurance::create($validated);

        return redirect()->route('insurances.show', $insurance)
            ->with('success', 'Assurance enregistrée avec succès.');
    }

    public function show(Insurance $insurance)
    {
        $insurance->load('vehicle');
        return view('insurances.show', compact('insurance'));
    }

    public function edit(Insurance $insurance)
    {
        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();
        return view('insurances.edit', compact('insurance', 'vehicles'));
    }

    public function update(Request $request, Insurance $insurance)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'insurance_company' => 'required|string|max:255',
            'policy_number' => 'required|string|max:255',
            'insurance_type' => 'required|string|max:255',
            'coverage_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'premium_amount' => 'required|numeric|min:0',
            'deductible' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'notes' => 'nullable|string',
            'policy_document' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('policy_document')) {
            if ($insurance->policy_document && Storage::disk('public')->exists($insurance->policy_document)) {
                Storage::disk('public')->delete($insurance->policy_document);
            }
            $file = $request->file('policy_document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/insurances', $fileName, 'public');
            $validated['policy_document'] = $filePath;
        }

        $insurance->update($validated);

        return redirect()->route('insurances.show', $insurance)
            ->with('success', 'Assurance mise à jour avec succès.');
    }

    public function destroy(Insurance $insurance)
    {
        try {
            if ($insurance->policy_document && Storage::disk('public')->exists($insurance->policy_document)) {
                Storage::disk('public')->delete($insurance->policy_document);
            }
            $insurance->delete();
            return redirect()->route('insurances.index')
                ->with('success', 'Assurance supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette assurance.');
        }
    }

    public function download(Insurance $insurance)
    {
        if (!$insurance->policy_document || !Storage::disk('public')->exists($insurance->policy_document)) {
            return back()->with('error', 'Fichier non trouvé.');
        }
        return Storage::disk('public')->download($insurance->policy_document);
    }
}
