<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with(['vehicle'])->latest()->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $rentals
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'client_name' => 'required',
            'client_phone' => 'required',
            'client_email' => 'nullable|email',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'daily_rate' => 'required|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:reserved,ongoing,completed,cancelled',
        ]);

        $rental = Rental::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rental created successfully',
            'data' => $rental
        ], 201);
    }

    public function show(Rental $rental)
    {
        return response()->json([
            'success' => true,
            'data' => $rental->load(['vehicle'])
        ]);
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'client_name' => 'sometimes|required',
            'client_phone' => 'sometimes|required',
            'status' => 'sometimes|in:reserved,ongoing,completed,cancelled',
            'actual_return_date' => 'nullable|date',
        ]);

        $rental->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rental updated successfully',
            'data' => $rental
        ]);
    }

    public function destroy(Rental $rental)
    {
        $rental->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rental deleted successfully'
        ]);
    }

    // Custom endpoints
    public function ongoing()
    {
        $rentals = Rental::ongoing()->with(['vehicle'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $rentals
        ]);
    }

    public function complete(Rental $rental)
    {
        $rental->update([
            'status' => 'completed',
            'actual_return_date' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rental marked as completed',
            'data' => $rental
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Rental::count(),
            'ongoing' => Rental::ongoing()->count(),
            'reserved' => Rental::reserved()->count(),
            'completed' => Rental::completed()->count(),
            'total_revenue' => Rental::completed()->sum('total_cost'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
