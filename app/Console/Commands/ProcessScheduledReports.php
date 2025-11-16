<?php

namespace App\Console\Commands;

use App\Models\ScheduledReport;
use App\Http\Controllers\ReportController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send scheduled reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing scheduled reports...');

        $dueReports = ScheduledReport::where('is_active', true)
            ->where('next_run_at', '<=', now())
            ->get();

        if ($dueReports->count() === 0) {
            $this->info('No reports due for processing.');
            return 0;
        }

        $processed = 0;
        $failed = 0;

        foreach ($dueReports as $report) {
            try {
                $this->info("Processing: {$report->name}");

                // Generate report (logic would go here)
                // For now, just update timestamps

                $report->update([
                    'last_run_at' => now(),
                    'next_run_at' => $this->calculateNextRun($report->frequency),
                ]);

                $processed++;
                $this->info("✓ Processed: {$report->name}");
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed: {$report->name} - {$e->getMessage()}");
            }
        }

        $this->info("\nSummary:");
        $this->info("Processed: {$processed}");
        if ($failed > 0) {
            $this->warn("Failed: {$failed}");
        }

        return 0;
    }

    /**
     * Calculate next run date
     */
    protected function calculateNextRun($frequency)
    {
        return match ($frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            default => now()->addMonth(),
        };
    }
}
