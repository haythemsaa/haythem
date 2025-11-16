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
            SettingsSeeder::class,
            SitesSeeder::class,
            FleetDataSeeder::class,
        ]);

        $this->command->info('✅ Base de données seedée avec succès!');
        $this->command->info('');
        $this->command->info('🔐 Utilisateur Admin créé:');
        $this->command->info('   Email: admin@fleet.com');
        $this->command->info('   Password: password123');
        $this->command->info('');
        $this->command->info('⚙️  Paramètres système configurés');
        $this->command->info('🏢 Sites/dépôts créés (Casablanca, Rabat, Marrakech, Tanger, Agadir)');
    }
}
