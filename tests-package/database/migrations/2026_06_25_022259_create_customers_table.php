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
        Schema::create('company', function (Blueprint $table) {
            $table->id('Company_ID');
            $table->string('Company_Name');
            $table->string('Company_No')->nullable();
            $table->string('Company_Email')->nullable();
            $table->unsignedBigInteger('Note_ID')->nullable();
            $table->string('Status')->nullable();
            $table->timestamp('Closed_Date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company');
    }
};
