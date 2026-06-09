<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncAllCommand extends Command
{
    protected $signature = 'wb:sync-all';
    protected $description = 'Run all Wildberries synchronization commands in sequence';

    public function handle()
    {
        $this->info('============ STARTING COMPLETE DATA INGESTION ============');

        $this->call('wb:sync-stocks');
        $this->call('wb:sync-orders');
        $this->call('wb:sync-sales');
        $this->call('wb:sync-incomes');

        $this->info('============ ALL DATA SUCCESSFULLY SYNCED TO RAILWAY DB ============');
        return Command::SUCCESS;
    }
}