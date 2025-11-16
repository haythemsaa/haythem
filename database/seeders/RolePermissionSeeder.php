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

            // Insurance
            'view_insurance',
            'create_insurance',
            'edit_insurance',
            'delete_insurance',

            // Contracts
            'view_contracts',
            'create_contracts',
            'edit_contracts',
            'delete_contracts',

            // Suppliers
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // Inventory Parts
            'view_inventory',
            'create_inventory',
            'edit_inventory',
            'delete_inventory',

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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Créer les rôles et assigner les permissions

        // 1. Administrateur - Tous les droits
        $admin = Role::firstOrCreate(['name' => 'Administrateur']);
        $admin->givePermissionTo(Permission::all());

        // 2. Gestionnaire de Flotte
        $fleetManager = Role::firstOrCreate(['name' => 'Gestionnaire de Flotte']);
        $fleetManager->syncPermissions([
            'view_vehicles', 'create_vehicles', 'edit_vehicles', 'assign_vehicles',
            'view_fuel', 'create_fuel', 'edit_fuel',
            'view_documents', 'create_documents', 'edit_documents',
            'view_accidents', 'create_accidents', 'edit_accidents', 'close_accidents',
            'view_violations', 'create_violations', 'edit_violations',
            'view_insurance', 'create_insurance', 'edit_insurance', 'delete_insurance',
            'view_contracts', 'create_contracts', 'edit_contracts', 'delete_contracts',
            'view_rentals', 'create_rentals', 'close_rentals',
            'view_suppliers', 'create_suppliers', 'edit_suppliers',
            'view_gps', 'view_gps_alerts',
        ]);

        // 3. Responsable Maintenance
        $maintenanceManager = Role::firstOrCreate(['name' => 'Responsable Maintenance']);
        $maintenanceManager->syncPermissions([
            'view_vehicles',
            'view_interventions', 'create_interventions', 'edit_interventions', 'close_interventions',
            'view_work_orders', 'create_work_orders', 'edit_work_orders',
            'view_stock',
            'view_suppliers', 'create_suppliers', 'edit_suppliers',
            'view_inventory', 'create_inventory', 'edit_inventory', 'delete_inventory',
        ]);

        // 4. Responsable RH
        $hrManager = Role::firstOrCreate(['name' => 'Responsable RH']);
        $hrManager->syncPermissions([
            'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
            'view_trainings', 'create_trainings', 'edit_trainings', 'delete_trainings',
            'view_accidents',
            'view_violations',
            'view_vehicles',
        ]);

        // 5. Magasinier
        $warehouseManager = Role::firstOrCreate(['name' => 'Magasinier']);
        $warehouseManager->syncPermissions([
            'view_stock', 'create_purchase_orders', 'receive_stock', 'output_stock',
            'view_inventory', 'create_inventory', 'edit_inventory',
            'view_suppliers',
        ]);

        // 6. Responsable Financier
        $financeManager = Role::firstOrCreate(['name' => 'Responsable Financier']);
        $financeManager->syncPermissions([
            'view_invoices', 'create_invoices',
            'view_payments', 'create_payments',
            'view_bank_accounts',
            'view_insurance', 'edit_insurance',
            'view_contracts', 'edit_contracts',
            'view_rentals',
        ]);

        // 7. Responsable Transport
        $transportManager = Role::firstOrCreate(['name' => 'Responsable Transport']);
        $transportManager->syncPermissions([
            'view_vehicles', 'view_employees',
            'view_transport_orders', 'create_transport_orders',
            'view_missions', 'create_missions',
            'view_gps',
        ]);

        // 8. Responsable Location
        $rentalManager = Role::firstOrCreate(['name' => 'Responsable Location']);
        $rentalManager->syncPermissions([
            'view_vehicles',
            'view_rentals', 'create_rentals', 'close_rentals',
            'view_contracts', 'create_contracts', 'edit_contracts',
        ]);

        // 9. Conducteur - Droits minimaux
        $driver = Role::firstOrCreate(['name' => 'Conducteur']);
        $driver->syncPermissions([
            'create_interventions',
            'view_missions',
        ]);

        // Créer un utilisateur administrateur par défaut
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@fleet.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password123'),
            ]
        );
        if (!$adminUser->hasRole('Administrateur')) {
            $adminUser->assignRole('Administrateur');
        }

        $this->command->info('Rôles et permissions créés avec succès!');
        $this->command->info('Utilisateur admin créé: admin@fleet.com / password123');
    }
}
