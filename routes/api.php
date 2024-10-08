<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\NYT\BestSellersController;

if (!defined('API_VERSION')) {
    define("API_VERSION", env('API_VERSION', 1));
}

Route::get(API_VERSION.'/nyt/best-sellers', [
    BestSellersController::class, 'BestSellersHistorySearch'
]);

