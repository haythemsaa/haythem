<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Site;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $hireDate = fake()->dateTimeBetween('-10 years', '-1 month');

        return [
            'employee_code' => 'EMP-' . fake()->unique()->numberBetween(1000, 9999),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $firstName . ' ' . $lastName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'cin' => strtoupper(fake()->bothify('??######')),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-22 years'),
            'hire_date' => $hireDate,
            'site_id' => Site::inRandomOrder()->first()?->id,
            'department_id' => Department::inRandomOrder()->first()?->id,
            'position' => fake()->randomElement([
                'Chauffeur',
                'Chauffeur-Livreur',
                'Mécanicien',
                'Chef Mécanicien',
                'Responsable Parc',
                'Agent de Maintenance',
                'Coordinateur Logistique',
            ]),
            'status' => fake()->randomElement(['actif', 'actif', 'actif', 'conge', 'suspendu']),
            'driving_license_number' => fake()->optional(0.7)->numerify('##########'),
            'driving_license_categories' => fake()->optional(0.7)->randomElements(['B', 'C', 'D', 'E'], rand(1, 3)),
            'driving_license_expiry' => fake()->optional(0.7)->dateTimeBetween('now', '+5 years'),
            'medical_checkup_date' => fake()->optional(0.6)->dateTimeBetween('-1 year', 'now'),
            'next_medical_checkup' => fake()->optional(0.6)->dateTimeBetween('now', '+1 year'),
            'address' => fake()->address(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->phoneNumber(),
            'photo' => null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the employee is a driver.
     */
    public function driver(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => fake()->randomElement(['Chauffeur', 'Chauffeur-Livreur']),
            'driving_license_number' => fake()->numerify('##########'),
            'driving_license_categories' => fake()->randomElements(['B', 'C', 'D', 'E'], rand(2, 4)),
            'driving_license_expiry' => fake()->dateTimeBetween('+1 month', '+5 years'),
        ]);
    }

    /**
     * Indicate that the employee is a mechanic.
     */
    public function mechanic(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => fake()->randomElement(['Mécanicien', 'Chef Mécanicien']),
            'driving_license_number' => fake()->optional(0.5)->numerify('##########'),
            'driving_license_categories' => fake()->optional(0.5)->randomElements(['B', 'C'], 1),
        ]);
    }

    /**
     * Indicate that the employee is a manager.
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => fake()->randomElement([
                'Responsable Parc',
                'Coordinateur Logistique',
                'Chef d\'Équipe',
            ]),
            'hire_date' => fake()->dateTimeBetween('-15 years', '-2 years'),
        ]);
    }

    /**
     * Indicate that the employee is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'actif',
        ]);
    }

    /**
     * Indicate that the employee is on leave.
     */
    public function onLeave(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'conge',
        ]);
    }

    /**
     * Indicate that the employee has all required certifications.
     */
    public function certified(): static
    {
        return $this->state(fn (array $attributes) => [
            'driving_license_number' => fake()->numerify('##########'),
            'driving_license_categories' => ['B', 'C', 'D'],
            'driving_license_expiry' => fake()->dateTimeBetween('+1 year', '+5 years'),
            'medical_checkup_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'next_medical_checkup' => fake()->dateTimeBetween('+3 months', '+1 year'),
        ]);
    }

    /**
     * Indicate that the employee's license is expiring soon.
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'driving_license_expiry' => fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }
}
