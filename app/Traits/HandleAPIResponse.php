<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

trait HandleAPIResponse
{
    /**
     * Handle any responses from an API request
     *
     * @param Response $response
     * @param array $apiParams
     * @return array
     */
    public function handleApiResponse(Response $response, array $apiParams): array
    {
        switch ($response->status()) {
            case 200:                                                                                                   // Success
                return $this->successResponse($response->json());
            case 400:                                                                                                   // Bad request
                Log::warning(
                    'A bad request was sent to the NYT API, request: ', [
                        'params' => $apiParams,
                        'response' => $response->body()
                    ]
                );
                return $this->errorResponse(
                    'An invalid request was made to the NYT API, please check your input parameters.',
                    $response->status(),
                );
            case 401:                                                                                                   // API key invalid
                Log::warning('API key unauthorized for NYT API.');
                return $this->errorResponse(
                    'An unexpected error occurred, please try again later.',
                    $response->status(),
                );
            case 404:                                                                                                   // API endpoint not found
                Log::warning('Request to NYT API was not found. Request: ', [
                    'params' => $apiParams,
                    'response' => $response->body()
                ]);
                return $this->errorResponse(
                    'Unable to complete the request at this time, please contact administration.',
                    $response->status(),
                );
            case 408:                                                                                                   // Timeout
                Log::warning('NYT API timeout.');
                return $this->errorResponse(
                    'A timeout occurred while making a request to the NYT API. Please try again later.',
                    $response->status(),
                );
            case 429:                                                                                                   // Rate limit exceeded
                Log::warning('Too many requests were made to the NYT API in a short amount of time.');
                return $this->errorResponse(
                    'Too many requests were made to the NYT API in a short amount of time. Please try again in 5 minutes.',
                    $response->status(),
                );
            default:                                                                                                    // Default, capture any other responses
                Log::error('Unexpected API response', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);
                return $this->errorResponse(
                    'Unable to complete the request at this time.',
                    $response->status(),
                );
        }
    }

    /**
     * Log and return error for exceptions
     *
     * @param Exception $e
     * @return array
     */
    public function handleApiException(Exception $e): array
    {
        Log::error(
            'API Request Exception: '.$e->getMessage(),
            ['trace' => $e->getTraceAsString(),
        ]);

        return [
            'success' => false,
            'status' => 500,
            'message' => 'An unexpected error occurred. Please try again later.',
            'error' => $e->getMessage(),
        ];
    }

    /**
     * Build a response array api return
     *
     * @param array $data
     * @return array
     */
    private function successResponse(array $data) : array
    {
        return [
            'success' => true,
            'status' => 200,
            'message' => 'Successfully executed the request.',
            'data' => $data,
        ];
    }

    /**
     * Build an error array for api return
     *
     * @param string $message
     * @param int $status
     * @param string|null $error
     * @return array
     */
    private function errorResponse(string $message, int $status, ?string $error = null) : array
    {
        return [
            'success' => false,
            'status' => $status,
            'message' => $message,
            'error' => $error,
        ];
    }
}
