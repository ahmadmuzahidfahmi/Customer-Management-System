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
    // No-op: Position and Status_Changed_At are now created directly
    // in the create_leads_table migration. Left in place since it's
    // already recorded as run in production.
}

public function down(): void
{
    //
}
};
