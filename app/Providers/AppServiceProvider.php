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

class AppServiceProvider extends ServiceProvider
{


public function boot(): void
{
    Customer::observe(CustomerObserver::class);

    Relation::morphMap([
        'Contacts' => Contact::class,
        'Company'  => Customer::class,
        'Leads'    => Leads::class,
        'Activity' => Activity::class,
        'Notes'    => Note::class,
    ]);
}
}