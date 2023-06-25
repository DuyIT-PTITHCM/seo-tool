<?php

use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => [], 'prefix' => 'ranking'], function () use ($router) {
    $router->get('get', [RankingController::class, 'get']);
});
