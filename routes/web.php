<?php

use App\Http\Controllers\Admin\ContractTemplateController;
use App\Http\Controllers\Admin\ContractTypeController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CertificateTypeController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\IssuingAuthorityController;
use App\Http\Controllers\LitigantController;
use App\Http\Controllers\NotificationController;
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
    'login'    => false,
    'logout'   => false,
]);

Route::get('login', [CustomAuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [CustomAuthController::class, 'login']);
Route::post('logout', [CustomAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('logs', [AdminLogController::class, 'index'])->name('admin.logs.index');
    Route::get('logs/{id}', [AdminLogController::class, 'show'])->name('admin.logs.show');
    Route::post('logs/{id}/force-logout', [AdminLogController::class, 'forceLogout'])->name('admin.logs.force-logout');
    Route::get('logs-analytics', [AdminLogController::class, 'analytics'])->name('admin.logs.analytics');
});

Route::middleware('auth')->group(function () {
    Route::get('/check-logout-notification', [NotificationController::class, 'checkLogoutNotification'])
        ->name('check.logout.notification');
    Route::post('/force-logout', [NotificationController::class, 'forceLogout'])
        ->name('force.logout');
});

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
Route::get('/certificate-types/search', [CertificateTypeController::class, 'search'])->name('certificate-types.search');
Route::post('/certificate-types', [CertificateTypeController::class, 'store'])->name('certificate-types.store');

// Issuing Authorities routes
Route::get('/issuing-authorities/search', [IssuingAuthorityController::class, 'search'])->name('issuing-authorities.search');
Route::post('/issuing-authorities', [IssuingAuthorityController::class, 'store'])->name('issuing-authorities.store');

Route::get('/api/search-litigants', [LitigantController::class, 'searchLitigants'])->name('search.litigants');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Contract Types Management
    Route::prefix('contract-types')->name('contract-types.')->group(function () {

        // Main CRUD routes
        Route::get('/', [ContractTypeController::class, 'index'])->name('index');
        Route::get('/create', [ContractTypeController::class, 'create'])->name('create');
        Route::post('/', [ContractTypeController::class, 'store'])->name('store');
        Route::get('/{contractType}', [ContractTypeController::class, 'show'])->name('show');
        Route::get('/{contractType}/edit', [ContractTypeController::class, 'edit'])->name('edit');
        Route::put('/{contractType}', [ContractTypeController::class, 'update'])->name('update');
        Route::delete('/{contractType}', [ContractTypeController::class, 'destroy'])->name('destroy');

        // Additional management actions
        Route::post('/{contractType}/toggle-status', [ContractTypeController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{contractType}/duplicate', [ContractTypeController::class, 'duplicate'])->name('duplicate');
        Route::post('/update-order', [ContractTypeController::class, 'updateOrder'])->name('update-order');
        Route::post('/bulk-action', [ContractTypeController::class, 'bulkAction'])->name('bulk-action');

        // Related content
        Route::get('/{contractType}/templates', [ContractTypeController::class, 'templates'])->name('templates');

        // AJAX utility routes
        Route::get('/ajax/active-types', [ContractTypeController::class, 'getActiveTypes'])->name('ajax.active-types');
        Route::get('/ajax/statistics', [ContractTypeController::class, 'statistics'])->name('ajax.statistics');
    });

    // Contract Templates Management
    Route::prefix('contract-templates')->name('contract-templates.')->group(function () {

        // Main CRUD routes
        Route::get('/', [ContractTemplateController::class, 'index'])->name('index');
        Route::get('/create', [ContractTemplateController::class, 'create'])->name('create');
        Route::post('/', [ContractTemplateController::class, 'store'])->name('store');
        Route::get('/{contractTemplate}', [ContractTemplateController::class, 'show'])->name('show');
        Route::get('/{contractTemplate}/edit', [ContractTemplateController::class, 'edit'])->name('edit');
        Route::put('/{contractTemplate}', [ContractTemplateController::class, 'update'])->name('update');
        Route::delete('/{contractTemplate}', [ContractTemplateController::class, 'destroy'])->name('destroy');

        // Additional management actions
        Route::post('/{contractTemplate}/toggle-status', [ContractTemplateController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{contractTemplate}/duplicate', [ContractTemplateController::class, 'duplicate'])->name('duplicate');
        Route::post('/update-order', [ContractTemplateController::class, 'updateOrder'])->name('update-order');

        // Preview and export/import
        Route::get('/{contractTemplate}/preview', [ContractTemplateController::class, 'preview'])->name('preview');
        Route::get('/{contractTemplate}/export', [ContractTemplateController::class, 'export'])->name('export');
        Route::post('/import', [ContractTemplateController::class, 'import'])->name('import');

        // AJAX utility routes
        Route::get('/ajax/by-contract-type', [ContractTemplateController::class, 'getByContractType'])->name('ajax.by-contract-type');
        Route::post('/ajax/generate-settings', [ContractTemplateController::class, 'generateSettings'])->name('ajax.generate-settings');
        Route::post('/ajax/validate-name', [ContractTemplateController::class, 'validateName'])->name('ajax.validate-name');
        Route::get('/ajax/statistics', [ContractTemplateController::class, 'statistics'])->name('ajax.statistics');
    });
});
