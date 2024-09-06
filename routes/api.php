<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/isadmin', [UserController::class, 'isAdmin'])->name('users.isadmin');

    Route::get('/connectedUser', [UserController::class, 'connectedUser']);
    
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy'); 


    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

    Route::get('/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');

    Route::post('/transactions', [TransactionController::class, 'create'])->name('transaction.create');

    Route::put('/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');

    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    

    Route::get('/earnings', [TransactionController::class, 'getEarnings']);

    Route::get('/earnings-sum', [TransactionController::class, 'getEarningsSum']);


    Route::get('/expenses', [TransactionController::class, 'getExpenses']);

    Route::get('/expenses-sum', [TransactionController::class, 'getExpensesSum']);

    Route::get('/total-sum', [TransactionController::class, 'getTotalSum']);



    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});