<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReportExportController;
use App\Livewire\Bills\BillIndex;
use App\Livewire\Categories\CategoryIndex;
use App\Livewire\Dashboard;
use App\Livewire\Reports\ReportIndex;
use App\Livewire\Transactions\TransactionIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', Dashboard::class)->name('dashboard');

    // Transactions
    Route::get('/transactions', TransactionIndex::class)->name('transactions.index');

    // Categories
    Route::get('/categories', CategoryIndex::class)->name('categories.index');

    // Bills
    Route::get('/bills', BillIndex::class)->name('bills.index');

    // Reports
    Route::get('/reports', ReportIndex::class)->name('reports.index');
    Route::get('/reports/export/excel', [ReportExportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportExportController::class, 'exportPdf'])->name('reports.export.pdf');
});
