<?php

namespace App\Http\Controllers;

use App\Models\VehicleDocument;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_documents')->only(['index', 'show', 'alerts']);
        $this->middleware('permission:create_documents')->only(['create', 'store']);
        $this->middleware('permission:edit_documents')->only(['edit', 'update']);
        $this->middleware('permission:delete_documents')->only(['destroy']);
    }

    /**
     * Display a listing of vehicle documents
     */
    public function index(Request $request)
    {
        $query = VehicleDocument::with('vehicle');

        // Filter by vehicle
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'expiring_soon') {
                $query->expiringSoon(30);
            } elseif ($request->status === 'valid') {
                $query->valid();
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_number', 'like', "%{$search}%")
                  ->orWhere('issuing_authority', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function ($vq) use ($search) {
                      $vq->where('registration_number', 'like', "%{$search}%");
                  });
            });
        }

        $documents = $query->latest('expiry_date')->paginate(15);

        // Statistics
        $totalDocuments = VehicleDocument::count();
        $expiredDocuments = VehicleDocument::expired()->count();
        $expiringSoonDocuments = VehicleDocument::expiringSoon(30)->count();
        $validDocuments = VehicleDocument::valid()->count();

        $vehicles = Vehicle::select('id', 'registration_number')->orderBy('registration_number')->get();
        $documentTypes = $this->getDocumentTypes();

        return view('vehicle-documents.index', compact(
            'documents',
            'vehicles',
            'documentTypes',
            'totalDocuments',
            'expiredDocuments',
            'expiringSoonDocuments',
            'validDocuments'
        ));
    }

    /**
     * Show the form for creating a new document
     */
    public function create(Request $request)
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id')
            ->orderBy('registration_number')
            ->get();

        $documentTypes = $this->getDocumentTypes();
        $selectedVehicleId = $request->vehicle_id;

        return view('vehicle-documents.create', compact('vehicles', 'documentTypes', 'selectedVehicleId'));
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'document_type' => 'required|string|max:50',
            'document_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_valid' => 'nullable|boolean',
            'reminder_days' => 'nullable|integer|min:1|max:365',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $validated['is_valid'] = $request->has('is_valid');

        // Handle file upload
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/vehicles', $fileName, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $document = VehicleDocument::create($validated);

        return redirect()
            ->route('vehicle-documents.index', ['vehicle_id' => $validated['vehicle_id']])
            ->with('success', 'Document ajouté avec succès.');
    }

    /**
     * Display the specified document
     */
    public function show(VehicleDocument $vehicleDocument)
    {
        $vehicleDocument->load(['vehicle.brand', 'vehicle.vehicleModel']);

        return view('vehicle-documents.show', compact('vehicleDocument'));
    }

    /**
     * Show the form for editing the document
     */
    public function edit(VehicleDocument $vehicleDocument)
    {
        $vehicles = Vehicle::with(['brand', 'vehicleModel'])
            ->select('id', 'registration_number', 'internal_code', 'brand_id', 'model_id')
            ->orderBy('registration_number')
            ->get();

        $documentTypes = $this->getDocumentTypes();

        return view('vehicle-documents.edit', compact('vehicleDocument', 'vehicles', 'documentTypes'));
    }

    /**
     * Update the specified document
     */
    public function update(Request $request, VehicleDocument $vehicleDocument)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'document_type' => 'required|string|max:50',
            'document_number' => 'nullable|string|max:255',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_authority' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_valid' => 'nullable|boolean',
            'reminder_days' => 'nullable|integer|min:1|max:365',
            'document_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $validated['is_valid'] = $request->has('is_valid');

        // Handle file upload
        if ($request->hasFile('document_file')) {
            // Delete old file if exists
            if ($vehicleDocument->file_path && Storage::disk('public')->exists($vehicleDocument->file_path)) {
                Storage::disk('public')->delete($vehicleDocument->file_path);
            }

            $file = $request->file('document_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/vehicles', $fileName, 'public');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $vehicleDocument->update($validated);

        return redirect()
            ->route('vehicle-documents.show', $vehicleDocument)
            ->with('success', 'Document mis à jour avec succès.');
    }

    /**
     * Remove the specified document
     */
    public function destroy(VehicleDocument $vehicleDocument)
    {
        try {
            // Delete file if exists
            if ($vehicleDocument->file_path && Storage::disk('public')->exists($vehicleDocument->file_path)) {
                Storage::disk('public')->delete($vehicleDocument->file_path);
            }

            $vehicleDocument->delete();

            return redirect()
                ->route('vehicle-documents.index')
                ->with('success', 'Document supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer ce document.');
        }
    }

    /**
     * Show expiration alerts
     */
    public function alerts()
    {
        $expiredDocuments = VehicleDocument::with('vehicle')
            ->expired()
            ->orderBy('expiry_date')
            ->get();

        $expiringSoonDocuments = VehicleDocument::with('vehicle')
            ->expiringSoon(30)
            ->orderBy('expiry_date')
            ->get();

        return view('vehicle-documents.alerts', compact('expiredDocuments', 'expiringSoonDocuments'));
    }

    /**
     * Download document file
     */
    public function download(VehicleDocument $vehicleDocument)
    {
        if (!$vehicleDocument->file_path || !Storage::disk('public')->exists($vehicleDocument->file_path)) {
            return back()->with('error', 'Fichier introuvable.');
        }

        return Storage::disk('public')->download(
            $vehicleDocument->file_path,
            $vehicleDocument->file_name
        );
    }

    /**
     * Get document types
     */
    private function getDocumentTypes(): array
    {
        return [
            'insurance' => 'Assurance',
            'registration' => 'Carte Grise',
            'technical_control' => 'Contrôle Technique',
            'authorization' => 'Autorisation de Circulation',
            'pollution_control' => 'Contrôle Anti-Pollution',
            'contract' => 'Contrat',
            'lease' => 'Leasing',
            'other' => 'Autre',
        ];
    }
}
