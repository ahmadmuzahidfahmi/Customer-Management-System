<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('Log_ID');
            $table->unsignedBigInteger('User_ID')->nullable();
            $table->string('Action'); // created, updated, deleted, restored, force_deleted, viewed, login, logout
            $table->string('Auditable_Type')->nullable(); // Customer, Contact, Lead, Note, Auth, Page
            $table->unsignedBigInteger('Auditable_ID')->nullable();
            $table->string('Description')->nullable();
            $table->json('Old_Values')->nullable();
            $table->json('New_Values')->nullable();
            $table->string('IP_Address')->nullable();
            $table->string('User_Agent')->nullable();
            $table->timestamp('Created_At')->nullable();

            $table->index(['Auditable_Type', 'Auditable_ID']);
            $table->index('Action');
            $table->index('User_ID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};