@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-cogs"></i> Modifier Pièce</h2>
        <a href="{{ route('inventory-parts.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>

    <div class="card">
        <div class="card-header bg-warning text-white"><h5 class="mb-0">Informations Pièce</h5></div>
        <div class="card-body">
            <form action="{{ route('inventory-parts.update', $inventoryPart) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Référence Pièce <span class="text-danger">*</span></label>
                        <input type="text" name="part_number" class="form-control @error('part_number') is-invalid @enderror" value="{{ old('part_number', $inventoryPart->part_number) }}" required>
                        @error('part_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nom Pièce <span class="text-danger">*</span></label>
                        <input type="text" name="part_name" class="form-control @error('part_name') is-invalid @enderror" value="{{ old('part_name', $inventoryPart->part_name) }}" required>
                        @error('part_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Catégorie</label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror">
                            <option value="">Sélectionner</option>
                            <option value="Filtres" {{ old('category', $inventoryPart->category) == 'Filtres' ? 'selected' : '' }}>Filtres</option>
                            <option value="Freins" {{ old('category', $inventoryPart->category) == 'Freins' ? 'selected' : '' }}>Freins</option>
                            <option value="Huiles/Lubrifiants" {{ old('category', $inventoryPart->category) == 'Huiles/Lubrifiants' ? 'selected' : '' }}>Huiles/Lubrifiants</option>
                            <option value="Pneus" {{ old('category', $inventoryPart->category) == 'Pneus' ? 'selected' : '' }}>Pneus</option>
                            <option value="Électrique" {{ old('category', $inventoryPart->category) == 'Électrique' ? 'selected' : '' }}>Électrique</option>
                            <option value="Carrosserie" {{ old('category', $inventoryPart->category) == 'Carrosserie' ? 'selected' : '' }}>Carrosserie</option>
                            <option value="Moteur" {{ old('category', $inventoryPart->category) == 'Moteur' ? 'selected' : '' }}>Moteur</option>
                            <option value="Autre" {{ old('category', $inventoryPart->category) == 'Autre' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Fournisseur</label>
                        <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                            <option value="">Sélectionner un fournisseur</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $inventoryPart->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Quantité en Stock <span class="text-danger">*</span></label>
                        <input type="number" name="quantity_in_stock" class="form-control @error('quantity_in_stock') is-invalid @enderror" value="{{ old('quantity_in_stock', $inventoryPart->quantity_in_stock) }}" required>
                        @error('quantity_in_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Stock Minimum <span class="text-danger">*</span></label>
                        <input type="number" name="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', $inventoryPart->minimum_stock) }}" required>
                        @error('minimum_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Prix Unitaire (DH) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price', $inventoryPart->unit_price) }}" required>
                        @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Emplacement</label>
                        <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $inventoryPart->location) }}">
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Unité</label>
                        <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $inventoryPart->unit) }}">
                        @error('unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $inventoryPart->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('inventory-parts.index') }}" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
