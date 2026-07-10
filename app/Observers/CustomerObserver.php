<?php

namespace App\Observers;

use App\Models\Customer;

class CustomerObserver
{
    public function deleting(Customer $customer): void
    {
        // Soft delete related leads and contacts when the customer is soft deleted
        $customer->leads()->delete();
        $customer->contacts()->delete();
    }

    public function restoring(Customer $customer): void
    {
        // Restore related leads and contacts when the customer is restored
        $customer->leads()->onlyTrashed()->restore();
        $customer->contacts()->onlyTrashed()->restore();
    }
}