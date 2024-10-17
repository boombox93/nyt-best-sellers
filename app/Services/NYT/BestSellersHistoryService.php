<?php

namespace App\Services\NYT;

use App\Traits\HandleAPIResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BestSellersHistoryService
{

    use HandleAPIResponse;

    /**
     * Fetch data from NYT Best Sellers API endpoint
     *
     * @param array $apiParams
     * @return array
     */
    public function fetchBestSellers(array $apiParams): array
    {
        try {
            $response = Http::withoutVerifying()
                ->acceptJson()
                ->get(config('services.nyt.best_sellers_endpoint'), [
                    'api-key' => config('services.nyt.api_key'),
                    ...$apiParams
                ]
            );

            return $this->handleApiResponse($response, $apiParams);
        } catch(\Exception $e) {                                                                                        // Handle general exception
            return $this->handleApiException($e);
        }
    }
}
