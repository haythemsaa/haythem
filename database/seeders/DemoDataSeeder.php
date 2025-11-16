<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\FuelConsumption;
use App\Models\Intervention;
use App\Models\Employee;
use App\Models\GpsLocation;
use App\Models\Brand;
use App\Models\VehicleModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding demo data...');

        // Create employees first (drivers and mechanics)
        $this->command->info('Creating employees...');
        $drivers = Employee::factory()->driver()->count(10)->create();
        $mechanics = Employee::factory()->mechanic()->count(5)->create();
        $managers = Employee::factory()->manager()->count(2)->create();

        $this->command->info("✓ Created {$drivers->count()} drivers, {$mechanics->count()} mechanics, {$managers->count()} managers");

        // Create vehicles
        $this->command->info('Creating vehicles...');
        $vehicles = Vehicle::factory()->count(20)->create([
            'status' => 'disponible'
        ]);

        // Create some vehicles in different states
        Vehicle::factory()->count(3)->create(['status' => 'en_mission']);
        Vehicle::factory()->count(2)->create(['status' => 'en_maintenance']);

        $allVehicles = Vehicle::all();
        $this->command->info("✓ Created {$allVehicles->count()} vehicles");

        // Create fuel consumptions for each vehicle
        $this->command->info('Creating fuel consumption records...');
        $fuelCount = 0;
        foreach ($allVehicles as $vehicle) {
            // Create 5-10 fuel records per vehicle
            $count = rand(5, 10);
            FuelConsumption::factory()
                ->count($count)
                ->create([
                    'vehicle_id' => $vehicle->id,
                    'employee_id' => $drivers->random()->id,
                ]);
            $fuelCount += $count;
        }
        $this->command->info("✓ Created {$fuelCount} fuel consumption records");

        // Create interventions
        $this->command->info('Creating interventions...');
        $interventionCount = 0;

        // Completed interventions
        foreach ($allVehicles->random(15) as $vehicle) {
            Intervention::factory()
                ->completed()
                ->create([
                    'vehicle_id' => $vehicle->id,
                    'assigned_to' => $mechanics->random()->id,
                ]);
            $interventionCount++;
        }

        // In-progress interventions
        foreach ($allVehicles->random(5) as $vehicle) {
            Intervention::factory()
                ->inProgress()
                ->create([
                    'vehicle_id' => $vehicle->id,
                    'assigned_to' => $mechanics->random()->id,
                ]);
            $interventionCount++;
        }

        // Pending interventions
        foreach ($allVehicles->random(8) as $vehicle) {
            Intervention::factory()
                ->pending()
                ->create([
                    'vehicle_id' => $vehicle->id,
                    'assigned_to' => $mechanics->random()->id,
                ]);
            $interventionCount++;
        }

        // Some urgent interventions
        Intervention::factory()
            ->urgent()
            ->count(3)
            ->create([
                'vehicle_id' => $allVehicles->random()->id,
                'assigned_to' => $mechanics->random()->id,
            ]);
        $interventionCount += 3;

        $this->command->info("✓ Created {$interventionCount} interventions");

        // Create GPS locations for vehicles
        $this->command->info('Creating GPS location history...');
        $gpsCount = 0;

        // Casablanca coordinates range
        $casablancaLat = 33.5731;
        $casablancaLng = -7.5898;

        foreach ($allVehicles->where('status', '!=', 'hors_service')->random(15) as $vehicle) {
            // Create 10-20 location points per vehicle
            $count = rand(10, 20);
            for ($i = 0; $i < $count; $i++) {
                GpsLocation::create([
                    'vehicle_id' => $vehicle->id,
                    'latitude' => $casablancaLat + (rand(-100, 100) / 1000),
                    'longitude' => $casablancaLng + (rand(-100, 100) / 1000),
                    'speed' => rand(0, 120),
                    'heading' => rand(0, 360),
                    'altitude' => rand(0, 500),
                    'recorded_at' => now()->subHours(rand(1, 720)), // Last 30 days
                ]);
                $gpsCount++;
            }
        }
        $this->command->info("✓ Created {$gpsCount} GPS location records");

        $this->command->newLine();
        $this->command->info('Demo data seeding completed successfully!');
        $this->command->newLine();

        $this->command->table(
            ['Entity', 'Count'],
            [
                ['Employees', Employee::count()],
                ['Vehicles', Vehicle::count()],
                ['Fuel Consumptions', FuelConsumption::count()],
                ['Interventions', Intervention::count()],
                ['GPS Locations', GpsLocation::count()],
            ]
        );
    }
}
