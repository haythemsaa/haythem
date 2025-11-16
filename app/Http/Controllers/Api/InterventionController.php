<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InterventionController extends Controller
{
    /**
     * Display a listing of interventions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Intervention::with(['vehicle', 'employee', 'interventionCategory', 'workOrders']);

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by urgency
        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        $interventions = $query->latest('request_date')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $interventions
        ]);
    }

    /**
     * Store a newly created intervention
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'intervention_category_id' => 'required|exists:intervention_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:preventive,curative',
            'urgency' => 'required|in:tres_urgent,urgent,normal,peut_attendre',
            'severity' => 'required|in:critique,serieuse,mineure',
            'request_date' => 'required|date',
            'mileage_at_request' => 'nullable|integer|min:0',
        ]);

        $validated['status'] = 'en_attente';

        $intervention = Intervention::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Intervention created successfully',
            'data' => $intervention
        ], 201);
    }

    /**
     * Display the specified intervention
     */
    public function show(Intervention $intervention): JsonResponse
    {
        $intervention->load(['vehicle', 'employee', 'interventionCategory', 'workOrders.technicians', 'diagnostics']);

        return response()->json([
            'success' => true,
            'data' => $intervention
        ]);
    }

    /**
     * Update the specified intervention
     */
    public function update(Request $request, Intervention $intervention): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'sometimes|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'intervention_category_id' => 'sometimes|exists:intervention_categories,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'type' => 'sometimes|in:preventive,curative',
            'urgency' => 'sometimes|in:tres_urgent,urgent,normal,peut_attendre',
            'severity' => 'sometimes|in:critique,serieuse,mineure',
            'status' => 'sometimes|in:en_attente,diagnostique,en_reparation,cloture,annule',
            'request_date' => 'sometimes|date',
            'mileage_at_request' => 'nullable|integer|min:0',
        ]);

        $intervention->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Intervention updated successfully',
            'data' => $intervention
        ]);
    }

    /**
     * Remove the specified intervention
     */
    public function destroy(Intervention $intervention): JsonResponse
    {
        $intervention->delete();

        return response()->json([
            'success' => true,
            'message' => 'Intervention deleted successfully'
        ]);
    }

    /**
     * Close an intervention
     */
    public function close(Intervention $intervention): JsonResponse
    {
        $intervention->update(['status' => 'cloture']);

        return response()->json([
            'success' => true,
            'message' => 'Intervention closed successfully',
            'data' => $intervention
        ]);
    }

    /**
     * Get pending interventions
     */
    public function pending(): JsonResponse
    {
        $interventions = Intervention::with(['vehicle', 'interventionCategory'])
            ->where('status', 'en_attente')
            ->latest('request_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $interventions
        ]);
    }

    /**
     * Get urgent interventions
     */
    public function urgent(): JsonResponse
    {
        $interventions = Intervention::with(['vehicle', 'interventionCategory'])
            ->whereIn('urgency', ['tres_urgent', 'urgent'])
            ->whereNotIn('status', ['cloture', 'annule'])
            ->latest('request_date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $interventions
        ]);
    }

    /**
     * Get intervention statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $query = Intervention::query();

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $stats = [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'en_attente')->count(),
            'in_progress' => (clone $query)->where('status', 'en_reparation')->count(),
            'completed' => (clone $query)->where('status', 'cloture')->count(),
            'urgent' => (clone $query)->whereIn('urgency', ['tres_urgent', 'urgent'])->count(),
            'preventive' => (clone $query)->where('type', 'preventive')->count(),
            'curative' => (clone $query)->where('type', 'curative')->count(),
        ];

        // Calculate total cost
        $totalCost = $query->with('workOrders')->get()
            ->flatMap->workOrders
            ->sum('total_cost');

        $stats['total_cost'] = round($totalCost, 2);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
