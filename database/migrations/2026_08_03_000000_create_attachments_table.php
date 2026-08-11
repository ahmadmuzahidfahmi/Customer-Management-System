<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id('Attachment_ID');
            $table->string('Entity_Type');
            $table->unsignedBigInteger('Entity_ID');
            $table->string('Original_Name');
            $table->string('Stored_Name');
            $table->string('File_Path');
            $table->string('File_Type')->nullable();
            $table->unsignedBigInteger('File_Size')->nullable();
            $table->unsignedBigInteger('Uploaded_By')->nullable();
            $table->timestamp('Created_At')->nullable();
            $table->timestamp('Updated_At')->nullable();

            $table->index(['Entity_Type', 'Entity_ID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};