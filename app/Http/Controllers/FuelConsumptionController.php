<?php

namespace App\Http\Controllers;

use App\Models\FuelConsumption;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\FuelType;
use App\Models\Supplier;
use Illuminate\Http\Request;

class FuelConsumptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_fuel')->only(['index', 'show', 'analytics']);
        $this->middleware('permission:create_fuel')->only(['create', 'store']);
        $this->middleware('permission:edit_fuel')->only(['edit', 'update']);
        $this->middleware('permission:delete_fuel')->only(['destroy']);
    }

    /**
     * Display a listing of fuel consumptions
     */
    public function index(Request $request)
    {
        $query = FuelConsumption::with(['vehicle', 'employee', 'fuelType', 'supplier']);

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by fuel type
        if ($request->filled('fuel_type_id')) {
            $query->where('fuel_type_id', $request->fuel_type_id);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->betweenDates($request->start_date, $request->end_date);
        }

        // Filter full tank only
        if ($request->filled('full_tank_only') && $request->full_tank_only == '1') {
            $query->fullTankOnly();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_number', 'like', "%{$search}%");
                  });
            });
        }

        $fuelConsumptions = $query->latest('refueling_date')->paginate(15);

        // Calculate statistics
        $totalSpent = FuelConsumption::sum('total_amount');
        $totalLiters = FuelConsumption::sum('quantity');
        $totalRefuelings = FuelConsumption::count();
        $avgConsumption = FuelConsumption::fullTankOnly()->avg('consumption_rate');

        $vehicles = Vehicle::select('id', 'registration_number')->orderBy('registration_number')->get();
        $fuelTypes = FuelType::active()->get();
        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();

        return view('fuel-consumptions.index', compact(
            'fuelConsumptions',
            'vehicles',
            'fuelTypes',
            'employees',
            'totalSpent',
            'totalLiters',
            'totalRefuelings',
            'avgConsumption'
        ));
    }

    /**
     * Show the form for creating a new fuel consumption
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

        $fuelTypes = FuelType::active()->get();
        $suppliers = Supplier::active()->get();

        return view('fuel-consumptions.create', compact(
            'vehicles',
            'employees',
            'fuelTypes',
            'suppliers'
        ));
    }

    /**
     * Store a newly created fuel consumption
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'refueling_date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0.01',
            'total_amount' => 'nullable|numeric|min:0',
            'mileage' => 'required|integer|min:0',
            'previous_mileage' => 'nullable|integer|min:0',
            'is_full_tank' => 'nullable|boolean',
            'invoice_number' => 'nullable|string|max:255',
            'pump_number' => 'nullable|string|max:50',
            'fuel_card_number' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Get previous mileage if not provided
        if (!isset($validated['previous_mileage'])) {
            $lastFueling = FuelConsumption::where('vehicle_id', $validated['vehicle_id'])
                ->orderBy('refueling_date', 'desc')
                ->first();

            $validated['previous_mileage'] = $lastFueling ? $lastFueling->mileage : 0;
        }

        $validated['is_full_tank'] = $request->has('is_full_tank');

        $fuelConsumption = FuelConsumption::create($validated);

        // Update vehicle mileage
        Vehicle::where('id', $validated['vehicle_id'])
            ->update(['current_mileage' => $validated['mileage']]);

        return redirect()
            ->route('fuel-consumptions.index')
            ->with('success', 'Ravitaillement enregistré avec succès.');
    }

    /**
     * Display the specified fuel consumption
     */
    public function show(FuelConsumption $fuelConsumption)
    {
        $fuelConsumption->load([
            'vehicle.brand',
            'vehicle.vehicleModel',
            'employee',
            'fuelType',
            'supplier'
        ]);

        return view('fuel-consumptions.show', compact('fuelConsumption'));
    }

    /**
     * Show the form for editing the fuel consumption
     */
    public function edit(FuelConsumption $fuelConsumption)
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id', 'current_mileage')
            ->orderBy('registration_number')
            ->get();

        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $fuelTypes = FuelType::active()->get();
        $suppliers = Supplier::active()->get();

        return view('fuel-consumptions.edit', compact(
            'fuelConsumption',
            'vehicles',
            'employees',
            'fuelTypes',
            'suppliers'
        ));
    }

    /**
     * Update the specified fuel consumption
     */
    public function update(Request $request, FuelConsumption $fuelConsumption)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'required|exists:employees,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'refueling_date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'unit_price' => 'required|numeric|min:0.01',
            'total_amount' => 'nullable|numeric|min:0',
            'mileage' => 'required|integer|min:0',
            'previous_mileage' => 'nullable|integer|min:0',
            'is_full_tank' => 'nullable|boolean',
            'invoice_number' => 'nullable|string|max:255',
            'pump_number' => 'nullable|string|max:50',
            'fuel_card_number' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['is_full_tank'] = $request->has('is_full_tank');

        // Recalculate fields
        if ($validated['quantity'] && $validated['unit_price']) {
            $validated['total_amount'] = $validated['quantity'] * $validated['unit_price'];
        }

        if ($validated['mileage'] && $validated['previous_mileage']) {
            $validated['distance_covered'] = $validated['mileage'] - $validated['previous_mileage'];
        }

        if ($validated['is_full_tank'] && isset($validated['distance_covered']) && $validated['distance_covered'] > 0) {
            $validated['consumption_rate'] = round(
                ($validated['quantity'] / $validated['distance_covered']) * 100,
                2
            );
        }

        $fuelConsumption->update($validated);

        return redirect()
            ->route('fuel-consumptions.show', $fuelConsumption)
            ->with('success', 'Ravitaillement mis à jour avec succès.');
    }

    /**
     * Remove the specified fuel consumption
     */
    public function destroy(FuelConsumption $fuelConsumption)
    {
        try {
            $fuelConsumption->delete();

            return redirect()
                ->route('fuel-consumptions.index')
                ->with('success', 'Ravitaillement supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer ce ravitaillement.');
        }
    }

    /**
     * Show fuel consumption analytics
     */
    public function analytics(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        $query = FuelConsumption::with(['vehicle', 'fuelType']);

        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        // Last 12 months statistics
        $monthlyStats = $query->clone()
            ->selectRaw('DATE_FORMAT(refueling_date, "%Y-%m") as month')
            ->selectRaw('SUM(quantity) as total_liters')
            ->selectRaw('SUM(total_amount) as total_cost')
            ->selectRaw('AVG(consumption_rate) as avg_consumption')
            ->selectRaw('COUNT(*) as refueling_count')
            ->where('refueling_date', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $vehicles = Vehicle::select('id', 'registration_number')->orderBy('registration_number')->get();

        return view('fuel-consumptions.analytics', compact('monthlyStats', 'vehicles', 'vehicleId'));
    }
}
