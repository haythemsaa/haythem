<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Brand;
use App\Models\VehicleModel;
use App\Models\VehicleCategory;
use App\Models\Parc;
use App\Models\Site;
use App\Models\AcquisitionMode;
use App\Models\Supplier;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_vehicles')->only(['index', 'show']);
        $this->middleware('permission:create_vehicles')->only(['create', 'store']);
        $this->middleware('permission:edit_vehicles')->only(['edit', 'update']);
        $this->middleware('permission:delete_vehicles')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Vehicle::with(['brand', 'vehicleModel', 'category', 'site', 'parc']);

        // Filtres
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('site_id') && $request->site_id != '') {
            $query->where('site_id', $request->site_id);
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('vehicle_category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('internal_code', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->latest()->paginate(15);

        // Données pour les filtres
        $sites = Site::active()->orderBy('name')->get();
        $categories = VehicleCategory::orderBy('name')->get();

        return view('vehicles.index', compact('vehicles', 'sites', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::orderBy('name')->get();
        $categories = VehicleCategory::orderBy('name')->get();
        $parcs = Parc::orderBy('name')->get();
        $sites = Site::active()->orderBy('name')->get();
        $acquisitionModes = AcquisitionMode::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('vehicles.create', compact(
            'brands',
            'categories',
            'parcs',
            'sites',
            'acquisitionModes',
            'suppliers'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|unique:vehicles,registration_number',
            'internal_code' => 'required|string|unique:vehicles,internal_code',
            'fleet_number' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'parc_id' => 'nullable|exists:parcs,id',
            'site_id' => 'nullable|exists:sites,id',
            'vin' => 'nullable|string|unique:vehicles,vin',
            'color' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'engine_type' => 'nullable|string',
            'engine_power' => 'nullable|integer',
            'fuel_capacity' => 'nullable|integer',
            'tire_type' => 'nullable|string',
            'seats' => 'nullable|integer',
            'load_capacity' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric',
            'acquisition_mode_id' => 'nullable|exists:acquisition_modes,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_mileage' => 'nullable|integer|min:0',
            'status' => 'required|in:disponible,en_mission,en_maintenance,en_panne,vendu,reforme',
            'notes' => 'nullable|string',
        ]);

        $vehicle = Vehicle::create($validated);

        return redirect()
            ->route('vehicles.show', $vehicle)
            ->with('success', 'Véhicule créé avec succès!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        $vehicle->load([
            'brand',
            'vehicleModel',
            'category',
            'parc',
            'site',
            'acquisitionMode',
            'supplier',
            'assignments.employee',
            'fuelConsumptions',
            'interventions.category',
            'insurances',
            'technicalInspections'
        ]);

        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        $brands = Brand::orderBy('name')->get();
        $models = VehicleModel::where('brand_id', $vehicle->brand_id)->orderBy('name')->get();
        $categories = VehicleCategory::orderBy('name')->get();
        $parcs = Parc::orderBy('name')->get();
        $sites = Site::active()->orderBy('name')->get();
        $acquisitionModes = AcquisitionMode::orderBy('name')->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('vehicles.edit', compact(
            'vehicle',
            'brands',
            'models',
            'categories',
            'parcs',
            'sites',
            'acquisitionModes',
            'suppliers'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|unique:vehicles,registration_number,' . $vehicle->id,
            'internal_code' => 'required|string|unique:vehicles,internal_code,' . $vehicle->id,
            'fleet_number' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'vehicle_model_id' => 'required|exists:vehicle_models,id',
            'vehicle_category_id' => 'required|exists:vehicle_categories,id',
            'parc_id' => 'nullable|exists:parcs,id',
            'site_id' => 'nullable|exists:sites,id',
            'vin' => 'nullable|string|unique:vehicles,vin,' . $vehicle->id,
            'color' => 'nullable|string',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'engine_type' => 'nullable|string',
            'engine_power' => 'nullable|integer',
            'fuel_capacity' => 'nullable|integer',
            'tire_type' => 'nullable|string',
            'seats' => 'nullable|integer',
            'load_capacity' => 'nullable|numeric',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric',
            'acquisition_mode_id' => 'nullable|exists:acquisition_modes,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_mileage' => 'nullable|integer|min:0',
            'status' => 'required|in:disponible,en_mission,en_maintenance,en_panne,vendu,reforme',
            'notes' => 'nullable|string',
        ]);

        $vehicle->update($validated);

        return redirect()
            ->route('vehicles.show', $vehicle)
            ->with('success', 'Véhicule mis à jour avec succès!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Véhicule supprimé avec succès!');
    }

    /**
     * Get models by brand (for AJAX)
     */
    public function getModelsByBrand($brandId)
    {
        $models = VehicleModel::where('brand_id', $brandId)->orderBy('name')->get();
        return response()->json($models);
    }
}
