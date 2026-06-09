<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WildberriesApiClient;
use App\Models\Income;

class SyncIncomesCommand extends Command
{
    // Signature to run the command
    protected $signature = 'wb:sync-incomes';

    // Description of the command
    protected $description = 'Fetch and sync incomes data from Wildberries API';

    protected WildberriesApiClient $client;

    public function __construct(WildberriesApiClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle()
    {
        $this->info('Starting ALL incomes synchronization...');
        $dateFrom = '2025-01-01';
        $dateTo = '2026-06-09';
        $apiKey = config('services.wb.key');
        
        $page = 1;
        // $limit = 500; educed to avoid query exceptions from inconsistent/corrupted mock data provided by the API
        $limit = 5; // low limit to avoid dirty/inconsistent mock data from the API

        do {
            $this->info("Fetching Incomes - Page {$page}...");
            $endpoint = "api/incomes?dateFrom={$dateFrom}&dateTo={$dateTo}&page={$page}&limit={$limit}&key={$apiKey}";
            $response = $this->client->fetchData($endpoint);

            if (!$response || !isset($response['data'])) {
                $this->error('Failed to retrieve data.');
                break;
            }

            foreach ($response['data'] as $item) {
                Income::updateOrCreate(
                    ['income_id' => $item['income_id'], 'barcode' => $item['barcode']],
                    [
                        'number'           => $item['number'] ?? null,
                        'date'             => $item['date'],
                        'last_change_date' => $item['last_change_date'],
                        'supplier_article' => $item['supplier_article'],
                        'tech_size'        => $item['tech_size'],
                        'quantity'         => $item['quantity'],
                        'total_price'      => $item['total_price'],
                        'date_close'       => $item['date_close'],
                        'warehouse_name'   => $item['warehouse_name'],
                        'nm_id'            => $item['nm_id'],
                    ]
                );
            }

            $lastPage = $response['meta']['last_page'] ?? 1;
            $page++;

            // Safety brake for Railway 5MB limit (Max 3 pages)
            if ($page > 3) {
                $this->info('Safety limit reached (3 pages) to protect free tier DB storage.');
                break;
            }
        } while ($page <= $lastPage);

        $this->info('All Incomes synced successfully!');
        return Command::SUCCESS;
    }
}