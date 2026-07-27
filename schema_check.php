<?php

foreach (['users', 'company', 'contacts', 'leads', 'activities'] as $t) {
    echo "=== {$t} ===" . PHP_EOL;
    print_r(Illuminate\Support\Facades\Schema::getColumnListing($t));
}