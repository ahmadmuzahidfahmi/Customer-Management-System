<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No-op: Position and Status_Changed_At are now created directly
        // in the create_leads_table migration. This migration duplicated
        // 2026_07_10_091100_add_kanban_fields_to_leads_table.php and
        // would error if both ran against a fresh database (column
        // already exists). Left in place since it's already recorded
        // as run in production.
    }

    public function down(): void
    {
        //
    }
};