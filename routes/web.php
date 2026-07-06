<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;

// Dashboard route
Route::get('/', [DashboardController::class, 'index'])->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Customer routes

Route::get('/customers', [CustomerController::class, 'index'])
    ->name('customers');

Route::get('/customers/create', [CustomerController::class, 'create'])
    ->name('customers.create');

Route::post('/customers', [CustomerController::class, 'store'])
    ->name('customers.store');

Route::get('/customers', [CustomerController::class, 'index'])->name('customers');


Route::get('/leads', [LeadController::class, 'index'])->name('leads');


Route::get('/profile', function () {
    return view('profile');
})->name('profile');

Route::get('/customers/{id}', [CustomerController::class, 'show'])
    ->name('customers.show');