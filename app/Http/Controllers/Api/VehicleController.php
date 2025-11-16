<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with(['site'])->paginate(50);
        
        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'required|unique:vehicles',
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'status' => 'required|in:available,in_service,maintenance,out_of_service',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric',
            'current_mileage' => 'nullable|integer',
        ]);

        $vehicle = Vehicle::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle created successfully',
            'data' => $vehicle
        ], 201);
    }

    public function show(Vehicle $vehicle)
    {
        return response()->json([
            'success' => true,
            'data' => $vehicle->load(['site', 'documents', 'interventions', 'accidents'])
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'registration_number' => 'sometimes|required|unique:vehicles,registration_number,' . $vehicle->id,
            'brand' => 'sometimes|required',
            'model' => 'sometimes|required',
            'status' => 'sometimes|required|in:available,in_service,maintenance,out_of_service',
            'current_mileage' => 'sometimes|nullable|integer',
        ]);

        $vehicle->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vehicle updated successfully',
            'data' => $vehicle
        ]);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle deleted successfully'
        ]);
    }

    // Custom endpoints
    public function available()
    {
        $vehicles = Vehicle::available()->get();
        
        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    public function maintenance()
    {
        $vehicles = Vehicle::maintenance()->get();
        
        return response()->json([
            'success' => true,
            'data' => $vehicles
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => Vehicle::count(),
            'available' => Vehicle::available()->count(),
            'in_service' => Vehicle::inService()->count(),
            'maintenance' => Vehicle::maintenance()->count(),
            'out_of_service' => Vehicle::outOfService()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
