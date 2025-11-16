<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFuelConsumptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\FuelConsumption::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'fuel_type_id' => ['required', 'exists:fuel_types,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'refueling_date' => ['required', 'date', 'before_or_equal:today'],
            'quantity' => ['required', 'numeric', 'min:0.01', 'max:1000'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'mileage' => ['required', 'integer', 'min:0'],
            'previous_mileage' => ['nullable', 'integer', 'min:0', 'lt:mileage'],
            'distance_covered' => ['nullable', 'integer', 'min:0'],
            'consumption_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_full_tank' => ['nullable', 'boolean'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'pump_number' => ['nullable', 'string', 'max:50'],
            'fuel_card_number' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attribute names
     */
    public function attributes(): array
    {
        return [
            'vehicle_id' => 'véhicule',
            'employee_id' => 'employé',
            'fuel_type_id' => 'type de carburant',
            'supplier_id' => 'fournisseur',
            'refueling_date' => 'date de ravitaillement',
            'quantity' => 'quantité',
            'unit_price' => 'prix unitaire',
            'total_amount' => 'montant total',
            'mileage' => 'kilométrage',
            'previous_mileage' => 'kilométrage précédent',
            'distance_covered' => 'distance parcourue',
            'consumption_rate' => 'taux de consommation',
            'is_full_tank' => 'plein',
            'invoice_number' => 'numéro de facture',
            'pump_number' => 'numéro de pompe',
            'fuel_card_number' => 'numéro de carte carburant',
            'location' => 'lieu',
            'notes' => 'notes',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'previous_mileage.lt' => 'Le kilométrage précédent doit être inférieur au kilométrage actuel.',
            'quantity.max' => 'La quantité ne peut pas dépasser 1000 litres.',
            'consumption_rate.max' => 'Le taux de consommation ne peut pas dépasser 100 L/100km.',
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate that mileage is greater than or equal to vehicle's current mileage
            if ($this->vehicle_id && $this->mileage) {
                $vehicle = \App\Models\Vehicle::find($this->vehicle_id);
                if ($vehicle && $this->mileage < $vehicle->current_mileage) {
                    $validator->errors()->add(
                        'mileage',
                        'Le kilométrage doit être supérieur ou égal au kilométrage actuel du véhicule (' . $vehicle->current_mileage . ' km).'
                    );
                }
            }
        });
    }
}
