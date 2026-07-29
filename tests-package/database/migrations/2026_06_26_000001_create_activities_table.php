<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id('Activity_ID');
            $table->unsignedBigInteger('User_ID')->nullable();
            $table->unsignedBigInteger('Company_ID')->nullable();
            $table->unsignedBigInteger('Contact_ID')->nullable();
            $table->unsignedBigInteger('Lead_ID')->nullable();
            $table->unsignedBigInteger('Assigned_To')->nullable();
            $table->string('Activity_Type');
            $table->string('Subject');
            $table->text('Activity_Detail')->nullable();
            $table->string('Status')->nullable();
            $table->timestamp('Dead_Line')->nullable();
            $table->timestamp('Completed_At')->nullable();
            $table->timestamp('Created_At')->nullable();
            $table->timestamp('Updated_At')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
