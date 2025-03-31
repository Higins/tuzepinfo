<?php

use App\Http\Controllers\Api\V1\PriceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/prices', [PriceController::class, 'index']);
    Route::get('/prices/{product}', [PriceController::class, 'show']);
});
