<?php

namespace Database\Factories;

use App\Models\Intervention;
use App\Models\Vehicle;
use App\Models\InterventionType;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intervention>
 */
class InterventionFactory extends Factory
{
    protected $model = Intervention::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', '+1 month');
        $status = fake()->randomElement(['en_attente', 'en_cours', 'terminee', 'annulee']);

        // If status is completed, set end date
        $endDate = in_array($status, ['terminee', 'annulee'])
            ? fake()->dateTimeBetween($startDate, 'now')
            : null;

        return [
            'vehicle_id' => Vehicle::factory(),
            'intervention_type_id' => InterventionType::inRandomOrder()->first()?->id ?? 1,
            'assigned_to' => Employee::inRandomOrder()->first()?->id,
            'scheduled_date' => $startDate,
            'start_date' => in_array($status, ['en_cours', 'terminee']) ? $startDate : null,
            'end_date' => $endDate,
            'mileage' => fake()->numberBetween(10000, 200000),
            'status' => $status,
            'priority' => fake()->randomElement(['faible', 'moyenne', 'haute', 'urgente']),
            'description' => fake()->paragraph(),
            'diagnostic' => $status !== 'en_attente' ? fake()->paragraph() : null,
            'work_done' => $status === 'terminee' ? fake()->paragraph() : null,
            'labor_cost' => $status === 'terminee' ? fake()->randomFloat(2, 100, 2000) : null,
            'parts_cost' => $status === 'terminee' ? fake()->randomFloat(2, 50, 5000) : null,
            'total_cost' => null, // Will be auto-calculated
            'next_intervention_date' => $status === 'terminee' ? fake()->dateTimeBetween('+1 month', '+6 months') : null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the intervention is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_attente',
            'start_date' => null,
            'end_date' => null,
            'diagnostic' => null,
            'work_done' => null,
            'labor_cost' => null,
            'parts_cost' => null,
        ]);
    }

    /**
     * Indicate that the intervention is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_cours',
            'start_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'end_date' => null,
            'diagnostic' => fake()->paragraph(),
            'work_done' => null,
        ]);
    }

    /**
     * Indicate that the intervention is completed.
     */
    public function completed(): static
    {
        $startDate = fake()->dateTimeBetween('-3 months', '-1 week');
        $endDate = fake()->dateTimeBetween($startDate, 'now');

        return $this->state(fn (array $attributes) => [
            'status' => 'terminee',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'diagnostic' => fake()->paragraph(),
            'work_done' => fake()->paragraph(),
            'labor_cost' => fake()->randomFloat(2, 200, 3000),
            'parts_cost' => fake()->randomFloat(2, 100, 8000),
        ]);
    }

    /**
     * Indicate that the intervention is urgent.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgente',
            'scheduled_date' => fake()->dateTimeBetween('now', '+3 days'),
        ]);
    }

    /**
     * Indicate that the intervention is preventive maintenance.
     */
    public function preventive(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'moyenne',
            'description' => 'Maintenance préventive programmée : ' . fake()->randomElement([
                'Vidange et changement des filtres',
                'Contrôle des freins et plaquettes',
                'Vérification du système de refroidissement',
                'Inspection générale des 50 000 km',
            ]),
        ]);
    }

    /**
     * Indicate that the intervention is corrective.
     */
    public function corrective(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => fake()->randomElement(['haute', 'urgente']),
            'description' => 'Réparation : ' . fake()->randomElement([
                'Problème de démarrage',
                'Fuite d\'huile moteur',
                'Bruit anormal au niveau des freins',
                'Système de climatisation défaillant',
                'Problème électrique',
            ]),
        ]);
    }
}
