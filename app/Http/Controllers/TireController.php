<?php

namespace App\Http\Controllers;

use App\Models\Tire;
use App\Models\TireRotation;
use App\Models\Vehicle;
use App\Models\Site;
use Illuminate\Http\Request;

class TireController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_vehicles')->only(['index', 'show', 'alerts']);
        $this->middleware('permission:create_vehicles')->only(['create', 'store']);
        $this->middleware('permission:edit_vehicles')->only(['edit', 'update', 'rotate']);
        $this->middleware('permission:delete_vehicles')->only(['destroy']);
    }

    /**
     * Display a listing of tires
     */
    public function index(Request $request)
    {
        $query = Tire::with(['vehicle', 'site']);

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->byVehicle($request->vehicle_id);
        }

        // Filter by site
        if ($request->filled('site_id')) {
            $query->bySite($request->site_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->byPosition($request->position);
        }

        // Filter by wear status
        if ($request->filled('wear_status')) {
            if ($request->wear_status === 'critical') {
                $query->criticalWear();
            } elseif ($request->wear_status === 'warning') {
                $query->lowWear();
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tire_code', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('size', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_number', 'like', "%{$search}%")
                         ->orWhere('brand', 'like', "%{$search}%");
                  });
            });
        }

        $tires = $query->latest()->paginate(15);

        // Statistics
        $totalTires = Tire::count();
        $installedTires = Tire::installed()->count();
        $stockTires = Tire::inStock()->count();
        $wornOutTires = Tire::wornOut()->count();
        $criticalWearTires = Tire::criticalWear()->count();
        $lowWearTires = Tire::lowWear()->count();
        $totalCost = Tire::sum('purchase_cost');
        $averageWear = Tire::installed()->avg('current_tread_depth');

        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();
        $sites = Site::select('id', 'name')->get();
        $tireStatuses = $this->getTireStatuses();
        $tirePositions = $this->getTirePositions();

        return view('tires.index', compact(
            'tires',
            'vehicles',
            'sites',
            'tireStatuses',
            'tirePositions',
            'totalTires',
            'installedTires',
            'stockTires',
            'wornOutTires',
            'criticalWearTires',
            'lowWearTires',
            'totalCost',
            'averageWear'
        ));
    }

    /**
     * Show alerts for critical tire wear
     */
    public function alerts()
    {
        $criticalWearTires = Tire::with(['vehicle', 'site'])
            ->criticalWear()
            ->orderBy('current_tread_depth')
            ->get();

        $lowWearTires = Tire::with(['vehicle', 'site'])
            ->lowWear()
            ->orderBy('current_tread_depth')
            ->get();

        return view('tires.alerts', compact('criticalWearTires', 'lowWearTires'));
    }

    /**
     * Show the form for creating a new tire
     */
    public function create()
    {
        $vehicles = Vehicle::active()
            ->select('id', 'registration_number', 'brand', 'model')
            ->get();

        $sites = Site::select('id', 'name')->get();
        $tireTypes = $this->getTireTypes();
        $tirePositions = $this->getTirePositions();
        $tireStatuses = $this->getTireStatuses();

        return view('tires.create', compact(
            'vehicles',
            'sites',
            'tireTypes',
            'tirePositions',
            'tireStatuses'
        ));
    }

    /**
     * Store a newly created tire
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'site_id' => 'required|exists:sites,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'tire_type' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'installation_date' => 'nullable|date|after_or_equal:purchase_date',
            'purchase_cost' => 'required|numeric|min:0',
            'initial_tread_depth' => 'nullable|numeric|min:0|max:20',
            'current_tread_depth' => 'nullable|numeric|min:0|max:20',
            'mileage_at_installation' => 'nullable|integer|min:0',
            'current_mileage' => 'nullable|integer|min:0',
            'status' => 'required|in:in_stock,installed,worn_out,damaged,scrapped',
            'notes' => 'nullable|string',
        ]);

        $tire = Tire::create($validated);

        return redirect()
            ->route('tires.show', $tire)
            ->with('success', 'Pneumatique enregistré avec succès.');
    }

    /**
     * Display the specified tire
     */
    public function show(Tire $tire)
    {
        $tire->load(['vehicle', 'site', 'rotations.vehicle']);

        return view('tires.show', compact('tire'));
    }

    /**
     * Show the form for editing the tire
     */
    public function edit(Tire $tire)
    {
        $vehicles = Vehicle::active()
            ->select('id', 'registration_number', 'brand', 'model')
            ->get();

        $sites = Site::select('id', 'name')->get();
        $tireTypes = $this->getTireTypes();
        $tirePositions = $this->getTirePositions();
        $tireStatuses = $this->getTireStatuses();

        return view('tires.edit', compact(
            'tire',
            'vehicles',
            'sites',
            'tireTypes',
            'tirePositions',
            'tireStatuses'
        ));
    }

    /**
     * Update the specified tire
     */
    public function update(Request $request, Tire $tire)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'site_id' => 'required|exists:sites,id',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'tire_type' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'installation_date' => 'nullable|date|after_or_equal:purchase_date',
            'purchase_cost' => 'required|numeric|min:0',
            'initial_tread_depth' => 'nullable|numeric|min:0|max:20',
            'current_tread_depth' => 'nullable|numeric|min:0|max:20',
            'mileage_at_installation' => 'nullable|integer|min:0',
            'current_mileage' => 'nullable|integer|min:0',
            'status' => 'required|in:in_stock,installed,worn_out,damaged,scrapped',
            'removal_date' => 'nullable|date',
            'removal_reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $tire->update($validated);

        return redirect()
            ->route('tires.show', $tire)
            ->with('success', 'Pneumatique mis à jour avec succès.');
    }

    /**
     * Remove the specified tire
     */
    public function destroy(Tire $tire)
    {
        try {
            $tire->delete();

            return redirect()
                ->route('tires.index')
                ->with('success', 'Pneumatique supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer ce pneumatique.');
        }
    }

    /**
     * Show tire rotation form
     */
    public function rotate(Tire $tire)
    {
        $tirePositions = $this->getTirePositions();

        return view('tires.rotate', compact('tire', 'tirePositions'));
    }

    /**
     * Store tire rotation
     */
    public function storeRotation(Request $request, Tire $tire)
    {
        $validated = $request->validate([
            'rotation_date' => 'required|date',
            'to_position' => 'required|string|max:255',
            'mileage' => 'required|integer|min:0',
            'tread_depth_after' => 'required|numeric|min:0|max:20',
            'technician' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Create rotation record
        TireRotation::create([
            'tire_id' => $tire->id,
            'vehicle_id' => $tire->vehicle_id,
            'rotation_date' => $validated['rotation_date'],
            'from_position' => $tire->position,
            'to_position' => $validated['to_position'],
            'mileage' => $validated['mileage'],
            'tread_depth_before' => $tire->current_tread_depth,
            'tread_depth_after' => $validated['tread_depth_after'],
            'technician' => $validated['technician'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update tire position and tread depth
        $tire->update([
            'position' => $validated['to_position'],
            'current_tread_depth' => $validated['tread_depth_after'],
            'current_mileage' => $validated['mileage'],
        ]);

        return redirect()
            ->route('tires.show', $tire)
            ->with('success', 'Rotation effectuée avec succès.');
    }

    /**
     * Get tire types
     */
    private function getTireTypes(): array
    {
        return [
            'Été' => 'Été',
            'Hiver' => 'Hiver',
            'Toutes Saisons' => 'Toutes Saisons',
            'Rechapé' => 'Rechapé',
            'Tout Terrain' => 'Tout Terrain',
            'Poids Lourd' => 'Poids Lourd',
        ];
    }

    /**
     * Get tire positions
     */
    private function getTirePositions(): array
    {
        return [
            'front_left' => 'Avant Gauche',
            'front_right' => 'Avant Droit',
            'rear_left' => 'Arrière Gauche',
            'rear_right' => 'Arrière Droit',
            'spare' => 'Roue de Secours',
            'stock' => 'Stock',
        ];
    }

    /**
     * Get tire statuses
     */
    private function getTireStatuses(): array
    {
        return [
            'in_stock' => 'En Stock',
            'installed' => 'Installé',
            'worn_out' => 'Usé',
            'damaged' => 'Endommagé',
            'scrapped' => 'Mis au rebut',
        ];
    }
}
