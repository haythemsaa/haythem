<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class CleanOldAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:clean {--days=90 : Keep logs newer than this many days} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old audit logs to free up database space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);

        $this->info("Cleaning audit logs older than {$days} days...");
        $this->info("Cutoff date: " . $cutoffDate->format('Y-m-d H:i:s'));

        // Count logs to be deleted
        $count = AuditLog::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No old audit logs to clean.');
            return 0;
        }

        $this->warn("\nFound {$count} audit logs to delete.");

        // Confirm deletion unless --force is used
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to proceed with deletion?', true)) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        // Delete old logs in chunks to avoid memory issues
        $this->info("\nDeleting logs...");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $deleted = 0;
        $chunkSize = 1000;

        while (true) {
            $deleted += AuditLog::where('created_at', '<', $cutoffDate)
                ->limit($chunkSize)
                ->delete();

            $bar->advance(min($chunkSize, $count - $deleted));

            if ($deleted >= $count) {
                break;
            }

            // Small delay to avoid overwhelming the database
            usleep(100000); // 100ms
        }

        $bar->finish();

        $this->info("\n\n✓ Successfully deleted {$deleted} audit logs.");
        $this->info("Database space freed up.");

        // Show statistics
        $remaining = AuditLog::count();
        $this->info("\nRemaining audit logs: {$remaining}");

        return 0;
    }
}
