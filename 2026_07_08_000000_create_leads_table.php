<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id('Lead_ID');
            $table->string('Lead_Name');
            $table->string('Source')->nullable();
            $table->unsignedBigInteger('User_ID')->nullable();
            $table->unsignedBigInteger('Company_ID')->nullable();
            $table->unsignedBigInteger('Contact_ID')->nullable();
            $table->unsignedBigInteger('Note_ID')->nullable();
            $table->string('Status')->nullable();
            $table->decimal('Estimated_Value', 15, 2)->nullable();
            $table->integer('Position')->default(0);
            $table->timestamp('Status_Changed_At')->nullable();
            $table->timestamp('Created_At')->nullable();
            $table->timestamp('Updated_At')->nullable();
            $table->softDeletes();
            $table->index('Status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
