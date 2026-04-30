<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\OfficerController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth', 'role:officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');
    Route::post('/connection/{id}/approve', [OfficerController::class, 'approveConnection'])->name('connection.approve');
    Route::post('/complaint/{id}/resolve', [OfficerController::class, 'resolveComplaint'])->name('complaint.resolve');
});

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerController::class, 'dashboard'])->name('dashboard');
    Route::post('/connection', [FarmerController::class, 'storeConnection'])->name('connection.store');
    Route::post('/complaint', [FarmerController::class, 'storeComplaint'])->name('complaint.store');
});
