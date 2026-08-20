<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Contact;
use App\Models\Leads;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Creates 50 customers, each with a random number of contacts (1-5)
     * and a random number of leads (0-6). Leads are optionally linked to
     * one of that same customer's own contacts, and assigned to an
     * existing user so relationships stay realistic instead of orphaned.
     */
    public function run(): void
    {
        // Leads reference User_ID - reuse existing users instead of
        // creating new ones, so this seeder plays nicely with whichever
        // users already exist (e.g. from UserSeeder).
        $userIds = User::pluck('User_ID');

        $leadStatuses = ['New', 'Contacted', 'Qualified', 'Won', 'Lost'];
        $leadSources = ['Referral', 'Website', 'Cold Call', 'Event'];

        Customer::factory()
            ->count(50)
            ->create()
            ->each(function (Customer $customer) use ($userIds, $leadStatuses, $leadSources) {
                $contacts = Contact::factory()
                    ->count(rand(1, 5))
                    ->create(['Company_ID' => $customer->Company_ID]);

                $leadCount = rand(0, 6);

                for ($i = 0; $i < $leadCount; $i++) {
                    Leads::factory()->create([
                        'Company_ID' => $customer->Company_ID,
                        'Contact_ID' => $contacts->isNotEmpty()
                            ? $contacts->random()->Contact_ID
                            : null,
                        'Status' => $leadStatuses[array_rand($leadStatuses)],
                        'Source' => $leadSources[array_rand($leadSources)],
                        'User_ID' => $userIds->isNotEmpty() ? $userIds->random() : null,
                    ]);
                }
            });
    }
}