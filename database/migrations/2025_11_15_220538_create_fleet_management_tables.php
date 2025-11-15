<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sites et multi-sites
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Parcs et sous-parcs
        Schema::create('parcs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('parcs')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        // Catégories de véhicules
        Schema::create('vehicle_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Marques
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        // Modèles
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->nullable(); // Berline, SUV, Camion, Bus, etc.
            $table->timestamps();
        });

        // Modes d'acquisition
        Schema::create('acquisition_modes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Achat, LLD, Leasing, Location
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Fournisseurs / Loueurs
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type'); // fournisseur, loueur, assureur, garage, etc.
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('bank_account')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Véhicules (Table principale)
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique(); // Immatriculation
            $table->string('internal_code')->unique(); // Code interne
            $table->string('fleet_number')->nullable(); // Numéro de flotte
            $table->foreignId('brand_id')->constrained()->onDelete('restrict');
            $table->foreignId('vehicle_model_id')->constrained()->onDelete('restrict');
            $table->foreignId('vehicle_category_id')->constrained()->onDelete('restrict');
            $table->foreignId('parc_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->string('vin')->unique()->nullable(); // Numéro de châssis
            $table->string('color')->nullable();
            $table->string('photo')->nullable();
            $table->integer('year')->nullable(); // Année de mise en service
            $table->string('engine_type')->nullable(); // Diesel, Essence, Électrique, Hybride
            $table->integer('engine_power')->nullable(); // Puissance en CV
            $table->integer('fuel_capacity')->nullable(); // Capacité réservoir en litres
            $table->string('tire_type')->nullable(); // Type de pneumatiques
            $table->decimal('length', 8, 2)->nullable(); // Longueur en mètres
            $table->decimal('width', 8, 2)->nullable(); // Largeur en mètres
            $table->decimal('height', 8, 2)->nullable(); // Hauteur en mètres
            $table->integer('seats')->nullable(); // Nombre de places
            $table->decimal('load_capacity', 10, 2)->nullable(); // Capacité de charge en kg
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->foreignId('acquisition_mode_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('current_mileage')->default(0);
            $table->enum('status', ['disponible', 'en_mission', 'en_maintenance', 'en_panne', 'vendu', 'reforme'])->default('disponible');
            $table->date('sale_date')->nullable();
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Contrats d'acquisition (LLD, Leasing)
        Schema::create('acquisition_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('acquisition_mode_id')->constrained()->onDelete('restrict');
            $table->string('contract_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_months');
            $table->decimal('monthly_payment', 10, 2);
            $table->integer('max_mileage')->nullable();
            $table->text('conditions')->nullable();
            $table->enum('status', ['actif', 'resilie', 'termine'])->default('actif');
            $table->timestamps();
        });

        // Paiements des contrats
        Schema::create('contract_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('acquisition_contract_id')->constrained()->onDelete('cascade');
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['en_attente', 'paye', 'en_retard'])->default('en_attente');
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Collaborateurs / Employés
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('employee_code')->unique();
            $table->string('photo')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('position')->nullable(); // Poste
            $table->string('department')->nullable();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supervisor_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('hire_date')->nullable();
            $table->string('contract_type')->nullable(); // CDI, CDD, etc.
            $table->decimal('base_salary', 10, 2)->nullable();
            $table->enum('status', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->timestamps();
            $table->softDeletes();
        });

        // Permis de conduire
        Schema::create('driving_licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->json('categories'); // ['A', 'B', 'C', 'D', 'E']
            $table->integer('points')->default(12);
            $table->text('restrictions')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // Habilitations spécifiques
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('name'); // CACES, ADR, etc.
            $table->string('certification_number')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('issuing_organization')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // Infractions routières
        Schema::create('traffic_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->date('violation_date');
            $table->string('violation_type');
            $table->text('location')->nullable();
            $table->decimal('fine_amount', 10, 2)->nullable();
            $table->integer('points_deducted')->default(0);
            $table->decimal('additional_costs', 10, 2)->nullable();
            $table->text('consequences')->nullable(); // Suspension, retrait véhicule, etc.
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // Accidents et incidents
        Schema::create('accidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('accident_date');
            $table->time('accident_time')->nullable();
            $table->text('location');
            $table->text('description');
            $table->enum('severity', ['mineure', 'serieuse', 'grave', 'critique'])->default('mineure');
            $table->integer('injured_count')->default(0);
            $table->integer('fatalities_count')->default(0);
            $table->text('material_damage')->nullable();
            $table->text('third_parties')->nullable();
            $table->string('police_report_number')->nullable();
            $table->string('insurance_claim_number')->nullable();
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->enum('status', ['en_cours', 'cloture'])->default('en_cours');
            $table->timestamps();
        });

        // Documents d'accident
        Schema::create('accident_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accident_id')->constrained()->onDelete('cascade');
            $table->string('document_type'); // constat, photo, rapport police, expert, etc.
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Formations
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('training_type'); // Conduite économique, sécurité routière, etc.
            $table->string('organization');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('result')->nullable(); // Réussi, Échoué, etc.
            $table->text('evaluation')->nullable();
            $table->string('certificate')->nullable();
            $table->timestamps();
        });

        // Visites médicales
        Schema::create('medical_checkups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('checkup_date');
            $table->string('medical_center')->nullable();
            $table->string('doctor_name')->nullable();
            $table->enum('result', ['apte', 'apte_avec_restrictions', 'inapte'])->default('apte');
            $table->text('restrictions')->nullable();
            $table->date('next_checkup_date')->nullable();
            $table->string('certificate')->nullable();
            $table->timestamps();
        });

        // EPI (Équipements de Protection Individuelle)
        Schema::create('epi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('equipment_type'); // Casque, gilet, gants, chaussures, etc.
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('size')->nullable();
            $table->integer('quantity')->default(1);
            $table->date('issue_date');
            $table->date('return_date')->nullable();
            $table->date('renewal_date')->nullable();
            $table->enum('status', ['en_service', 'retourne', 'perdu', 'endommage'])->default('en_service');
            $table->timestamps();
        });

        // Affectations de véhicules
        Schema::create('vehicle_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('secondary_driver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->enum('assignment_type', ['exclusive', 'shared'])->default('exclusive');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('service')->nullable();
            $table->string('project')->nullable();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('start_mileage')->nullable();
            $table->integer('end_mileage')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Carburant - Types
        Schema::create('fuel_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Diesel, Essence, GPL, Électricité
            $table->string('unit')->default('litre'); // litre, kWh
            $table->timestamps();
        });

        // Consommation carburant
        Schema::create('fuel_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('fuel_type_id')->constrained()->onDelete('restrict');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->date('refuel_date');
            $table->time('refuel_time')->nullable();
            $table->decimal('quantity', 10, 2); // Quantité en litres ou kWh
            $table->decimal('unit_price', 10, 2); // Prix unitaire
            $table->decimal('total_cost', 10, 2); // Coût total
            $table->integer('mileage'); // Kilométrage au moment du plein
            $table->string('station')->nullable();
            $table->string('voucher_number')->nullable(); // Numéro bon de carburant
            $table->string('card_number')->nullable(); // Numéro carte carburant
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Plans de maintenance préventive
        Schema::create('preventive_maintenance_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('operation_name');
            $table->text('description')->nullable();
            $table->enum('frequency_type', ['date', 'mileage', 'both'])->default('both');
            $table->integer('frequency_days')->nullable();
            $table->integer('frequency_mileage')->nullable();
            $table->date('last_execution_date')->nullable();
            $table->integer('last_execution_mileage')->nullable();
            $table->date('next_execution_date')->nullable();
            $table->integer('next_execution_mileage')->nullable();
            $table->enum('status', ['planifie', 'effectue', 'en_retard'])->default('planifie');
            $table->timestamps();
        });

        // Catégories d'intervention
        Schema::create('intervention_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Mécanique, Électrique, Carrosserie, Pneumatiques, etc.
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Demandes d'intervention (Curative)
        Schema::create('interventions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null'); // Demandeur
            $table->foreignId('intervention_category_id')->constrained()->onDelete('restrict');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['preventive', 'curative'])->default('curative');
            $table->enum('urgency', ['tres_urgent', 'urgent', 'normal', 'peut_attendre'])->default('normal');
            $table->enum('severity', ['critique', 'serieuse', 'mineure'])->default('mineure');
            $table->date('request_date');
            $table->integer('mileage_at_request')->nullable();
            $table->enum('status', ['en_attente', 'diagnostique', 'en_reparation', 'cloture', 'annule'])->default('en_attente');
            $table->timestamps();
        });

        // Diagnostics
        Schema::create('diagnostics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intervention_id')->constrained()->onDelete('cascade');
            $table->foreignId('technician_id')->constrained('employees')->onDelete('restrict');
            $table->date('diagnostic_date');
            $table->text('problem_identified');
            $table->text('proposed_works');
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->integer('estimated_duration_hours')->nullable();
            $table->timestamps();
        });

        // Ordres de travail
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intervention_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->enum('location_type', ['interne', 'externe'])->default('interne');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null'); // Garage externe
            $table->date('start_date');
            $table->time('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->text('work_performed')->nullable();
            $table->decimal('labor_cost', 10, 2)->default(0);
            $table->integer('labor_hours')->default(0);
            $table->decimal('parts_cost', 10, 2)->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->enum('status', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->timestamps();
        });

        // Affectation des techniciens aux ordres de travail
        Schema::create('work_order_technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('hours_worked')->default(0);
            $table->timestamps();
        });

        // Catégories d'articles
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Articles / Pièces détachées
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_category_id')->constrained()->onDelete('restrict');
            $table->string('reference')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit'); // pièce, litre, kg, etc.
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->integer('min_stock')->default(0);
            $table->integer('optimal_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Entrepôts / Dépôts
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->text('address')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Stock par entrepôt
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->decimal('average_unit_cost', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['article_id', 'warehouse_id']);
        });

        // Commandes fournisseurs
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('requested_by')->constrained('employees')->onDelete('restrict');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['en_attente', 'validee', 'partiellement_livree', 'livree', 'annulee'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Lignes de commande
        Schema::create('purchase_order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('restrict');
            $table->integer('quantity_ordered');
            $table->integer('quantity_received')->default(0);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        // Réceptions de commandes
        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->string('reception_number')->unique();
            $table->date('reception_date');
            $table->foreignId('received_by')->constrained('employees')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Lignes de réception
        Schema::create('reception_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reception_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('restrict');
            $table->integer('quantity_received');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Sorties de stock
        Schema::create('stock_outputs', function (Blueprint $table) {
            $table->id();
            $table->string('output_number')->unique();
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('intervention_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('work_order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('requested_by')->constrained('employees')->onDelete('restrict');
            $table->foreignId('delivered_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('output_date');
            $table->enum('output_type', ['intervention', 'epi', 'carburant_interne', 'autre'])->default('intervention');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Lignes de sortie
        Schema::create('stock_output_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_output_id')->constrained()->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->timestamps();
        });

        // Mouvements de stock
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->enum('movement_type', ['entree', 'sortie', 'transfert', 'ajustement'])->default('entree');
            $table->integer('quantity'); // Positif pour entrée, négatif pour sortie
            $table->string('reference')->nullable(); // Référence du document (commande, sortie, etc.)
            $table->text('reason')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });

        // Pneumatiques
        Schema::create('tires', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('dimensions'); // 205/55R16
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('initial_depth')->nullable(); // Profondeur sculpture en mm
            $table->integer('current_depth')->nullable();
            $table->integer('mileage')->default(0);
            $table->enum('status', ['en_stock', 'monte', 'rechape', 'reforme'])->default('en_stock');
            $table->timestamps();
        });

        // Affectation pneus sur véhicules
        Schema::create('tire_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tire_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->enum('position', ['avant_gauche', 'avant_droit', 'arriere_gauche', 'arriere_droit', 'roue_secours'])->default('avant_gauche');
            $table->date('mount_date');
            $table->integer('mount_mileage');
            $table->date('dismount_date')->nullable();
            $table->integer('dismount_mileage')->nullable();
            $table->text('dismount_reason')->nullable();
            $table->timestamps();
        });

        // Assurances
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict'); // Compagnie d'assurance
            $table->string('policy_number')->unique();
            $table->string('broker')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('annual_premium', 10, 2);
            $table->string('coverage_type')->nullable(); // Tous risques, au tiers, etc.
            $table->string('certificate')->nullable();
            $table->timestamps();
        });

        // Visites techniques
        Schema::create('technical_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('inspection_date');
            $table->enum('result', ['favorable', 'defavorable', 'contre_visite_requise'])->default('favorable');
            $table->string('inspection_center')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->date('next_inspection_date')->nullable();
            $table->text('observations')->nullable();
            $table->string('certificate')->nullable();
            $table->timestamps();
        });

        // Autorisations et documents légaux
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('document_type'); // Transport commun, scolaire, ADR, etc.
            $table->string('document_number')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->string('issuing_authority')->nullable();
            $table->text('conditions')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // Comptes bancaires et caisses
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name');
            $table->string('account_number')->unique()->nullable();
            $table->enum('account_type', ['banque', 'caisse'])->default('banque');
            $table->string('bank_name')->nullable();
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('initial_balance', 12, 2)->default(0);
            $table->decimal('current_balance', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Clients
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('type')->default('particulier'); // particulier, entreprise
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_id')->nullable();
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->integer('payment_terms')->default(30); // Délai paiement en jours
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Factures clients
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('status', ['brouillon', 'emise', 'partiellement_payee', 'payee', 'annulee'])->default('brouillon');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Lignes de facture
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->text('description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        // Règlements clients
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('bank_account_id')->constrained()->onDelete('restrict');
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // espèces, chèque, virement, carte
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Factures fournisseurs
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('purchase_order_id')->nullable()->constrained()->onDelete('set null');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->enum('status', ['en_attente', 'partiellement_payee', 'payee'])->default('en_attente');
            $table->timestamps();
        });

        // Règlements fournisseurs
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->foreignId('bank_account_id')->constrained()->onDelete('restrict');
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tarification transport
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('service_type'); // marchandise, voyageurs, touristique, BTP
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade'); // null = tarif général
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('price_per_km', 10, 2)->default(0);
            $table->decimal('price_per_ton', 10, 2)->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Devis transport
        Schema::create('transport_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->date('quote_date');
            $table->date('valid_until');
            $table->string('service_type'); // marchandise, voyageurs, touristique, BTP
            $table->text('origin');
            $table->text('destination');
            $table->decimal('estimated_distance', 10, 2)->nullable();
            $table->text('cargo_description')->nullable();
            $table->decimal('cargo_weight', 10, 2)->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['brouillon', 'envoye', 'accepte', 'refuse', 'expire'])->default('brouillon');
            $table->timestamps();
        });

        // Commandes transport
        Schema::create('transport_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('transport_quote_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->date('order_date');
            $table->string('service_type');
            $table->text('origin');
            $table->text('destination');
            $table->text('cargo_description')->nullable();
            $table->decimal('cargo_weight', 10, 2)->nullable();
            $table->date('pickup_date');
            $table->time('pickup_time')->nullable();
            $table->date('delivery_date')->nullable();
            $table->time('delivery_time')->nullable();
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['en_attente', 'planifie', 'en_cours', 'termine', 'annule', 'facture'])->default('en_attente');
            $table->timestamps();
        });

        // Missions transport
        Schema::create('transport_missions', function (Blueprint $table) {
            $table->id();
            $table->string('mission_number')->unique();
            $table->foreignId('transport_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('secondary_driver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->date('planned_start_date');
            $table->time('planned_start_time')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->time('planned_end_time')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->time('actual_start_time')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->time('actual_end_time')->nullable();
            $table->integer('start_mileage')->nullable();
            $table->integer('end_mileage')->nullable();
            $table->decimal('driver_bonus', 10, 2)->default(0);
            $table->decimal('road_expenses', 10, 2)->default(0);
            $table->enum('status', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->timestamps();
        });

        // Feuilles de route
        Schema::create('route_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transport_mission_id')->constrained()->onDelete('cascade');
            $table->text('detailed_route')->nullable();
            $table->text('cargo_details')->nullable();
            $table->text('special_instructions')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // Lignes régulières (pour transport voyageurs)
        Schema::create('regular_lines', function (Blueprint $table) {
            $table->id();
            $table->string('line_number')->unique();
            $table->string('name');
            $table->text('route');
            $table->json('stops'); // Liste des arrêts
            $table->decimal('total_distance', 10, 2);
            $table->integer('duration_minutes');
            $table->decimal('base_fare', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Horaires lignes régulières
        Schema::create('line_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regular_line_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('driver_id')->nullable()->constrained('employees')->onDelete('set null');
            $table->time('departure_time');
            $table->json('days_of_week'); // ['lundi', 'mardi', ...]
            $table->integer('available_seats')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Billets (pour transport voyageurs)
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('regular_line_id')->constrained()->onDelete('restrict');
            $table->foreignId('line_schedule_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->string('passenger_name');
            $table->date('travel_date');
            $table->string('departure_stop');
            $table->string('arrival_stop');
            $table->string('seat_number')->nullable();
            $table->decimal('fare', 10, 2);
            $table->decimal('luggage_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('sold_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->foreignId('site_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['reserve', 'vendu', 'annule', 'utilise'])->default('vendu');
            $table->timestamps();
        });

        // Location de véhicules - Tarifs
        Schema::create('rental_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade'); // null = tarif général
            $table->decimal('daily_rate', 10, 2);
            $table->integer('included_km')->default(0);
            $table->decimal('extra_km_rate', 10, 2)->default(0);
            $table->decimal('franchise', 10, 2)->default(0); // Franchise en cas de dommages
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Réservations location
        Schema::create('rental_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('vehicle_category_id')->constrained()->onDelete('restrict');
            $table->date('pickup_date');
            $table->time('pickup_time')->nullable();
            $table->date('return_date');
            $table->time('return_time')->nullable();
            $table->enum('status', ['reserve', 'confirme', 'en_cours', 'termine', 'annule'])->default('reserve');
            $table->timestamps();
        });

        // Contrats de location
        Schema::create('rental_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('rental_reservation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('vehicle_id')->constrained()->onDelete('restrict');
            $table->date('pickup_date');
            $table->time('pickup_time')->nullable();
            $table->integer('pickup_mileage');
            $table->integer('pickup_fuel_level'); // En pourcentage
            $table->date('planned_return_date');
            $table->date('actual_return_date')->nullable();
            $table->time('actual_return_time')->nullable();
            $table->integer('return_mileage')->nullable();
            $table->integer('return_fuel_level')->nullable();
            $table->decimal('daily_rate', 10, 2);
            $table->integer('included_km');
            $table->decimal('extra_km_rate', 10, 2);
            $table->decimal('base_amount', 12, 2);
            $table->decimal('extra_km_cost', 12, 2)->default(0);
            $table->decimal('late_return_fee', 12, 2)->default(0);
            $table->decimal('damage_cost', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->text('pickup_notes')->nullable();
            $table->text('return_notes')->nullable();
            $table->enum('status', ['en_cours', 'termine', 'annule'])->default('en_cours');
            $table->timestamps();
        });

        // GPS Trackers
        Schema::create('gps_trackers', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique();
            $table->string('imei')->unique();
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->date('installation_date')->nullable();
            $table->enum('status', ['actif', 'inactif', 'en_panne'])->default('actif');
            $table->timestamps();
        });

        // Positions GPS
        Schema::create('gps_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gps_tracker_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('speed', 5, 2)->nullable(); // km/h
            $table->integer('heading')->nullable(); // Direction en degrés
            $table->timestamp('position_time');
            $table->integer('mileage')->nullable();
            $table->timestamps();
            $table->index(['gps_tracker_id', 'position_time']);
        });

        // Géofences (zones virtuelles)
        Schema::create('geofences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('shape', ['circle', 'polygon'])->default('circle');
            $table->decimal('center_latitude', 10, 8)->nullable();
            $table->decimal('center_longitude', 11, 8)->nullable();
            $table->decimal('radius', 10, 2)->nullable(); // en mètres
            $table->json('polygon_coordinates')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Alertes géolocalisation
        Schema::create('gps_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('gps_tracker_id')->nullable()->constrained()->onDelete('set null');
            $table->string('alert_type'); // speeding, geofence_entry, geofence_exit, unauthorized_use
            $table->text('description');
            $table->timestamp('alert_time');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('speed', 5, 2)->nullable();
            $table->foreignId('geofence_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_acknowledged')->default(false);
            $table->timestamps();
        });

        // Notifications système
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // maintenance_due, document_expiring, alert, etc.
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Logs d'audit
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create, update, delete
            $table->string('model'); // Vehicle, Intervention, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('gps_alerts');
        Schema::dropIfExists('geofences');
        Schema::dropIfExists('gps_positions');
        Schema::dropIfExists('gps_trackers');
        Schema::dropIfExists('rental_contracts');
        Schema::dropIfExists('rental_reservations');
        Schema::dropIfExists('rental_rates');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('line_schedules');
        Schema::dropIfExists('regular_lines');
        Schema::dropIfExists('route_sheets');
        Schema::dropIfExists('transport_missions');
        Schema::dropIfExists('transport_orders');
        Schema::dropIfExists('transport_quotes');
        Schema::dropIfExists('pricing_rules');
        Schema::dropIfExists('supplier_payments');
        Schema::dropIfExists('supplier_invoices');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('legal_documents');
        Schema::dropIfExists('technical_inspections');
        Schema::dropIfExists('insurances');
        Schema::dropIfExists('tire_assignments');
        Schema::dropIfExists('tires');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_output_lines');
        Schema::dropIfExists('stock_outputs');
        Schema::dropIfExists('reception_lines');
        Schema::dropIfExists('receptions');
        Schema::dropIfExists('purchase_order_lines');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_categories');
        Schema::dropIfExists('work_order_technicians');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('diagnostics');
        Schema::dropIfExists('interventions');
        Schema::dropIfExists('intervention_categories');
        Schema::dropIfExists('preventive_maintenance_plans');
        Schema::dropIfExists('fuel_consumptions');
        Schema::dropIfExists('fuel_types');
        Schema::dropIfExists('vehicle_assignments');
        Schema::dropIfExists('epi');
        Schema::dropIfExists('medical_checkups');
        Schema::dropIfExists('trainings');
        Schema::dropIfExists('accident_documents');
        Schema::dropIfExists('accidents');
        Schema::dropIfExists('traffic_violations');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('driving_licenses');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('contract_payments');
        Schema::dropIfExists('acquisition_contracts');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('acquisition_modes');
        Schema::dropIfExists('vehicle_models');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('vehicle_categories');
        Schema::dropIfExists('parcs');
        Schema::dropIfExists('sites');
    }
};
