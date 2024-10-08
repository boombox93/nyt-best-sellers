<?php

namespace App\Http\Controllers\NYT;

use App\Exceptions\BestSellersValidationException;
use App\Http\Requests\NYTBestSellersSearchRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BestSellersController
{
    /**
     * @param Request $request
     * @return array
     */
    public function BestSellersHistorySearch(Request $request) : array
    {
        try {
            $validator = Validator::make(
                $request->all(),
                (new NYTBestSellersSearchRequest())->rules()
            );

            if ($validator->fails()) {
                throw new BestSellersValidationException(
                    $validator->errors()->messages()
                );
            }
        } catch (BestSellersValidationException $e) {
            Log::error(
                'Validation error',
                [$e->getMessage()]
            );

            return $e->render();
        }

        $query = $request->only([
            'author',
            'isbn',
            'title',
            'offset',
        ]);

        $isbns = [];
        if (isset($query['isbn'])) {
            foreach ($query['isbn'] as $isbn) {
                $isbns[] = intval(trim($isbn));                                                                         // Intval and trim isbns
            }
        }

        $apiParams = [                                                                                                  // Create params request array
            'author' => isset($query['author']) ? trim($query['author']) : null,                                        // Trim author
            'isbn' => !empty($isbns) ? implode(';', $isbns) : null,                                                     // Separate isbns with ;
            'title' => isset($query['title']) ? trim($query['title']) : null,                                           // Trim title
            'offset' => isset($query['offset']) ? intval($query['offset']) : null,                                      // Intval offset
        ];

        try {
            $handler = Http::withoutVerifying();                                                                        // No ssl cert so don't verify
            $handler->acceptJson();                                                                                     // Set header
            $response = $handler->get(config('services.nyt.best_sellers_endpoint'), [                                   // Make request to NYT best sellers history endpoint
                'api-key' => config('services.nyt.api_key'),                                                            // Get key from config
                ...$apiParams                                                                                           // Add query params
            ]);
        } catch(\Exception $e) {                                                                                        // Handle any exception that may happen
            Log::error('Exception: '.$e->getMessage());
            Log::error($e->getTraceAsString());

            return [
                'success' => false,
                'status' => 500,                                                                                        // Internal failure
                'message' => 'An unexpected error occurred, please try again later.',
                'error' => $e->getMessage(),
            ];
        }

        if (!$response->successful()) {
            return [
                'success' => false,
                'status' => $response->status(),
                'message' => 'Unable to complete the request at this time.',
                'error' => $response->reason()
            ];
        }

        return [
            'success' => true,
            'status' => 200,                                                                                            // Success code
            'message' => 'Successfully executed the request.',
            'data' => $response->json(),                                                                                // Return response data as json
        ];
    }
}
