<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\LigneFactureController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::group(['middleware' => ['auth:api']],function () {
    Route::resource('clients', ClientController::class);
});
Route::group(['middleware' => ['auth:api']],function () {
    Route::resource('services', ServiceController::class);
});
Route::group(['middleware' => ['auth:api']],function () {
    Route::resource('factures', FactureController::class);
});
Route::group(['middleware' => ['auth:api']],function () {
    Route::resource('lignes', LigneFactureController::class);
});
Route::group(['middleware' => ['auth:api']],function () {
    Route::resource('paiements', PaiementController::class);
});
Route::group([ 'middleware' => 'api', 'prefix' => 'users' ], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});
Route::get('users/verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/factures/client/{clientId}', [FactureController::class, 'getFacturesForClient']);
    Route::get('/factures/details/{factureId}', [FactureController::class, 'getFactureDetails']);
    Route::get('/factures/pagination/paginate', [FactureController::class, 'facturesPaginate']);
    Route::get('/clients/pagination/paginate', [ClientController::class, 'clientsPaginate']);
    Route::get('/paiements/pagination/paginate', [PaiementController::class, 'paiementsPaginate']);
    Route::resource('paiements', PaiementController::class);
    Route::get('/services/pagination/paginate', [ServiceController::class, 'servicesPaginate']);
});