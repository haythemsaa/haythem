<?php

namespace App\Http\Controllers;

use App\Models\InventoryPart;
use App\Models\Supplier;
use Illuminate\Http\Request;

class InventoryPartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view_stock')->only(['index', 'show', 'alerts']);
        $this->middleware('permission:create_stock')->only(['create', 'store']);
        $this->middleware('permission:edit_stock')->only(['edit', 'update']);
        $this->middleware('permission:delete_stock')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = InventoryPart::with('supplier');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('part_number', 'like', "%{$search}%");
            });
        }

        $parts = $query->latest()->paginate(15);

        $totalParts = InventoryPart::count();
        $lowStockParts = InventoryPart::lowStock()->count();
        $outOfStockParts = InventoryPart::outOfStock()->count();
        $totalValue = InventoryPart::selectRaw('SUM(quantity_in_stock * unit_price) as total')->value('total');

        $suppliers = Supplier::active()->select('id', 'name')->get();

        return view('inventory-parts.index', compact(
            'parts',
            'suppliers',
            'totalParts',
            'lowStockParts',
            'outOfStockParts',
            'totalValue'
        ));
    }

    public function alerts()
    {
        $lowStockParts = InventoryPart::with('supplier')->lowStock()->get();
        $outOfStockParts = InventoryPart::with('supplier')->outOfStock()->get();

        return view('inventory-parts.alerts', compact('lowStockParts', 'outOfStockParts'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->select('id', 'name')->get();
        return view('inventory-parts.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $part = InventoryPart::create($validated);

        return redirect()->route('inventory-parts.show', $part)
            ->with('success', 'Pièce ajoutée avec succès.');
    }

    public function show(InventoryPart $inventoryPart)
    {
        $inventoryPart->load('supplier');
        return view('inventory-parts.show', compact('inventoryPart'));
    }

    public function edit(InventoryPart $inventoryPart)
    {
        $suppliers = Supplier::active()->select('id', 'name')->get();
        return view('inventory-parts.edit', compact('inventoryPart', 'suppliers'));
    }

    public function update(Request $request, InventoryPart $inventoryPart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'quantity_in_stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'maximum_stock' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $inventoryPart->update($validated);

        return redirect()->route('inventory-parts.show', $inventoryPart)
            ->with('success', 'Pièce mise à jour avec succès.');
    }

    public function destroy(InventoryPart $inventoryPart)
    {
        try {
            $inventoryPart->delete();
            return redirect()->route('inventory-parts.index')
                ->with('success', 'Pièce supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Impossible de supprimer cette pièce.');
        }
    }
}
