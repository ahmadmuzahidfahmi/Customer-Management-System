<?php

// Marks the 3 new migrations as already run, since the tables they
// "create" already exist in the real database. This prevents
// `php artisan migrate` from trying to CREATE TABLE on something
// that's already there.

$batch = \Illuminate\Support\Facades\DB::table('migrations')->max('batch') + 1;

$migrations = [
    '2026_06_26_000000_create_contacts_table',
    '2026_06_26_000001_create_activities_table',
    '2026_07_08_000000_create_leads_table',
];

foreach ($migrations as $migration) {
    $exists = \Illuminate\Support\Facades\DB::table('migrations')
        ->where('migration', $migration)
        ->exists();

    if ($exists) {
        echo "Already marked: {$migration}\n";
        continue;
    }

    \Illuminate\Support\Facades\DB::table('migrations')->insert([
        'migration' => $migration,
        'batch' => $batch,
    ]);

    echo "Marked as applied: {$migration}\n";
}

echo "Done. Run 'php artisan migrate:status' to confirm.\n";
