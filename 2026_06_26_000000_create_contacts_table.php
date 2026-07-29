<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id('Contact_ID');
            $table->string('Contact_Name');
            $table->string('Contact_No')->nullable();
            $table->string('Contact_Role')->nullable();
            $table->string('Contact_Email')->nullable();
            $table->text('Contact_Note')->nullable();
            $table->unsignedBigInteger('Company_ID')->nullable();
            $table->unsignedBigInteger('User_ID')->nullable();
            $table->string('Country_Code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
