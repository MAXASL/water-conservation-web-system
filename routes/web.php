

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterUsageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeakController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminReportsController;
use Illuminate\Http\Request;
use App\Models\HomeUsage;

Route::get('/check-configs', function () {
    foreach (glob(config_path('*.php')) as $file) {
        $config = require $file;
        if (!is_array($config)) {
            return "❌ Invalid config: " . basename($file) . " (Expected array, got " . gettype($config) . ")";
        }
    }
    return "✅ All config files return valid arrays.";
});


// Default route should show login/register page
Route::get('/', [AuthController::class, 'showLoginForm'])->name('auth');

// Handle registration & login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
// Add this route (keep all your existing routes)
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/index', [WaterUsageController::class, 'index'])->name('index');
    // other protected routes...
});
// Admin Dashboard Route
Route::get('/index', [WaterUsageController::class, 'index'])->name('index');

// Household Dashboard Route
Route::get('/homee', [HomeController::class, 'index0'])->name('homee');

// Routes for various pages (water usage, leak reporting, etc.)
Route::get('/alerts', [WaterUsageController::class, 'alerts'])->name('alerts');
Route::post('/send-alert/{user}', [WaterUsageController::class, 'sendAlert'])->name('send.alert');
Route::get('/incentives', [HomeController::class, 'incentives'])->name('incentives');

Route::get('/usage-pattern', [WaterUsageController::class, 'usagePattern'])->name('usage.pattern');
Route::get('/charts', [WaterUsageController::class, 'charts'])->name('charts');

// Routes for leak reports and setting goals
Route::get('/report-leak', [LeakController::class, 'create'])->name('leaks.create');
Route::post('/report-leak', [LeakController::class, 'store'])->name('leaks.store');
Route::get('/set-goal', [GoalController::class, 'create'])->name('goal.create');
Route::post('/set-goal', [GoalController::class, 'store'])->name('goals.store');
Route::get('/getProgressData', [HomeController::class, 'getProgressData'])->name('getProgressData');

// routes/web.php
    Route::get('leaks', [WaterUsageController::class, 'detectLeaks'])
         ->name('water.leaks');

// history routes
    Route::get('/history', [WaterUsageController::class, 'history'])->name('history');

// summary routes
// history routes
    Route::get('/summary', [HomeController::class, 'summary'])->name('summary');

//AJAX ENDPOINT ROUTE
Route::get('/update-usage', [HomeController::class, 'updateUsage'])->name('usage.update');

// testing real-time data
Route::get('/test-update-usage', [HomeController::class, 'updateUsage']);

// sensor controller
Route::post('/sensor', [HomeController::class, 'store']);
// For viewing latest record
Route::get('/sensor', [HomeController::class, 'showLatest']);

// Optional: to view recent 10 records
Route::get('/sensor/recent', [HomeController::class, 'showRecent']);

// The POST route (you probably already have this)
Route::post('/sensor/store', [HomeController::class, 'store']);

// report generation routes
Route::prefix('reports')->group(function() {
    Route::get('/', [ReportsController::class, 'index2'])->name('reports');
    Route::post('/generate', [ReportsController::class, 'generate'])->name('reports.generate');
});

// payment routes
Route::prefix('payments')->group(function () {
    Route::get('/{payment}', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('/{payment}/pay', [PaymentController::class, 'pay'])->name('payment.process');
    Route::get('/success', [PaymentController::class, 'success'])->name('payment.success');
});

// routes for the admin view of the reported leaks
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/reports', [AdminReportsController::class, 'index1'])->name('admin.reports');
    // ... other routes
});







