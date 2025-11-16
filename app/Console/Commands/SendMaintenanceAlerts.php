<?php

namespace App\Console\Commands;

use App\Models\PreventiveMaintenancePlan;
use App\Models\Insurance;
use App\Models\TechnicalInspection;
use App\Models\LegalDocument;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMaintenanceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:alerts {--days=7 : Number of days ahead to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send alerts for upcoming maintenance, expirations, and technical inspections';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("Checking for alerts (next {$days} days)...\n");

        $alertsSent = 0;

        // Check preventive maintenance plans
        $this->info('Checking preventive maintenance...');
        $alertsSent += $this->checkPreventiveMaintenance($days);

        // Check insurance expirations
        $this->info('Checking insurance expirations...');
        $alertsSent += $this->checkInsuranceExpirations($days);

        // Check technical inspections
        $this->info('Checking technical inspections...');
        $alertsSent += $this->checkTechnicalInspections($days);

        // Check legal document expirations
        $this->info('Checking legal document expirations...');
        $alertsSent += $this->checkLegalDocuments($days);

        $this->info("\n✓ Process complete!");
        $this->info("Total alerts sent: {$alertsSent}");

        return 0;
    }

    /**
     * Check for upcoming preventive maintenance
     */
    protected function checkPreventiveMaintenance($days): int
    {
        $count = 0;
        $endDate = now()->addDays($days);

        $plans = PreventiveMaintenancePlan::where('is_active', true)
            ->where('next_maintenance_date', '<=', $endDate)
            ->where('next_maintenance_date', '>=', now())
            ->with('vehicle')
            ->get();

        foreach ($plans as $plan) {
            $daysUntil = now()->diffInDays($plan->next_maintenance_date, false);

            Notification::create([
                'user_id' => 1, // Admin user
                'type' => $daysUntil <= 3 ? 'critical' : 'warning',
                'title' => 'Maintenance préventive à venir',
                'message' => "Le véhicule {$plan->vehicle->registration_number} nécessite une maintenance préventive le " . $plan->next_maintenance_date->format('d/m/Y'),
                'action_url' => '/preventive-maintenance-plans/' . $plan->id,
            ]);

            $count++;
            $this->line("  - {$plan->vehicle->registration_number}: {$daysUntil} jours");
        }

        return $count;
    }

    /**
     * Check for insurance expirations
     */
    protected function checkInsuranceExpirations($days): int
    {
        $count = 0;
        $endDate = now()->addDays($days);

        $insurances = Insurance::where('is_active', true)
            ->where('end_date', '<=', $endDate)
            ->where('end_date', '>=', now())
            ->with('vehicle')
            ->get();

        foreach ($insurances as $insurance) {
            $daysUntil = now()->diffInDays($insurance->end_date, false);

            Notification::create([
                'user_id' => 1,
                'type' => $daysUntil <= 3 ? 'critical' : 'warning',
                'title' => 'Expiration d\'assurance imminente',
                'message' => "L'assurance du véhicule {$insurance->vehicle->registration_number} expire le " . $insurance->end_date->format('d/m/Y'),
                'action_url' => '/insurances/' . $insurance->id,
            ]);

            $count++;
            $this->line("  - {$insurance->vehicle->registration_number}: {$daysUntil} jours");
        }

        return $count;
    }

    /**
     * Check for technical inspections
     */
    protected function checkTechnicalInspections($days): int
    {
        $count = 0;
        $endDate = now()->addDays($days);

        $inspections = TechnicalInspection::where('next_inspection_date', '<=', $endDate)
            ->where('next_inspection_date', '>=', now())
            ->with('vehicle')
            ->get();

        foreach ($inspections as $inspection) {
            $daysUntil = now()->diffInDays($inspection->next_inspection_date, false);

            Notification::create([
                'user_id' => 1,
                'type' => $daysUntil <= 3 ? 'critical' : 'warning',
                'title' => 'Contrôle technique à venir',
                'message' => "Le véhicule {$inspection->vehicle->registration_number} doit passer un contrôle technique le " . $inspection->next_inspection_date->format('d/m/Y'),
                'action_url' => '/technical-inspections/' . $inspection->id,
            ]);

            $count++;
            $this->line("  - {$inspection->vehicle->registration_number}: {$daysUntil} jours");
        }

        return $count;
    }

    /**
     * Check for legal document expirations
     */
    protected function checkLegalDocuments($days): int
    {
        $count = 0;
        $endDate = now()->addDays($days);

        $documents = LegalDocument::where('expiration_date', '<=', $endDate)
            ->where('expiration_date', '>=', now())
            ->with('vehicle')
            ->get();

        foreach ($documents as $document) {
            $daysUntil = now()->diffInDays($document->expiration_date, false);

            Notification::create([
                'user_id' => 1,
                'type' => $daysUntil <= 3 ? 'critical' : 'warning',
                'title' => 'Document légal expirant',
                'message' => "Le document {$document->document_type} du véhicule {$document->vehicle->registration_number} expire le " . $document->expiration_date->format('d/m/Y'),
                'action_url' => '/legal-documents/' . $document->id,
            ]);

            $count++;
            $this->line("  - {$document->vehicle->registration_number} ({$document->document_type}): {$daysUntil} jours");
        }

        return $count;
    }
}
