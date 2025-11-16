<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_vehicles')->only(['index', 'show']);
        $this->middleware('permission:create_vehicles')->only(['create', 'store']);
        $this->middleware('permission:edit_vehicles')->only(['edit', 'update', 'complete']);
        $this->middleware('permission:delete_vehicles')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Rental::with(['vehicle', 'employee']);

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rentals = $query->latest('start_date')->paginate(15);

        $totalRentals = Rental::count();
        $ongoingRentals = Rental::ongoing()->count();
        $completedRentals = Rental::completed()->count();
        $reservedRentals = Rental::reserved()->count();
        $totalRevenue = Rental::completed()->sum('total_cost');

        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();

        return view('rentals.index', compact(
            'rentals',
            'vehicles',
            'totalRentals',
            'ongoingRentals',
            'completedRentals',
            'reservedRentals',
            'totalRevenue'
        ));
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'available')->select('id', 'registration_number', 'brand', 'model')->get();
        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        return view('rentals.create', compact('vehicles', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'daily_rate' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $rental = Rental::create($validated);

        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Location enregistrée avec succès.');
    }

    public function show(Rental $rental)
    {
        $rental->load(['vehicle', 'employee']);
        return view('rentals.show', compact('rental'));
    }

    public function edit(Rental $rental)
    {
        $vehicles = Vehicle::active()->select('id', 'registration_number', 'brand', 'model')->get();
        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        return view('rentals.edit', compact('rental', 'vehicles', 'employees'));
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'actual_return_date' => 'nullable|date',
            'daily_rate' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $rental->update($validated);

        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Location mise à jour avec succès.');
    }

    public function destroy(Rental $rental)
    {
        try {
            $rental->delete();
            return redirect()->route('rentals.index')
                ->with('success', 'Location supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette location.');
        }
    }

    public function complete(Rental $rental)
    {
        $rental->update([
            'status' => 'completed',
            'actual_return_date' => now(),
        ]);

        return redirect()->route('rentals.show', $rental)
            ->with('success', 'Location clôturée avec succès.');
    }
}
