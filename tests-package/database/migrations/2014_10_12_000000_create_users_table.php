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
        Schema::create('users', function (Blueprint $table) {
            $table->id('User_ID');
            $table->string('User_Name');
            $table->string('User_Role')->nullable();
            $table->string('User_Password');
            $table->string('User_Email')->nullable();
            $table->string('Status')->nullable();
            $table->timestamp('Last_Login')->nullable();
            $table->timestamp('Created_At')->nullable();
            $table->timestamp('Updated_At')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
