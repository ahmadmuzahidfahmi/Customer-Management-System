<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notes')) {
            return; // Table already exists in this environment — nothing to do.
        }

        Schema::create('notes', function (Blueprint $table) {
            $table->id('Note_ID');
            $table->string('Subject')->nullable();
            $table->text('Content');
            $table->unsignedBigInteger('Company_ID')->nullable();
            $table->unsignedBigInteger('Contact_ID')->nullable();
            $table->unsignedBigInteger('Lead_ID')->nullable();
            $table->timestamp('Created_At')->nullable();
            $table->timestamp('Updated_At')->nullable();

            $table->index('Company_ID');
            $table->index('Contact_ID');
            $table->index('Lead_ID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};