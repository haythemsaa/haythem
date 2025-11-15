<?php

namespace App\Http\Controllers;

use App\Models\DrivingLicense;
use App\Models\Employee;
use App\Models\TrafficViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DrivingLicenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_employees')->only(['index', 'show', 'alerts']);
        $this->middleware('permission:create_employees')->only(['create', 'store']);
        $this->middleware('permission:edit_employees')->only(['edit', 'update', 'updatePoints']);
        $this->middleware('permission:delete_employees')->only(['destroy']);
    }

    /**
     * Display a listing of driving licenses
     */
    public function index(Request $request)
    {
        $query = DrivingLicense::with('employee');

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
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

        // Filter low points
        if ($request->filled('low_points') && $request->low_points == '1') {
            $query->lowPoints();
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_number', 'like', "%{$search}%")
                  ->orWhereHas('employee', function ($eq) use ($search) {
                      $eq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $licenses = $query->latest()->paginate(15);

        // Statistics
        $totalLicenses = DrivingLicense::count();
        $expiredLicenses = DrivingLicense::expired()->count();
        $expiringSoonLicenses = DrivingLicense::expiringSoon()->count();
        $validLicenses = DrivingLicense::valid()->count();
        $lowPointsLicenses = DrivingLicense::lowPoints()->count();
        $totalPoints = DrivingLicense::sum('points');
        $averagePoints = $totalLicenses > 0 ? round($totalPoints / $totalLicenses, 1) : 0;

        $employees = Employee::active()->select('id', 'first_name', 'last_name')->get();

        return view('driving-licenses.index', compact(
            'licenses',
            'employees',
            'totalLicenses',
            'expiredLicenses',
            'expiringSoonLicenses',
            'validLicenses',
            'lowPointsLicenses',
            'averagePoints'
        ));
    }

    /**
     * Show alerts for expired and expiring licenses
     */
    public function alerts()
    {
        $expiredLicenses = DrivingLicense::with('employee')
            ->expired()
            ->orderBy('expiry_date')
            ->get();

        $expiringSoonLicenses = DrivingLicense::with('employee')
            ->expiringSoon()
            ->orderBy('expiry_date')
            ->get();

        $lowPointsLicenses = DrivingLicense::with('employee')
            ->lowPoints()
            ->orderBy('points')
            ->get();

        return view('driving-licenses.alerts', compact(
            'expiredLicenses',
            'expiringSoonLicenses',
            'lowPointsLicenses'
        ));
    }

    /**
     * Show the form for creating a new license
     */
    public function create()
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $categories = ['A', 'A1', 'B', 'C', 'D', 'E'];

        return view('driving-licenses.create', compact('employees', 'categories'));
    }

    /**
     * Store a newly created license
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'license_number' => 'required|string|unique:driving_licenses,license_number',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'categories' => 'required|array|min:1',
            'points' => 'nullable|integer|min:0|max:12',
            'restrictions' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/licenses', $fileName, 'public');
            $validated['attachment'] = $filePath;
        }

        // Default points to 12 if not provided
        if (!isset($validated['points'])) {
            $validated['points'] = 12;
        }

        $license = DrivingLicense::create($validated);

        return redirect()
            ->route('driving-licenses.show', $license)
            ->with('success', 'Permis de conduire enregistré avec succès.');
    }

    /**
     * Display the specified license
     */
    public function show(DrivingLicense $drivingLicense)
    {
        $drivingLicense->load('employee');

        // Get violations for this employee
        $violations = TrafficViolation::where('employee_id', $drivingLicense->employee_id)
            ->withPoints()
            ->latest('violation_date')
            ->limit(10)
            ->get();

        return view('driving-licenses.show', compact('drivingLicense', 'violations'));
    }

    /**
     * Show the form for editing the license
     */
    public function edit(DrivingLicense $drivingLicense)
    {
        $employees = Employee::active()
            ->select('id', 'first_name', 'last_name', 'employee_code')
            ->get();

        $categories = ['A', 'A1', 'B', 'C', 'D', 'E'];

        return view('driving-licenses.edit', compact('drivingLicense', 'employees', 'categories'));
    }

    /**
     * Update the specified license
     */
    public function update(Request $request, DrivingLicense $drivingLicense)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'license_number' => 'required|string|unique:driving_licenses,license_number,' . $drivingLicense->id,
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'categories' => 'required|array|min:1',
            'points' => 'nullable|integer|min:0|max:12',
            'restrictions' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old file if exists
            if ($drivingLicense->attachment && Storage::disk('public')->exists($drivingLicense->attachment)) {
                Storage::disk('public')->delete($drivingLicense->attachment);
            }

            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents/licenses', $fileName, 'public');
            $validated['attachment'] = $filePath;
        }

        $drivingLicense->update($validated);

        return redirect()
            ->route('driving-licenses.show', $drivingLicense)
            ->with('success', 'Permis de conduire mis à jour avec succès.');
    }

    /**
     * Update license points
     */
    public function updatePoints(Request $request, DrivingLicense $drivingLicense)
    {
        $validated = $request->validate([
            'points' => 'required|integer|min:0|max:12',
        ]);

        $drivingLicense->update($validated);

        return back()->with('success', 'Points du permis mis à jour avec succès.');
    }

    /**
     * Remove the specified license
     */
    public function destroy(DrivingLicense $drivingLicense)
    {
        try {
            // Delete file if exists
            if ($drivingLicense->attachment && Storage::disk('public')->exists($drivingLicense->attachment)) {
                Storage::disk('public')->delete($drivingLicense->attachment);
            }

            $drivingLicense->delete();

            return redirect()
                ->route('driving-licenses.index')
                ->with('success', 'Permis de conduire supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer ce permis de conduire.');
        }
    }
}
