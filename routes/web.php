<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\LitigantController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes([
    'register' => false,
    'reset'    => false,
    'verify'   => false,
    'confirm'  => false,
]);

Route::middleware(['auth'])->group(function () {

    Route::get('/', function () {
        return view('welcome');
    });
    //litigants
    Route::resource('litigants', LitigantController::class);

    // Additional routes if needed
    Route::get('litigants/{litigant}/addresses', [LitigantController::class, 'addresses'])
        ->name('litigants.addresses');

// Asset Management Routes
    Route::prefix('properties')->name('properties.')->group(function () {
        // AJAX routes - phải đặt TRƯỚC resource routes
        Route::get('get-fields', [AssetController::class, 'getAssetFields'])->name('get-fields');

        // Bulk operations
        Route::post('bulk-delete', [AssetController::class, 'bulkDelete'])->name('bulk-delete');

        // Export
        Route::post('export', [AssetController::class, 'export'])->name('export');

        // Statistics (optional)
        Route::get('statistics', [AssetController::class, 'getStatistics'])->name('statistics');

        // Search (optional)
        Route::get('search', [AssetController::class, 'search'])->name('search');
    });

// Clone route với parameter
    Route::post('properties/{asset}/clone', [AssetController::class, 'cloneAsset'])->name('properties.clone');

// Resource routes - phải đặt SAU các routes cụ thể
    Route::resource('properties', AssetController::class)->names([
        'index'   => 'properties.index',
        'create'  => 'properties.create',
        'store'   => 'properties.store',
        'show'    => 'properties.show',
        'edit'    => 'properties.edit',
        'update'  => 'properties.update',
        'destroy' => 'properties.destroy',
    ]);
});
Route::get('/api/search-litigants', [LitigantController::class, 'searchLitigants'])->name('search.litigants');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
