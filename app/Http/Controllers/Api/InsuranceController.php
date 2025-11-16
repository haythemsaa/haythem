<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InsuranceController extends Controller
{
    /**
     * Display a listing of insurances
     */
    public function index(): JsonResponse
    {
        $insurances = Insurance::with('vehicle', 'supplier')
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $insurances
        ]);
    }

    /**
     * Store a newly created insurance
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'policy_number' => 'required|unique:insurances',
            'broker' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'annual_premium' => 'required|numeric|min:0',
            'coverage_type' => 'nullable|string|max:255',
            'certificate' => 'nullable|string|max:255',
        ]);

        $insurance = Insurance::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Insurance created successfully',
            'data' => $insurance
        ], 201);
    }

    /**
     * Display the specified insurance
     */
    public function show(Insurance $insurance): JsonResponse
    {
        $insurance->load('vehicle', 'supplier');

        return response()->json([
            'success' => true,
            'data' => $insurance
        ]);
    }

    /**
     * Update the specified insurance
     */
    public function update(Request $request, Insurance $insurance): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'supplier_id' => 'sometimes|exists:suppliers,id',
            'policy_number' => 'sometimes|unique:insurances,policy_number,' . $insurance->id',
            'broker' => 'nullable|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'annual_premium' => 'sometimes|numeric|min:0',
            'coverage_type' => 'nullable|string|max:255',
            'certificate' => 'nullable|string|max:255',
        ]);

        $insurance->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Insurance updated successfully',
            'data' => $insurance
        ]);
    }

    /**
     * Remove the specified insurance
     */
    public function destroy(Insurance $insurance): JsonResponse
    {
        $insurance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Insurance deleted successfully'
        ]);
    }

    /**
     * Get expiring insurances
     */
    public function expiring(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);

        $insurances = Insurance::with('vehicle', 'supplier')
            ->expiringSoon($days)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $insurances
        ]);
    }

    /**
     * Get expired insurances
     */
    public function expired(): JsonResponse
    {
        $insurances = Insurance::with('vehicle', 'supplier')
            ->expired()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $insurances
        ]);
    }

    /**
     * Get insurance statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Insurance::count(),
            'active' => Insurance::active()->count(),
            'expired' => Insurance::expired()->count(),
            'expiring_soon' => Insurance::expiringSoon(30)->count(),
            'total_premium' => Insurance::sum('annual_premium'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
