<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Site;
use App\Models\Parc;
use App\Models\Brand;
use App\Models\VehicleModel;
use App\Models\VehicleCategory;
use App\Models\AcquisitionMode;
use App\Models\Supplier;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\User;
use App\Models\FuelType;
use App\Models\InterventionCategory;
use App\Models\ArticleCategory;
use App\Models\Warehouse;

class FleetDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer des sites
        $sites = [
            ['name' => 'Siège Social', 'code' => 'HQ', 'city' => 'Paris', 'country' => 'France'],
            ['name' => 'Agence Nord', 'code' => 'AGN', 'city' => 'Lille', 'country' => 'France'],
            ['name' => 'Agence Sud', 'code' => 'AGS', 'city' => 'Marseille', 'country' => 'France'],
        ];
        foreach ($sites as $siteData) {
            Site::create($siteData);
        }

        // 2. Créer des parcs
        $parcs = [
            ['name' => 'Parc Véhicules Légers', 'code' => 'PVL', 'site_id' => 1],
            ['name' => 'Parc Poids Lourds', 'code' => 'PPL', 'site_id' => 1],
            ['name' => 'Parc Transport Passagers', 'code' => 'PTP', 'site_id' => 1],
        ];
        foreach ($parcs as $parcData) {
            Parc::create($parcData);
        }

        // 3. Créer des marques
        $brands = [
            ['name' => 'Renault'],
            ['name' => 'Peugeot'],
            ['name' => 'Volkswagen'],
            ['name' => 'Mercedes-Benz'],
            ['name' => 'Iveco'],
            ['name' => 'Scania'],
        ];
        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }

        // 4. Créer des modèles par marque
        $models = [
            // Renault
            ['brand_id' => 1, 'name' => 'Clio', 'type' => 'Berline'],
            ['brand_id' => 1, 'name' => 'Kangoo', 'type' => 'Utilitaire'],
            ['brand_id' => 1, 'name' => 'Master', 'type' => 'Fourgon'],

            // Peugeot
            ['brand_id' => 2, 'name' => '308', 'type' => 'Berline'],
            ['brand_id' => 2, 'name' => 'Partner', 'type' => 'Utilitaire'],
            ['brand_id' => 2, 'name' => 'Boxer', 'type' => 'Fourgon'],

            // Mercedes
            ['brand_id' => 4, 'name' => 'Sprinter', 'type' => 'Fourgon'],
            ['brand_id' => 4, 'name' => 'Actros', 'type' => 'Camion'],

            // Iveco
            ['brand_id' => 5, 'name' => 'Daily', 'type' => 'Fourgon'],
            ['brand_id' => 5, 'name' => 'Stralis', 'type' => 'Camion'],
        ];
        foreach ($models as $modelData) {
            VehicleModel::create($modelData);
        }

        // 5. Créer des catégories
        $categories = [
            ['name' => 'Véhicule Léger', 'code' => 'VL'],
            ['name' => 'Utilitaire Léger', 'code' => 'UL'],
            ['name' => 'Poids Lourd', 'code' => 'PL'],
            ['name' => 'Bus', 'code' => 'BUS'],
            ['name' => 'Camion', 'code' => 'CAM'],
        ];
        foreach ($categories as $categoryData) {
            VehicleCategory::create($categoryData);
        }

        // 6. Modes d'acquisition
        $acquisitionModes = [
            ['name' => 'Achat Comptant', 'description' => 'Achat direct du véhicule'],
            ['name' => 'Crédit', 'description' => 'Achat à crédit'],
            ['name' => 'Location Longue Durée', 'description' => 'LLD'],
            ['name' => 'Leasing', 'description' => 'Location avec option d\'achat'],
        ];
        foreach ($acquisitionModes as $modeData) {
            AcquisitionMode::create($modeData);
        }

        // 7. Types de carburant
        $fuelTypes = [
            ['name' => 'Diesel', 'unit' => 'litre'],
            ['name' => 'Essence', 'unit' => 'litre'],
            ['name' => 'GPL', 'unit' => 'litre'],
            ['name' => 'Électricité', 'unit' => 'kWh'],
        ];
        foreach ($fuelTypes as $fuelData) {
            FuelType::create($fuelData);
        }

        // 8. Fournisseurs
        $suppliers = [
            ['name' => 'Renault France', 'code' => 'REN001', 'type' => 'loueur', 'city' => 'Paris', 'country' => 'France', 'is_active' => true],
            ['name' => 'Garage Central', 'code' => 'GAR001', 'type' => 'garage', 'city' => 'Paris', 'country' => 'France', 'is_active' => true],
            ['name' => 'AXA Assurances', 'code' => 'ASS001', 'type' => 'assureur', 'city' => 'Paris', 'country' => 'France', 'is_active' => true],
            ['name' => 'Pièces Auto Pro', 'code' => 'FOU001', 'type' => 'fournisseur', 'city' => 'Lyon', 'country' => 'France', 'is_active' => true],
        ];
        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // 9. Véhicules
        $vehicles = [
            [
                'registration_number' => 'AB-123-CD',
                'internal_code' => 'VEH001',
                'fleet_number' => '001',
                'brand_id' => 1,
                'vehicle_model_id' => 1,
                'vehicle_category_id' => 1,
                'parc_id' => 1,
                'site_id' => 1,
                'vin' => 'VF1RJ0F0H12345678',
                'color' => 'Blanc',
                'year' => 2022,
                'engine_type' => 'Diesel',
                'engine_power' => 90,
                'fuel_capacity' => 50,
                'seats' => 5,
                'purchase_date' => '2022-01-15',
                'purchase_price' => 18000,
                'acquisition_mode_id' => 1,
                'supplier_id' => 1,
                'current_mileage' => 45000,
                'status' => 'disponible',
            ],
            [
                'registration_number' => 'EF-456-GH',
                'internal_code' => 'VEH002',
                'fleet_number' => '002',
                'brand_id' => 2,
                'vehicle_model_id' => 5,
                'vehicle_category_id' => 2,
                'parc_id' => 1,
                'site_id' => 1,
                'vin' => 'VF3LCYHZP12345678',
                'color' => 'Gris',
                'year' => 2021,
                'engine_type' => 'Diesel',
                'engine_power' => 110,
                'fuel_capacity' => 60,
                'seats' => 3,
                'load_capacity' => 800,
                'purchase_date' => '2021-06-20',
                'purchase_price' => 22000,
                'acquisition_mode_id' => 3,
                'supplier_id' => 1,
                'current_mileage' => 78000,
                'status' => 'disponible',
            ],
            [
                'registration_number' => 'IJ-789-KL',
                'internal_code' => 'VEH003',
                'fleet_number' => '003',
                'brand_id' => 4,
                'vehicle_model_id' => 7,
                'vehicle_category_id' => 3,
                'parc_id' => 2,
                'site_id' => 1,
                'vin' => 'WDB9060451L123456',
                'color' => 'Blanc',
                'year' => 2023,
                'engine_type' => 'Diesel',
                'engine_power' => 190,
                'fuel_capacity' => 100,
                'seats' => 3,
                'load_capacity' => 3500,
                'purchase_date' => '2023-03-10',
                'purchase_price' => 45000,
                'acquisition_mode_id' => 4,
                'supplier_id' => 1,
                'current_mileage' => 15000,
                'status' => 'en_mission',
            ],
        ];
        foreach ($vehicles as $vehicleData) {
            Vehicle::create($vehicleData);
        }

        // 10. Employés
        $employees = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'employee_code' => 'EMP001',
                'phone' => '0612345678',
                'email' => 'jean.dupont@fleet.com',
                'position' => 'Chauffeur',
                'department' => 'Transport',
                'site_id' => 1,
                'hire_date' => '2020-01-15',
                'contract_type' => 'CDI',
                'base_salary' => 2500,
                'status' => 'actif',
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'employee_code' => 'EMP002',
                'phone' => '0687654321',
                'email' => 'marie.martin@fleet.com',
                'position' => 'Mécanicien',
                'department' => 'Maintenance',
                'site_id' => 1,
                'hire_date' => '2019-05-20',
                'contract_type' => 'CDI',
                'base_salary' => 2800,
                'status' => 'actif',
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Dubois',
                'employee_code' => 'EMP003',
                'phone' => '0698765432',
                'email' => 'pierre.dubois@fleet.com',
                'position' => 'Gestionnaire de Flotte',
                'department' => 'Administration',
                'site_id' => 1,
                'supervisor_id' => null,
                'hire_date' => '2018-09-01',
                'contract_type' => 'CDI',
                'base_salary' => 3200,
                'status' => 'actif',
            ],
        ];
        foreach ($employees as $employeeData) {
            Employee::create($employeeData);
        }

        // 11. Catégories d'intervention
        $interventionCategories = [
            ['name' => 'Mécanique', 'description' => 'Interventions mécaniques'],
            ['name' => 'Électrique', 'description' => 'Interventions électriques'],
            ['name' => 'Carrosserie', 'description' => 'Réparations carrosserie'],
            ['name' => 'Pneumatiques', 'description' => 'Changement et entretien pneus'],
            ['name' => 'Entretien Courant', 'description' => 'Vidanges, filtres, etc.'],
        ];
        foreach ($interventionCategories as $catData) {
            InterventionCategory::create($catData);
        }

        // 12. Catégories d'articles
        $articleCategories = [
            ['name' => 'Pièces Mécaniques', 'code' => 'MEC'],
            ['name' => 'Pièces Électriques', 'code' => 'ELEC'],
            ['name' => 'Filtres', 'code' => 'FIL'],
            ['name' => 'Huiles et Lubrifiants', 'code' => 'HUI'],
            ['name' => 'Pneumatiques', 'code' => 'PNEU'],
        ];
        foreach ($articleCategories as $artCatData) {
            ArticleCategory::create($artCatData);
        }

        // 13. Entrepôts
        $warehouses = [
            ['name' => 'Dépôt Principal', 'code' => 'DEP001', 'site_id' => 1, 'is_active' => true],
            ['name' => 'Dépôt Nord', 'code' => 'DEP002', 'site_id' => 2, 'is_active' => true],
        ];
        foreach ($warehouses as $warehouseData) {
            Warehouse::create($warehouseData);
        }

        $this->command->info('Données de flotte créées avec succès!');
        $this->command->info('- 3 Sites');
        $this->command->info('- 6 Marques et 10 Modèles');
        $this->command->info('- 5 Catégories de véhicules');
        $this->command->info('- 3 Véhicules de test');
        $this->command->info('- 3 Employés');
        $this->command->info('- Catégories et données de référence');
    }
}
