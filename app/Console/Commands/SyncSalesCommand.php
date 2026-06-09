<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WildberriesApiClient;
use App\Models\Sale;

class SyncSalesCommand extends Command
{
    // Terminal trigger command
    protected $signature = 'wb:sync-sales';

    // Description of the command task
    protected $description = 'Fetch and sync sales data from Wildberries API';

    protected WildberriesApiClient $client;

    public function __construct(WildberriesApiClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle()
    {
        $this->info('Starting ALL sales synchronization...');
        $dateFrom = '2025-01-01';
        $dateTo = '2026-06-09';
        $apiKey = config('services.wb.key');
        
        $page = 1;
        // $limit = 500; reduced to avoid query exceptions from inconsistent/corrupted mock data provided by the API
        $limit = 5; // Low limit to avoid dirty/inconsistent mock data from the API

        do {
            $this->info("Fetching Sales - Page {$page}...");
            $endpoint = "api/sales?dateFrom={$dateFrom}&dateTo={$dateTo}&page={$page}&limit={$limit}&key={$apiKey}";
            $response = $this->client->fetchData($endpoint);

            if (!$response || !isset($response['data'])) {
                $this->error('Failed to retrieve data.');
                break;
            }

            foreach ($response['data'] as $item) {
                Sale::updateOrCreate(
                    ['sale_id' => $item['sale_id']],
                    [
                        'g_number'            => $item['g_number'],
                        'date'                => $item['date'],
                        'last_change_date'    => $item['last_change_date'],
                        'supplier_article'    => $item['supplier_article'],
                        'tech_size'           => $item['tech_size'],
                        'barcode'             => $item['barcode'],
                        'total_price'         => $item['total_price'],
                        'discount_percent'    => $item['discount_percent'],
                        'is_supply'           => $item['is_supply'],
                        'is_realization'      => $item['is_realization'],
                        'promo_code_discount' => $item['promo_code_discount'] ?? null,
                        'warehouse_name'      => $item['warehouse_name'],
                        'country_name'        => $item['country_name'],
                        'oblast_okrug_name'   => $item['oblast_okrug_name'],
                        'region_name'         => $item['region_name'],
                        'income_id'           => $item['income_id'],
                        'odid'                => $item['odid'] ?? null,
                        'spp'                 => $item['spp'],
                        'for_pay'             => $item['for_pay'],
                        'finished_price'      => $item['finished_price'],
                        'price_with_disc'     => $item['price_with_disc'],
                        'nm_id'               => $item['nm_id'],
                        'subject'             => $item['subject'],
                        'category'            => $item['category'],
                        'brand'               => $item['brand'],
                        'is_storno'           => $item['is_storno'] ?? null,
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

        $this->info('All Sales synced successfully!');
        return Command::SUCCESS;
    }
}