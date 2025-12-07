<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\VibeWallController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Cashier\DashboardController as CashierDashboardController;
use App\Http\Controllers\Cashier\EmpathyRadarController;
use App\Http\Controllers\Cashier\FlashSaleController;
use App\Http\Controllers\Cashier\MenuController as CashierMenuController;
use App\Http\Controllers\Cashier\OrderController;
use App\Http\Controllers\Customer\AiController as CustomerAiController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Customer\HomeController as CustomerHomeController;
use App\Http\Controllers\Customer\MenuController as CustomerMenuController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\VibeWallController as CustomerVibeWallController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page Routes
|--------------------------------------------------------------------------
| Public routes untuk landing page yang bisa diakses tanpa login
*/

Route::controller(LandingController::class)->group(function () {
    Route::get('/', 'home')->name('landing.home');
    Route::get('/menu', 'menu')->name('landing.menu');
    Route::get('/vibewall', 'vibewall')->name('landing.vibewall');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
| Routes untuk login, register, dan logout
*/

Route::middleware('guest')->group(function () {
    // Customer Quick Access (Main Login - tanpa password)
    Route::get('/login', [AuthController::class, 'showCustomerAccess'])->name('login');
    Route::post('/login', [AuthController::class, 'customerAccess'])->name('customer.access');

    // Staff Login (Admin/Cashier) - Secondary
    Route::get('/staff/login', [AuthController::class, 'showLogin'])->name('staff.login');
    Route::post('/staff/login', [AuthController::class, 'login']);
});

// Logout (untuk semua authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard redirect berdasarkan role
Route::get('/dashboard-redirect', function () {
    $user = auth()->user();

    if ($user?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }

    if ($user?->isCashier()) {
        return redirect()->route('cashier.dashboard');
    }

    // Customer atau guest
    return redirect()->route('customer.home');
})->name('dashboard.redirect');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| Routes yang hanya bisa diakses oleh admin
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Staff Management (CRUD Kasir)
        Route::resource('staff', StaffController::class)->except(['show']);

        // Menu Management
        Route::resource('menu', AdminMenuController::class)->except(['show']);
        Route::patch('/menu/{menu}/toggle', [AdminMenuController::class, 'toggleAvailability'])->name('menu.toggle');

        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index'])->name('index');
            Route::get('/{order}', [AdminOrderController::class, 'show'])->name('show');
            Route::patch('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('status');
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
            Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        });

        // Analytics (Mood Analytics)
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

        // Vibe Wall Moderation
        Route::prefix('vibewall')->name('vibewall.')->group(function () {
            Route::get('/', [VibeWallController::class, 'index'])->name('index');
            Route::get('/pending', [VibeWallController::class, 'pending'])->name('pending');
            Route::patch('/{entry}/approve', [VibeWallController::class, 'approve'])->name('approve');
            Route::delete('/{entry}', [VibeWallController::class, 'reject'])->name('reject');
            Route::patch('/{entry}/featured', [VibeWallController::class, 'toggleFeatured'])->name('featured');
            Route::post('/{entry}/analyze', [VibeWallController::class, 'analyze'])->name('analyze');
        });
    });

/*
|--------------------------------------------------------------------------
| Cashier Routes
|--------------------------------------------------------------------------
| Routes yang hanya bisa diakses oleh kasir
*/

Route::prefix('cashier')
    ->name('cashier.')
    ->middleware(['auth', 'role:cashier'])
    ->group(function () {
        Route::get('/dashboard', [CashierDashboardController::class, 'index'])->name('dashboard');

        // Menu Management (CRUD Menu Items)
        Route::resource('menu', CashierMenuController::class)->except(['show']);
        Route::patch('/menu/{menu}/toggle', [CashierMenuController::class, 'toggleAvailability'])->name('menu.toggle');

        // Order Management
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/pending', [OrderController::class, 'pending'])->name('pending');
            Route::get('/preparing', [OrderController::class, 'preparing'])->name('preparing');
            Route::get('/completed', [OrderController::class, 'completed'])->name('completed');
            Route::get('/create', [OrderController::class, 'create'])->name('create');
            Route::post('/', [OrderController::class, 'store'])->name('store');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::post('/{order}/payment', [OrderController::class, 'processPayment'])->name('payment');
            Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('status');
            Route::delete('/{order}', [OrderController::class, 'cancel'])->name('cancel');
        });

        // Flash Sale Management
        Route::prefix('flashsale')->name('flashsale.')->group(function () {
            Route::get('/', [FlashSaleController::class, 'index'])->name('index');
            Route::get('/create', [FlashSaleController::class, 'create'])->name('create');
            Route::post('/', [FlashSaleController::class, 'store'])->name('store');
            Route::patch('/{flashsale}/end', [FlashSaleController::class, 'end'])->name('end');
        });

        // Empathy Radar (Sentiment Analysis)
        Route::prefix('empathy')->name('empathy.')->group(function () {
            Route::get('/', [EmpathyRadarController::class, 'index'])->name('index');
            Route::get('/create', [EmpathyRadarController::class, 'create'])->name('create');
            Route::post('/', [EmpathyRadarController::class, 'store'])->name('store');
            Route::patch('/{interaction}/handle', [EmpathyRadarController::class, 'handle'])->name('handle');
        });
    });

/*
|--------------------------------------------------------------------------
| Customer Routes (Protected - Butuh Customer Session)
|--------------------------------------------------------------------------
| Semua routes di bawah ini membutuhkan customer sudah login (isi nama & email)
| Untuk melihat menu tanpa login, gunakan /menu (landing.menu)
*/

Route::prefix('order')
    ->name('customer.')
    ->middleware('customer.session')
    ->group(function () {
        // Home page dengan AI Chat
        Route::get('/', [CustomerHomeController::class, 'index'])->name('home');

        // Menu Browsing (untuk customer yang sudah login)
        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/', [CustomerMenuController::class, 'index'])->name('index');
            Route::get('/{slug}', [CustomerMenuController::class, 'show'])->name('show');
        });

        // Cart
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CustomerCartController::class, 'index'])->name('index');
            Route::post('/add', [CustomerCartController::class, 'add'])->name('add');
            Route::post('/update', [CustomerCartController::class, 'update'])->name('update');
            Route::post('/remove', [CustomerCartController::class, 'remove'])->name('remove');
            Route::get('/clear', [CustomerCartController::class, 'clear'])->name('clear');
            Route::get('/count', [CustomerCartController::class, 'count'])->name('count');
        });

        // Orders
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::get('/', [CustomerOrderController::class, 'index'])->name('index');
            Route::get('/checkout', [CustomerOrderController::class, 'checkout'])->name('checkout');
            Route::post('/', [CustomerOrderController::class, 'store'])->name('store');
            Route::get('/{orderNumber}', [CustomerOrderController::class, 'show'])->name('show');
        });

        // Vibe Wall
        Route::prefix('vibewall')->name('vibewall.')->group(function () {
            Route::get('/', [CustomerVibeWallController::class, 'index'])->name('index');
            Route::get('/create', [CustomerVibeWallController::class, 'create'])->name('create');
            Route::post('/', [CustomerVibeWallController::class, 'store'])->name('store');
        });

        // AI Chat Demo
        Route::get('/ai-demo', function () {
            return view('customer.ai-demo');
        })->name('ai.demo');
    });
