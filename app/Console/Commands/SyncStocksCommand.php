<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WildberriesApiClient;
use App\Models\Stock;

class SyncStocksCommand extends Command
{
    // The console command name and arguments
    protected $signature = 'wb:sync-stocks';

    // The console command description
    protected $description = 'Fetch and sync stock data from Wildberries API';

    protected WildberriesApiClient $client;

    public function __construct(WildberriesApiClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle()
    {
        $this->info('Starting ALL stocks synchronization...');
        $today = now()->format('Y-m-d');
        $apiKey = config('services.wb.key');
        
        $page = 1;
        // $limit = 500; reduced to avoid query exceptions from inconsistent/corrupted mock data provided by the API
        $limit = 5; // Low limit to avoid dirty/inconsistent mock data from the API

        do {
            $this->info("Fetching Stocks - Page {$page}...");
            $endpoint = "api/stocks?dateFrom={$today}&dateTo={$today}&page={$page}&limit={$limit}&key={$apiKey}";
            $response = $this->client->fetchData($endpoint);

            if (!$response || !isset($response['data'])) {
                $this->error('Failed to retrieve data.');
                break;
            }

            foreach ($response['data'] as $item) {
                Stock::updateOrCreate(
                    ['barcode' => $item['barcode'], 'date' => $item['date']],
                    [
                        'last_change_date'   => $item['last_change_date'],
                        'supplier_article'   => $item['supplier_article'],
                        'tech_size'          => $item['tech_size'],
                        'quantity'           => $item['quantity'],
                        'is_supply'          => $item['is_supply'],
                        'is_realization'     => $item['is_realization'],
                        'quantity_full'      => $item['quantity_full'],
                        'warehouse_name'     => $item['warehouse_name'],
                        'in_way_to_client'   => $item['in_way_to_client'],
                        'in_way_from_client' => $item['in_way_from_client'],
                        'nm_id'              => $item['nm_id'],
                        'subject'            => $item['subject'],
                        'category'           => $item['category'],
                        'brand'              => $item['brand'],
                        'sc_code'            => $item['sc_code'] ?? null,
                        'price'              => $item['price'],
                        'discount'           => $item['discount'],
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

        $this->info('All Stocks synced successfully!');
        return Command::SUCCESS;
    }
}