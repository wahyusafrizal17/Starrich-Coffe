<?php

use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CapitalInflowController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\OrderAddonController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('admin')
        ->name('dashboard');

    Route::middleware('staff')->group(function () {
        Route::get('/kasir', [CashierController::class, 'index'])->name('cashier.index');
        Route::post('/kasir/checkout', [CashierController::class, 'checkout'])->name('cashier.checkout');
        Route::get('/kasir/open-bills', [CashierController::class, 'openBillsPage'])->name('cashier.open-bills');
        Route::get('/kasir/open-bills/data', [CashierController::class, 'openBills'])->name('cashier.open-bills.data');
        Route::get('/kasir/open-bills/{transaction}/edit-data', [CashierController::class, 'openBillEditData'])->name('cashier.open-bills.edit-data');
        Route::put('/kasir/open-bills/{transaction}', [CashierController::class, 'updateOpenBill'])->name('cashier.open-bills.update');
        Route::post('/kasir/open-bills/{transaction}/pay', [CashierController::class, 'payOpenBill'])->name('cashier.open-bills.pay');
        Route::delete('/kasir/open-bills/{transaction}', [CashierController::class, 'destroyOpenBill'])->name('cashier.open-bills.destroy');
        Route::get('/kasir/history', [CashierController::class, 'history'])->name('cashier.history');
        Route::patch('/kasir/history/{transaction}/payment', [CashierController::class, 'updateTransactionPayment'])->name('cashier.history.update-payment');
        Route::get('/kasir/struk/{transaction}', [CashierController::class, 'invoice'])->name('cashier.invoice');
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show', 'create', 'edit']);
        Route::resource('order-addons', OrderAddonController::class)->except(['show', 'create', 'edit']);
        Route::resource('products', ProductController::class)->except(['show', 'create', 'edit']);
        Route::resource('diskon', DiscountController::class)
            ->parameters(['diskon' => 'discount'])
            ->names('discounts')
            ->except(['show', 'create', 'edit']);
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
        Route::resource('pengeluaran', ExpenseController::class)
            ->parameters(['pengeluaran' => 'expense'])
            ->names('expenses')
            ->except(['show', 'create', 'edit']);
        Route::resource('pemasukan', CapitalInflowController::class)
            ->parameters(['pemasukan' => 'inflow'])
            ->names('inflows')
            ->except(['show', 'create', 'edit']);
        Route::resource('aset', AssetController::class)
            ->parameters(['aset' => 'asset'])
            ->names('assets')
            ->except(['show', 'create', 'edit']);

        Route::get('laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('laporan/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('laporan/laba-rugi', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');

        Route::put('pengaturan', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('pengaturan', fn () => redirect()->route('profile.edit'));
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
