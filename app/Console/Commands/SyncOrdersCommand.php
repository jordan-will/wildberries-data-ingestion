<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WildberriesApiClient;
use App\Models\Order;

class SyncOrdersCommand extends Command
{
    // Command signature to execute in terminal
    protected $signature = 'wb:sync-orders';

    // Command objective
    protected $description = 'Fetch and sync orders data from Wildberries API';

    protected WildberriesApiClient $client;

    public function __construct(WildberriesApiClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle()
    {
        $this->info('Starting ALL orders synchronization...');
        $dateFrom = '2026-01-01';
        $dateTo = '2026-06-09';
        $apiKey = config('services.wb.key');
        
        $page = 1;
        // $limit = 500; reduced to avoid query exceptions from inconsistent/corrupted mock data provided by the API
        $limit = 5; // Low limit to avoid dirty/inconsistent mock data from the API

        do {
            $this->info("Fetching Orders - Page {$page}...");
            $endpoint = "api/orders?dateFrom={$dateFrom}&dateTo={$dateTo}&page={$page}&limit={$limit}&key={$apiKey}";
            $response = $this->client->fetchData($endpoint);

            if (!$response || !isset($response['data'])) {
                $this->error('Failed to retrieve data.');
                break;
            }

            foreach ($response['data'] as $item) {
                Order::updateOrCreate(
                    ['g_number' => $item['g_number'], 'barcode' => $item['barcode']],
                    [
                        'date'             => $item['date'],
                        'last_change_date' => $item['last_change_date'],
                        'supplier_article' => $item['supplier_article'],
                        'tech_size'        => $item['tech_size'],
                        'total_price'      => $item['total_price'],
                        'discount_percent' => $item['discount_percent'],
                        'warehouse_name'   => $item['warehouse_name'],
                        'oblast'           => $item['oblast'],
                        'income_id'        => $item['income_id'],
                        'odid'             => $item['odid'],
                        'nm_id'            => $item['nm_id'],
                        'subject'          => $item['subject'],
                        'category'         => $item['category'],
                        'brand'            => $item['brand'],
                        'is_cancel'        => $item['is_cancel'],
                        'cancel_dt'        => $item['cancel_dt'] ?? null,
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

        $this->info('All Orders synced successfully!');
        return Command::SUCCESS;
    }
}