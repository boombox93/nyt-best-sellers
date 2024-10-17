<?php

namespace App\Http\Controllers\NYT;

use App\Exceptions\BestSellersValidationException;
use App\Http\Requests\NYTBestSellersSearchRequest;
use App\Services\NYT\BestSellersHistoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BestSellersController
{
    private BestSellersHistoryService $bestSellersHistoryService;

    public function __construct(BestSellersHistoryService $bestSellersHistoryService) {
        $this->bestSellersHistoryService = $bestSellersHistoryService;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function BestSellersHistorySearch(Request $request) : array
    {
        try {
            $this->validateRequest($request);
        } catch (BestSellersValidationException $e) {
            Log::error(
                'Validation error',
                [$e->getMessage()]
            );

            return $e->render();
        }

        return $this->bestSellersHistoryService->fetchBestSellers(
            $this->prepareQueryParams($request)
        );
    }

    /**
     * Validate incoming request
     *
     * @param Request $request
     * @return void
     * @throws BestSellersValidationException
     */
    protected function validateRequest(Request $request) : void
    {
        $validator = Validator::make($request->all(), (new NYTBestSellersSearchRequest())->rules());

        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            Log::error('Validation error', $errors);
            throw new BestSellersValidationException($errors);
        }
    }

    /**
     * Prepare the params sent to the API
     *
     * @param Request $request
     * @return array
     */
    protected function prepareQueryParams(Request $request) : array
    {
        $query = $request->only([
            'author',
            'isbn',
            'title',
            'offset',
        ]);

        $isbns = $this->prepareIsbns($query['isbn'] ?? []);

        return [
            'author' => isset($query['author']) ? trim($query['author']) : null,                                        // Trim author
            'isbn' => !empty($isbns) ? implode(';', $isbns) : null,                                            // Separate isbns with ;
            'title' => isset($query['title']) ? trim($query['title']) : null,                                           // Trim title
            'offset' => isset($query['offset']) ? intval($query['offset']) : null,                                      // Intval offset
        ];
    }

    /**
     * Separate ISBNs with a ';' char
     *
     * @param array $isbn
     * @return array
     */
    protected function prepareIsbns(array $isbn) : array
    {
        return array_map(static function($isbn) {
            return intval(trim($isbn));
        }, $isbn);
    }
}
