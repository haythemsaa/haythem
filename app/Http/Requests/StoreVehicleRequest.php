<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Vehicle::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'registration_number' => ['required', 'string', 'max:50', 'unique:vehicles,registration_number'],
            'internal_code' => ['nullable', 'string', 'max:50', 'unique:vehicles,internal_code'],
            'fleet_number' => ['nullable', 'string', 'max:50'],
            'brand_id' => ['required', 'exists:brands,id'],
            'vehicle_model_id' => ['required', 'exists:vehicle_models,id'],
            'vehicle_category_id' => ['required', 'exists:vehicle_categories,id'],
            'parc_id' => ['nullable', 'exists:parcs,id'],
            'site_id' => ['nullable', 'exists:sites,id'],
            'vin' => ['nullable', 'string', 'max:17', 'unique:vehicles,vin'],
            'color' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'engine_type' => ['nullable', 'string', 'max:50'],
            'engine_power' => ['nullable', 'integer', 'min:0'],
            'fuel_capacity' => ['nullable', 'numeric', 'min:0'],
            'tire_type' => ['nullable', 'string', 'max:50'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:100'],
            'load_capacity' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['required', 'date', 'before_or_equal:today'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'acquisition_mode_id' => ['nullable', 'exists:acquisition_modes,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'current_mileage' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:disponible,en_mission,en_maintenance,en_reparation,hors_service,vendu'],
            'sale_date' => ['nullable', 'date', 'after:purchase_date'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'required_with:sale_date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'registration_number' => 'numéro d\'immatriculation',
            'internal_code' => 'code interne',
            'fleet_number' => 'numéro de flotte',
            'brand_id' => 'marque',
            'vehicle_model_id' => 'modèle',
            'vehicle_category_id' => 'catégorie',
            'parc_id' => 'parc',
            'site_id' => 'site',
            'vin' => 'numéro de châssis',
            'color' => 'couleur',
            'photo' => 'photo',
            'year' => 'année',
            'engine_type' => 'type de moteur',
            'engine_power' => 'puissance moteur',
            'fuel_capacity' => 'capacité réservoir',
            'tire_type' => 'type de pneus',
            'length' => 'longueur',
            'width' => 'largeur',
            'height' => 'hauteur',
            'seats' => 'nombre de places',
            'load_capacity' => 'capacité de charge',
            'purchase_date' => 'date d\'achat',
            'purchase_price' => 'prix d\'achat',
            'acquisition_mode_id' => 'mode d\'acquisition',
            'supplier_id' => 'fournisseur',
            'current_mileage' => 'kilométrage actuel',
            'status' => 'statut',
            'sale_date' => 'date de vente',
            'sale_price' => 'prix de vente',
            'notes' => 'notes',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'registration_number.unique' => 'Ce numéro d\'immatriculation existe déjà.',
            'internal_code.unique' => 'Ce code interne existe déjà.',
            'vin.unique' => 'Ce numéro de châssis existe déjà.',
            'sale_price.required_with' => 'Le prix de vente est requis lorsqu\'une date de vente est spécifiée.',
        ];
    }
}
