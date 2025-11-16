<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchVehicleController extends Controller
{
    /**
     * Display batch operations interface
     */
    public function index()
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])->get();

        return view('vehicles.batch', compact('vehicles'));
    }

    /**
     * Perform batch status update
     */
    public function batchUpdateStatus(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'status' => 'required|in:disponible,en_mission,en_maintenance,en_panne,hors_service',
        ]);

        DB::beginTransaction();
        try {
            $count = Vehicle::whereIn('id', $request->vehicle_ids)
                ->update(['status' => $request->status]);

            DB::commit();

            return redirect()->back()->with('success', "$count véhicule(s) mis à jour avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Batch assign site
     */
    public function batchAssignSite(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'site_id' => 'required|exists:sites,id',
        ]);

        DB::beginTransaction();
        try {
            $count = Vehicle::whereIn('id', $request->vehicle_ids)
                ->update(['site_id' => $request->site_id]);

            DB::commit();

            return redirect()->back()->with('success', "$count véhicule(s) assigné(s) au site avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de l\'assignation: ' . $e->getMessage());
        }
    }

    /**
     * Batch export selected vehicles
     */
    public function batchExport(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'format' => 'required|in:pdf,excel,csv',
        ]);

        $vehicles = Vehicle::with(['brand', 'vehicleModel', 'category'])
            ->whereIn('id', $request->vehicle_ids)
            ->get();

        $exportService = app(\App\Services\ExportService::class);

        switch ($request->format) {
            case 'pdf':
                return $exportService->exportFleetSummaryPdf($vehicles);
            case 'excel':
                return $exportService->exportToExcel($vehicles->toArray(), 'vehicles_selection');
            case 'csv':
                return $exportService->exportToCsv($vehicles->toArray(), 'vehicles_selection');
        }
    }

    /**
     * Batch delete vehicles
     */
    public function batchDelete(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
        ]);

        DB::beginTransaction();
        try {
            $count = Vehicle::whereIn('id', $request->vehicle_ids)
                ->delete();

            DB::commit();

            return redirect()->back()->with('success', "$count véhicule(s) supprimé(s) avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Batch update custom field
     */
    public function batchUpdateField(Request $request)
    {
        $request->validate([
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'field' => 'required|string|in:color,year,fuel_tank_capacity',
            'value' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $count = Vehicle::whereIn('id', $request->vehicle_ids)
                ->update([$request->field => $request->value]);

            DB::commit();

            return redirect()->back()->with('success', "$count véhicule(s) mis à jour avec succès.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
}
