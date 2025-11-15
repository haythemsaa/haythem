<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            FleetDataSeeder::class,
        ]);

        $this->command->info('✅ Base de données seedée avec succès!');
        $this->command->info('');
        $this->command->info('🔐 Utilisateur Admin créé:');
        $this->command->info('   Email: admin@fleet.com');
        $this->command->info('   Password: password123');
    }
}
