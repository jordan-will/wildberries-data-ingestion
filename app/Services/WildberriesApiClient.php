<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WildberriesApiClient
{
    protected string $host;
    protected string $key;

    public function __construct()
    {
        // Pulls the automatic configurations mapped in config/services.php
        $this->host = config('services.wb.host');
        $this->key  = config('services.wb.key');
    }

  
    public function fetchData(string $endpoint): ?array
    {
        try {
            // Builds the target URL (e.g., http://109.73.206.144:6969/api/v1/stocks)
            $url = "http://{$this->host}/{$endpoint}";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->key,
                'Accept'        => 'application/json',
            ])
            ->retry(3, 100) 
            ->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("Wildberries API Error [{$endpoint}]: Status {$response->status()} - {$response->body()}");
            return null;

        } catch (\Exception $e) {
            // Logs critical failures such as timeouts or networking connection drops
            Log::error("Critical failure connecting to Wildberries API [{$endpoint}]: " . $e->getMessage());
            return null;
        }
    }
}