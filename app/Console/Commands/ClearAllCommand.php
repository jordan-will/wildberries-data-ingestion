<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearAllCommand extends Command
{
    // Terminal trigger command
    protected $signature = 'wb:clear-all';

    // Command description
    protected $description = 'Truncate all synchronized tables to free up Railway storage';

    public function handle()
    {
        // Ask for confirmation to avoid accidental data loss in production environments
        if (!$this->confirm('Are you sure you want to wipe all synced data from the database?')) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->info('Cleaning database tables...');

        // Disable foreign key checks to allow clean truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate clears the rows and resets the auto-increment IDs to 1
        DB::table('stocks')->truncate();
        DB::table('orders')->truncate();
        DB::table('sales')->truncate();
        DB::table('incomes')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('============ ALL TABLES CLEARED SUCCESSFULLY AND STORAGE FREED! ============');
        return Command::SUCCESS;
    }
}