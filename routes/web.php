<?php

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

    Route::resource('litigants', LitigantController::class);

    // Additional routes if needed
    Route::get('litigants/{litigant}/addresses', [LitigantController::class, 'addresses'])
        ->name('litigants.addresses');

});
Route::get('/api/search-litigants', [LitigantController::class, 'searchLitigants'])->name('search.litigants');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
