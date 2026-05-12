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

Route::get('/health', function () {
    $results = [];
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $results['database'] = 'Connected';
    } catch (\Exception $e) {
        $results['database'] = 'Error: ' . $e->getMessage();
    }
    
    $results['storage'] = is_writable(storage_path()) ? 'Writable' : 'Not Writable';
    $results['session_driver'] = config('session.driver');
    $results['app_env'] = config('app.env');
    $results['app_debug'] = config('app.debug');
    
    return response()->json($results);
});

Route::get('/health/db-fix', function () {
    $output = "";
    try {
        // Manually ensure the state column exists in zones table
        if (!\Illuminate\Support\Facades\Schema::hasColumn('zones', 'state')) {
            \Illuminate\Support\Facades\Schema::table('zones', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('state')->default('Rajasthan')->after('name');
            });
            $output .= "Added 'state' column to 'zones' table.\n";
        } else {
            $output .= "'state' column already exists in 'zones' table.\n";
        }

        // Manually ensure avatar column exists in users table
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'avatar')) {
            \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('avatar')->nullable()->after('password');
            });
            $output .= "Added 'avatar' column to 'users' table.\n";
        }
        
        // Manually ensure document_path column exists in consumer_subsidies table
        if (!\Illuminate\Support\Facades\Schema::hasColumn('consumer_subsidies', 'document_path')) {
            \Illuminate\Support\Facades\Schema::table('consumer_subsidies', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('document_path')->nullable();
            });
            $output .= "Added 'document_path' column to 'consumer_subsidies' table.\n";
        }

        // Run migrations
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $output .= \Illuminate\Support\Facades\Artisan::output();
        
        return "<pre>DB Fix run successfully.\nOutput:\n" . htmlspecialchars($output) . "</pre>";
    } catch (\Exception $e) {
        return "<pre>DB Fix encountered an error.\nError:\n" . htmlspecialchars($e->getMessage()) . "\n\nPartial Output:\n" . htmlspecialchars($output) . "</pre>";
    }
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{id}/toggle', [AdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::get('/tariffs', [AdminController::class, 'tariffs'])->name('tariffs');
    Route::post('/tariffs', [AdminController::class, 'storeTariff'])->name('tariff.store');
    Route::patch('/tariffs/{id}', [AdminController::class, 'updateTariff'])->name('tariff.update');
    Route::delete('/tariffs/{id}', [AdminController::class, 'deleteTariff'])->name('tariff.delete');
    Route::get('/subsidies', [AdminController::class, 'subsidySchemes'])->name('subsidies');
    Route::post('/subsidies', [AdminController::class, 'storeSubsidyScheme'])->name('subsidy.store');
    Route::patch('/subsidies/{id}', [AdminController::class, 'updateSubsidyScheme'])->name('subsidy.update');
    Route::delete('/subsidies/{id}', [AdminController::class, 'deleteSubsidyScheme'])->name('subsidy.delete');
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit_logs');
    Route::get('/zones', [AdminController::class, 'zones'])->name('zones');
    Route::post('/zones', [AdminController::class, 'storeZone'])->name('zones.store');
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
