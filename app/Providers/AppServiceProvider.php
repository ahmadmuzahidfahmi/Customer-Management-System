<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Customer;
use App\Models\Contact;
use App\Models\Leads;
use App\Models\Activity;
use App\Models\Note;
use App\Observers\CustomerObserver;
use Illuminate\Support\Facades\Blade;
use App\Observers\ContactObserver;
use App\Observers\LeadObserver;
use Illuminate\Support\Facades\View;
use App\Models\Attachment;


class AppServiceProvider extends ServiceProvider
{


public function boot(): void
{
    Customer::observe(CustomerObserver::class);
    Contact::observe(ContactObserver::class);
    Leads::observe(LeadObserver::class);

    Relation::morphMap([
        'Contacts' => Contact::class,
        'Company'  => Customer::class,
        'Leads'    => Leads::class,
        'Activity' => Activity::class,
        'Notes'    => Note::class,
    ]);
    
    View::composer('app', function ($view) {
    $view->with('unsyncedCount', Attachment::where('Is_On_Local', false)->orWhere('Is_On_Drive', false)->count());});

    Blade::if('guestUser', function () {
        return auth()->check() && auth()->user()->User_Role === 'Guest';
    });
    }
}