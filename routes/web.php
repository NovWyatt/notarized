<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SpecialistController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('welcome');
});


// Routes dành riêng cho Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::get('/system-settings', [AdminController::class, 'systemSettings'])->name('admin.settings');
});

// Routes dành cho Specialist (và Admin cũng có thể truy cập)
Route::middleware(['auth', 'role:specialist|admin'])->prefix('specialist')->group(function () {
    Route::get('/dashboard', [SpecialistController::class, 'dashboard'])->name('specialist.dashboard');
    Route::get('/reports', [SpecialistController::class, 'reports'])->name('specialist.reports');
});

// Routes chỉ dành cho Admin (không cho Specialist)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/system-logs', [AdminController::class, 'systemLogs'])->name('admin.logs');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
