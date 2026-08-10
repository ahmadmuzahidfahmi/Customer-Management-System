<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\EmailController;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('guest');
Route::post('/login/guest', [AuthController::class, 'guestLogin'])->name('login.guest')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt')->middleware('guest');

Route::middleware(['auth', 'guest.readonly'])->group(function () {
Route::middleware('admin')->group(function () {
Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log');
});
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
Route::post('/customers/{id}/contacts', [CustomerController::class, 'addContact'])->name('customers.addContact');
Route::post('/customers/{id}/contacts/link', [CustomerController::class, 'linkContact'])->name('customers.linkContact');
Route::post('/customers/{id}/leads', [CustomerController::class, 'addLead'])->name('customers.addLead');
Route::post('/customers/{id}/leads/link', [CustomerController::class, 'linkLead'])->name('customers.linkLead');
Route::post('/customers/{id}/pin', [CustomerController::class, 'togglePin'] )->name('customers.pin');

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

Route::post('/contacts/{id}/restore',[ContactController::class, 'restore'])->name('contacts.restore');

Route::delete('/contacts/{id}/force-delete', [ContactController::class, 'forceDelete'])->name('contacts.forceDelete');

Route::get('/recycle-bin/contact/{id}',[ContactController::class, 'showDeleted'])->name('contacts.trashed.show');

Route::post('/contacts/{id}/pin',[ContactController::class, 'togglePin'])->name('contacts.pin');

Route::post('/contacts/{id}/leads', [ContactController::class, 'addLead'])->name('contacts.addLead');

Route::post('/contacts/{id}/leads/link', [ContactController::class, 'linkLead'])->name('contacts.linkLead');
/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

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

/*
|--------------------------------------------------------------------------
| Attachements
|--------------------------------------------------------------------------
*/
Route::get('/attachments/sync-status', [AttachmentController::class, 'syncStatus'])->name('attachments.syncStatus');
Route::post('/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
Route::post('/attachments/{id}/resync', [AttachmentController::class, 'resync'])->name('attachments.resync');
Route::delete('/attachments/bulk-destroy', [AttachmentController::class, 'bulkDestroy'])->name('attachments.bulkDestroy');
Route::get('/attachments/{id}', [AttachmentController::class, 'show'])->name('attachments.show');
Route::delete('/attachments/{id}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
Route::post('/attachments/verify-all', [AttachmentController::class, 'verifyAll'])->name('attachments.verifyAll');

// Note 
Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
Route::delete('/notes/bulk-destroy', [NoteController::class, 'bulkDestroy'])->name('notes.bulkDestroy');
Route::delete('/notes/{id}', [NoteController::class, 'destroy'])->name('notes.destroy');
Route::put('/notes/{id}', [NoteController::class, 'update'])->name('notes.update');

Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
Route::put('/activities/{id}', [ActivityController::class, 'update'])->name('activities.update');
Route::post('/activities/{id}/complete', [ActivityController::class, 'complete'])->name('activities.complete');
Route::post('/activities/{id}/cancel', [ActivityController::class, 'cancel'])->name('activities.cancel');
Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->name('activities.destroy');

Route::middleware('log.view')->group(function () {
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/contacts/{id}', [ContactController::class, 'show'])->name('contacts.show');
    Route::get('/leads/{id}', [LeadController::class, 'show'])->name('leads.show');
});
Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log');

Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');
Route::post('/calendar/reschedule', [CalendarController::class, 'reschedule'])->name('calendar.reschedule');
Route::post('/emails/send', [EmailController::class, 'send'])->name('emails.send');

Route::get('/calendar', [CalendarController::class, 'index'])
    ->name('calendar');

    Route::get('/activities/{id}',
    [ActivityController::class, 'show']
)->name('activities.show');

Route::get('/calendar/week',
    [CalendarController::class, 'week'])
    ->name('calendar.week');

Route::get('/calendar/month',
    [CalendarController::class, 'index'])
    ->name('calendar.month');
});