<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No-op: soft deletes are now created directly in the
        // create_customers_table migration (which creates 'company').
        // Left in place (rather than deleted) since it's already
        // recorded as run in production.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
