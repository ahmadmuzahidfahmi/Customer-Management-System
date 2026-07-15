
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


Route::middleware('auth')->group(function () {
/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Customers
|--------------------------------------------------------------------------
*/

Route::get('/customers', [CustomerController::class, 'index'])
    ->name('customers');

Route::get('/customers/create', [CustomerController::class, 'create'])
    ->name('customers.create');

Route::post('/customers', [CustomerController::class, 'store'])
    ->name('customers.store');

Route::get('/customers/{id}', [CustomerController::class, 'show'])
    ->name('customers.show');

Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])
    ->name('customers.edit');

Route::put('/customers/{id}', [CustomerController::class, 'update'])
    ->name('customers.update');

Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])
    ->name('customers.destroy');

/*
|--------------------------------------------------------------------------
| Leads
|--------------------------------------------------------------------------
*/

Route::get('/leads/kanban', [KanbanController::class, 'index'])->name('leads.kanban');
Route::post('/leads/kanban/update-position', [KanbanController::class, 'updatePosition'])->name('leads.kanban.update');


Route::get('/leads', [LeadController::class, 'index'])
    ->name('leads');

Route::get('/leads/create', [LeadController::class, 'create'])
    ->name('leads.create');

Route::post('/leads', [LeadController::class, 'store'])
    ->name('leads.store');

Route::get('/leads/{id}', [LeadController::class, 'show'])
    ->name('leads.show');

Route::get('/leads/{id}/edit', [LeadController::class, 'edit'])
    ->name('leads.edit');

Route::put('/leads/{id}', [LeadController::class, 'update'])
    ->name('leads.update');

Route::delete('/leads/{id}', [LeadController::class, 'destroy'])
    ->name('leads.destroy');

Route::post('/leads/{id}/restore', [LeadController::class, 'restore'])
    ->name('leads.restore');

Route::delete('/leads/{id}/force-delete', [LeadController::class, 'forceDelete'])
    ->name('leads.forceDelete');

Route::get(
    '/recycle-bin/lead/{id}',
    [LeadController::class, 'showDeleted']
)->name('leads.trashed.show');

Route::get('/leads/board', [LeadController::class, 'board'])->name('leads.board');

Route::post('/leads/{lead}/move', [LeadController::class, 'move'])->name('leads.move');

/*
|--------------------------------------------------------------------------
| Contacts
|--------------------------------------------------------------------------
*/

Route::get('/contacts', [ContactController::class, 'index'])
    ->name('contacts');

Route::get('/contacts/create', [ContactController::class, 'create'])
    ->name('contacts.create');

Route::post('/contacts', [ContactController::class, 'store'])
    ->name('contacts.store');

Route::get('/contacts/{id}', [ContactController::class, 'show'])
    ->name('contacts.show');

Route::get('/contacts/{id}/edit', [ContactController::class, 'edit'])
    ->name('contacts.edit');

Route::put('/contacts/{id}', [ContactController::class, 'update'])
    ->name('contacts.update');

Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])
    ->name('contacts.destroy');

    Route::post('/contacts/{id}/restore',
    [ContactController::class, 'restore'])
    ->name('contacts.restore');

    Route::delete('/contacts/{id}/force-delete',
    [ContactController::class, 'forceDelete'])
    ->name('contacts.forceDelete');

Route::get(
    '/recycle-bin/contact/{id}',
    [ContactController::class, 'showDeleted']
)->name('contacts.trashed.show');

/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::get('/profile', function () {
    return view('profile');
})->name('profile');

Route::get('/recycle-bin', [CustomerController::class, 'recycleBin'])
    ->name('recycle-bin');

Route::post('/customers/{id}/restore',
    [CustomerController::class, 'restore'])
    ->name('customers.restore');

Route::delete('/customers/{id}/force-delete',
    [CustomerController::class, 'forceDelete'])
    ->name('customers.forceDelete');

Route::get(
    '/recycle-bin/customer/{id}',
    [CustomerController::class, 'showDeleted']
)->name('customers.trashed.show');

// Note 
Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->name('notes.destroy');
Route::put('/notes/{id}', [NoteController::class, 'update'])->name('notes.update');

});