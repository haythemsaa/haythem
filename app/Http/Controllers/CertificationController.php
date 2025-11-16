<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_employees')->only(['index', 'show', 'alerts']);
        $this->middleware('permission:create_employees')->only(['create', 'store']);
        $this->middleware('permission:edit_employees')->only(['edit', 'update']);
        $this->middleware('permission:delete_employees')->only(['destroy']);
    }

    /**
     * Display a listing of certifications
     */
    public function index(Request $request)
    {
        $query = Certification::with('employee');

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'expiring_soon') {
                $query->expiringSoon();
            } elseif ($request->status === 'valid') {
                $query->valid();
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('certification_number', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $certifications = $query->latest()->paginate(15);

        // Statistics
        $totalCertifications = Certification::count();
        $expiredCertifications = Certification::expired()->count();
        $expiringSoonCertifications = Certification::expiringSoon()->count();
        $validCertifications = Certification::valid()->count();

        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();
        $certificationTypes = $this->getCertificationTypes();

        return view('certifications.index', compact(
            'certifications',
            'employees',
            'certificationTypes',
            'totalCertifications',
            'expiredCertifications',
            'expiringSoonCertifications',
            'validCertifications'
        ));
    }

    /**
     * Show alerts for expired and expiring certifications
     */
    public function alerts()
    {
        $expiredCertifications = Certification::with('employee')
            ->expired()
            ->orderBy('expiry_date')
            ->get();

        $expiringSoonCertifications = Certification::with('employee')
            ->expiringSoon()
            ->orderBy('expiry_date')
            ->get();

        return view('certifications.alerts', compact(
            'expiredCertifications',
            'expiringSoonCertifications'
        ));
    }

    /**
     * Show the form for creating a new certification
     */
    public function create()
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $certificationTypes = $this->getCertificationTypes();

        return view('certifications.create', compact('employees', 'certificationTypes'));
    }

    /**
     * Store a newly created certification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'certification_number' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_organization' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/certifications', $fileName, 'public');
            $validated['attachment'] = $filePath;
        }

        $certification = Certification::create($validated);

        return redirect()
            ->route('certifications.show', $certification)
            ->with('success', 'Certification enregistrée avec succès.');
    }

    /**
     * Display the specified certification
     */
    public function show(Certification $certification)
    {
        $certification->load('employee');

        return view('certifications.show', compact('certification'));
    }

    /**
     * Show the form for editing the certification
     */
    public function edit(Certification $certification)
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $certificationTypes = $this->getCertificationTypes();

        return view('certifications.edit', compact('certification', 'employees', 'certificationTypes'));
    }

    /**
     * Update the specified certification
     */
    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'certification_number' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'issuing_organization' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($certification->attachment && Storage::disk('public')->exists($certification->attachment)) {
                Storage::disk('public')->delete($certification->attachment);
            }

            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/certifications', $fileName, 'public');
            $validated['attachment'] = $filePath;
        }

        $certification->update($validated);

        return redirect()
            ->route('certifications.show', $certification)
            ->with('success', 'Certification mise à jour avec succès.');
    }

    /**
     * Remove the specified certification
     */
    public function destroy(Certification $certification)
    {
        try {
            // Delete file if exists
            if ($certification->attachment && Storage::disk('public')->exists($certification->attachment)) {
                Storage::disk('public')->delete($certification->attachment);
            }

            $certification->delete();

            return redirect()
                ->route('certifications.index')
                ->with('success', 'Certification supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette certification.');
        }
    }

    /**
     * Get certification types
     */
    private function getCertificationTypes(): array
    {
        return [
            'CACES' => 'CACES (Certificat d\'Aptitude à la Conduite En Sécurité)',
            'CACES R489' => 'CACES R489 (Chariots automoteurs)',
            'CACES R482' => 'CACES R482 (Engins de chantier)',
            'CACES R486' => 'CACES R486 (Plateformes élévatrices)',
            'ADR' => 'ADR (Transport de Matières Dangereuses)',
            'FIMO' => 'FIMO (Formation Initiale Minimale Obligatoire)',
            'FCO' => 'FCO (Formation Continue Obligatoire)',
            'AIPR' => 'AIPR (Autorisation d\'Intervention à Proximité des Réseaux)',
            'SST' => 'SST (Sauveteur Secouriste du Travail)',
            'Habilitation Électrique' => 'Habilitation Électrique',
            'Travail en Hauteur' => 'Travail en Hauteur',
            'Autre' => 'Autre',
        ];
    }
}
