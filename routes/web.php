<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\LinemanController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::get('/tariffs', [AdminController::class, 'tariffs'])->name('tariffs');
    Route::post('/tariffs', [AdminController::class, 'storeTariff'])->name('tariffs.store');
    Route::get('/subsidies', [AdminController::class, 'subsidySchemes'])->name('subsidies');
    Route::post('/subsidies', [AdminController::class, 'storeSubsidyScheme'])->name('subsidies.store');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit_logs');
    Route::get('/export', [AdminController::class, 'exportReport'])->name('export');
});

Route::middleware(['auth', 'role:sdo'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');
    Route::post('/connection/{id}/approve', [OfficerController::class, 'approveConnection'])->name('connection.approve');
    Route::post('/connection/{id}/reject', [OfficerController::class, 'rejectConnection'])->name('connection.reject');
    Route::post('/complaint/{id}/assign', [OfficerController::class, 'assignComplaint'])->name('complaint.assign');
    Route::post('/complaint/{id}/resolve', [OfficerController::class, 'resolveComplaint'])->name('complaint.resolve');
    Route::post('/reading/{id}/verify', [OfficerController::class, 'verifyReading'])->name('reading.verify');
    Route::post('/generate-bills', [OfficerController::class, 'generateBills'])->name('generate_bills');
    Route::post('/subsidy/{id}/approve', [OfficerController::class, 'approveSubsidy'])->name('subsidy.approve');
    Route::post('/subsidy/{id}/reject', [OfficerController::class, 'rejectSubsidy'])->name('subsidy.reject');
    Route::post('/schedule', [OfficerController::class, 'storeSchedule'])->name('schedule.store');
    Route::delete('/schedule/{id}', [OfficerController::class, 'deleteSchedule'])->name('schedule.delete');
});

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [FarmerController::class, 'dashboard'])->name('dashboard');
    Route::get('/connections', [FarmerController::class, 'connections'])->name('connections');
    Route::post('/connection', [FarmerController::class, 'storeConnection'])->name('connection.store');
    Route::get('/bills', [FarmerController::class, 'bills'])->name('bills');
    Route::get('/bills/{id}/pay-confirm', [FarmerController::class, 'payConfirm'])->name('bill.pay.confirm');
    Route::post('/bills/{id}/pay', [FarmerController::class, 'payBill'])->name('bill.pay');
    Route::get('/bills/{id}/download', [FarmerController::class, 'downloadBill'])->name('bill.download');
    Route::get('/usage', [FarmerController::class, 'usage'])->name('usage');
    Route::get('/usage/chart-data', [FarmerController::class, 'usageChart'])->name('usage.chart');
    Route::get('/complaints', [FarmerController::class, 'complaints'])->name('complaints');
    Route::post('/complaint', [FarmerController::class, 'storeComplaint'])->name('complaint.store');
    Route::get('/subsidies', [FarmerController::class, 'subsidies'])->name('subsidies');
    Route::post('/subsidy/apply', [FarmerController::class, 'applySubsidy'])->name('subsidy.apply');
    Route::get('/help', [FarmerController::class, 'help'])->name('help');
    Route::get('/profile', [FarmerController::class, 'profile'])->name('profile');
    Route::post('/profile', [FarmerController::class, 'updateProfile'])->name('profile.update');
});

Route::middleware(['auth', 'role:lineman'])->prefix('lineman')->name('lineman.')->group(function () {
    Route::get('/dashboard', [LinemanController::class, 'dashboard'])->name('dashboard');
    Route::post('/reading', [LinemanController::class, 'storeReading'])->name('reading.store');
    Route::post('/complaint/{id}', [LinemanController::class, 'updateComplaint'])->name('complaint.update');
});
