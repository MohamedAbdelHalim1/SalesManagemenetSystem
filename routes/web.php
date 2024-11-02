<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;



Route::middleware('auth')->group(function () {
    Route::get('/' , [DashboardController::class , 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/users', [UserController::class, 'users'])->name('user.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/search-user', [UserController::class, 'searchUser']);
    Route::get('/roles', [UserController::class, 'roles'])->name('roles');
    Route::get('/roles/create', [UserController::class, 'create_role'])->name('roles.create');
    Route::post('/roles', [UserController::class, 'store_role'])->name('roles.store');
    Route::get('/roles/{role}', [UserController::class, 'show_role'])->name('roles.show');
    Route::get('/roles/{role}/edit', [UserController::class, 'edit_role'])->name('roles.edit');
    Route::put('/roles/{role}', [UserController::class, 'update_role'])->name('roles.update');
    Route::delete('/roles/{role}', [UserController::class, 'destroy_role'])->name('roles.destroy');
    Route::get('/transaction', [TransactionController::class, 'transaction'])->name('transaction.index');
    Route::get('/transaction/create', [TransactionController::class, 'create'])->name('transaction.create');
    Route::get('/transactions/open', [TransactionController::class, 'openTransaction'])->name('open_transaction');
    Route::get('/transactions/cancel', [TransactionController::class, 'cancelTransaction'])->name('cancel_transaction');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{open_close_id}/coins/{total_cash}', [TransactionController::class, 'coins'])->name('transactions.coins');
    Route::post('/transactions/{open_close_id}/coins', [TransactionController::class, 'storeCoins'])->name('transactions.storeCoins');
    Route::post('/transactions/close-day/{open_close_id}', [TransactionController::class, 'closeDay'])->name('transactions.closeDay');
    Route::get('/transactions/{id}/show', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/reports/{userId}', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/show/{id}', [ReportController::class, 'show'])->name('reports.show');

    Route::get('/reports/sales/{userId}', [ReportController::class, 'salesReport'])->name('reports.sales');
});

require __DIR__.'/auth.php';
