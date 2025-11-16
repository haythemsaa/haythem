<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Gestion de Flotte
            'view_vehicles',
            'create_vehicles',
            'edit_vehicles',
            'delete_vehicles',
            'assign_vehicles',

            // Fuel
            'view_fuel',
            'create_fuel',
            'edit_fuel',
            'delete_fuel',

            // Documents
            'view_documents',
            'create_documents',
            'edit_documents',
            'delete_documents',

            // Maintenance
            'view_interventions',
            'create_interventions',
            'edit_interventions',
            'close_interventions',
            'view_work_orders',
            'create_work_orders',
            'edit_work_orders',

            // RH
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',
            'view_trainings',
            'create_trainings',
            'edit_trainings',
            'delete_trainings',

            // Accidents
            'view_accidents',
            'create_accidents',
            'edit_accidents',
            'delete_accidents',
            'close_accidents',

            // Traffic Violations
            'view_violations',
            'create_violations',
            'edit_violations',
            'delete_violations',

            // Stocks
            'view_stock',
            'create_purchase_orders',
            'receive_stock',
            'output_stock',

            // Finance
            'view_invoices',
            'create_invoices',
            'view_payments',
            'create_payments',
            'view_bank_accounts',

            // Transport
            'view_transport_orders',
            'create_transport_orders',
            'view_missions',
            'create_missions',

            // Location
            'view_rentals',
            'create_rentals',
            'close_rentals',

            // GPS
            'view_gps',
            'view_gps_alerts',

            // Administration
            'manage_users',
            'manage_roles',
            'manage_settings',
            'view_audit_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer les rôles et assigner les permissions

        // 1. Administrateur - Tous les droits
        $admin = Role::create(['name' => 'Administrateur']);
        $admin->givePermissionTo(Permission::all());

        // 2. Gestionnaire de Flotte
        $fleetManager = Role::create(['name' => 'Gestionnaire de Flotte']);
        $fleetManager->givePermissionTo([
            'view_vehicles', 'create_vehicles', 'edit_vehicles', 'assign_vehicles',
            'view_fuel', 'create_fuel', 'edit_fuel',
            'view_documents', 'create_documents', 'edit_documents',
            'view_accidents', 'create_accidents', 'edit_accidents', 'close_accidents',
            'view_violations', 'create_violations', 'edit_violations',
            'view_gps', 'view_gps_alerts',
        ]);

        // 3. Responsable Maintenance
        $maintenanceManager = Role::create(['name' => 'Responsable Maintenance']);
        $maintenanceManager->givePermissionTo([
            'view_vehicles',
            'view_interventions', 'create_interventions', 'edit_interventions', 'close_interventions',
            'view_work_orders', 'create_work_orders', 'edit_work_orders',
            'view_stock',
        ]);

        // 4. Responsable RH
        $hrManager = Role::create(['name' => 'Responsable RH']);
        $hrManager->givePermissionTo([
            'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
            'view_trainings', 'create_trainings', 'edit_trainings', 'delete_trainings',
            'view_accidents',
            'view_violations',
            'view_vehicles',
        ]);

        // 5. Magasinier
        $warehouseManager = Role::create(['name' => 'Magasinier']);
        $warehouseManager->givePermissionTo([
            'view_stock', 'create_purchase_orders', 'receive_stock', 'output_stock',
        ]);

        // 6. Responsable Financier
        $financeManager = Role::create(['name' => 'Responsable Financier']);
        $financeManager->givePermissionTo([
            'view_invoices', 'create_invoices',
            'view_payments', 'create_payments',
            'view_bank_accounts',
        ]);

        // 7. Responsable Transport
        $transportManager = Role::create(['name' => 'Responsable Transport']);
        $transportManager->givePermissionTo([
            'view_vehicles', 'view_employees',
            'view_transport_orders', 'create_transport_orders',
            'view_missions', 'create_missions',
            'view_gps',
        ]);

        // 8. Responsable Location
        $rentalManager = Role::create(['name' => 'Responsable Location']);
        $rentalManager->givePermissionTo([
            'view_vehicles',
            'view_rentals', 'create_rentals', 'close_rentals',
        ]);

        // 9. Conducteur - Droits minimaux
        $driver = Role::create(['name' => 'Conducteur']);
        $driver->givePermissionTo([
            'create_interventions',
            'view_missions',
        ]);

        // Créer un utilisateur administrateur par défaut
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'admin@fleet.com',
            'password' => bcrypt('password123'),
        ]);
        $adminUser->assignRole('Administrateur');

        $this->command->info('Rôles et permissions créés avec succès!');
        $this->command->info('Utilisateur admin créé: admin@fleet.com / password123');
    }
}
