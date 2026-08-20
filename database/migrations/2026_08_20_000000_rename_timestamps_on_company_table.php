<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Customer model declares CREATED_AT = 'Created_At' and
     * UPDATED_AT = 'Updated_At', matching every other table in this app
     * (users, activities, notes, leads, audit_logs, attachments).
     *
     * The original create_customers_table migration used the default
     * $table->timestamps(), which created lowercase created_at/updated_at
     * instead. This mismatch causes Eloquent to read back an uncast raw
     * string for created_at/updated_at when the model is reloaded from
     * the database, which crashes anything calling ->toIso8601String()
     * on it (e.g. CustomerResource) with a 500 error.
     *
     * This migration brings the company table in line with the rest
     * of the schema.
     */
    public function up(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->renameColumn('created_at', 'Created_At');
            $table->renameColumn('updated_at', 'Updated_At');
        });
    }

    public function down(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->renameColumn('Created_At', 'created_at');
            $table->renameColumn('Updated_At', 'updated_at');
        });
    }
};