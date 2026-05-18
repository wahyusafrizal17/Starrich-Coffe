<?php

use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
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
        Route::post('/kasir/open-bills/{transaction}/pay', [CashierController::class, 'payOpenBill'])->name('cashier.open-bills.pay');
        Route::get('/kasir/history', [CashierController::class, 'history'])->name('cashier.history');
        Route::get('/kasir/struk/{transaction}', [CashierController::class, 'invoice'])->name('cashier.invoice');
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('pengeluaran', ExpenseController::class)
            ->parameters(['pengeluaran' => 'expense'])
            ->names('expenses')
            ->except(['show']);
        Route::resource('aset', AssetController::class)
            ->parameters(['aset' => 'asset'])
            ->names('assets')
            ->except(['show']);

        Route::get('laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('laporan/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('laporan/laba-rugi', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
