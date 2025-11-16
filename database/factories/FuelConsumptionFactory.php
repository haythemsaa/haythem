<?php

namespace Database\Factories;

use App\Models\FuelConsumption;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\FuelType;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FuelConsumption>
 */
class FuelConsumptionFactory extends Factory
{
    protected $model = FuelConsumption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(2, 10, 100);
        $unitPrice = fake()->randomFloat(3, 10, 20); // Price per liter
        $totalAmount = $quantity * $unitPrice;

        $previousMileage = fake()->numberBetween(10000, 180000);
        $distanceCovered = fake()->numberBetween(200, 1000);
        $currentMileage = $previousMileage + $distanceCovered;

        $consumptionRate = ($quantity / $distanceCovered) * 100;

        return [
            'vehicle_id' => Vehicle::factory(),
            'employee_id' => Employee::inRandomOrder()->first()?->id,
            'fuel_type_id' => FuelType::inRandomOrder()->first()?->id ?? 1,
            'supplier_id' => Supplier::inRandomOrder()->first()?->id,
            'refueling_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'mileage' => $currentMileage,
            'previous_mileage' => $previousMileage,
            'distance_covered' => $distanceCovered,
            'consumption_rate' => round($consumptionRate, 2),
            'is_full_tank' => fake()->boolean(70), // 70% chance of full tank
            'invoice_number' => 'FAC-' . fake()->unique()->numberBetween(100000, 999999),
            'pump_number' => fake()->optional(0.6)->randomElement(['P1', 'P2', 'P3', 'P4', 'P5']),
            'fuel_card_number' => fake()->optional(0.7)->numerify('FC-####-####'),
            'location' => fake()->optional(0.8)->randomElement([
                'Station Total Casablanca',
                'Afriquia Rabat',
                'Shell Marrakech',
                'Vivo Energy Agadir',
                'Winxo Tanger',
            ]),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that this is a full tank refueling.
     */
    public function fullTank(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_full_tank' => true,
            'quantity' => fake()->randomFloat(2, 40, 80), // Larger quantity for full tank
        ]);
    }

    /**
     * Indicate that this is a partial refueling.
     */
    public function partial(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_full_tank' => false,
            'quantity' => fake()->randomFloat(2, 10, 40), // Smaller quantity
        ]);
    }

    /**
     * Indicate that this is a diesel refueling.
     */
    public function diesel(): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_price' => fake()->randomFloat(3, 12, 15), // Diesel price range
        ]);
    }

    /**
     * Indicate that this is a gasoline refueling.
     */
    public function gasoline(): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_price' => fake()->randomFloat(3, 14, 18), // Gasoline price range
        ]);
    }

    /**
     * Indicate high fuel consumption.
     */
    public function highConsumption(): static
    {
        return $this->state(function (array $attributes) {
            $quantity = fake()->randomFloat(2, 60, 100);
            $distanceCovered = fake()->numberBetween(300, 500);
            $consumptionRate = ($quantity / $distanceCovered) * 100;

            return [
                'quantity' => $quantity,
                'distance_covered' => $distanceCovered,
                'consumption_rate' => round($consumptionRate, 2), // High consumption (12-20 L/100km)
            ];
        });
    }

    /**
     * Indicate efficient fuel consumption.
     */
    public function efficient(): static
    {
        return $this->state(function (array $attributes) {
            $quantity = fake()->randomFloat(2, 30, 50);
            $distanceCovered = fake()->numberBetween(600, 1000);
            $consumptionRate = ($quantity / $distanceCovered) * 100;

            return [
                'quantity' => $quantity,
                'distance_covered' => $distanceCovered,
                'consumption_rate' => round($consumptionRate, 2), // Low consumption (3-7 L/100km)
            ];
        });
    }

    /**
     * Indicate this is a recent refueling.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'refueling_date' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
