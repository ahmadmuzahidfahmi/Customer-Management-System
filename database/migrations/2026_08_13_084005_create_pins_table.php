<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pins', function (Blueprint $table) {

            $table->id('Pin_ID');

            $table->unsignedBigInteger('User_ID');

            $table->string('Entity_Type');

            $table->unsignedBigInteger('Entity_ID');

            $table->timestamps();

            $table->unique([
                'User_ID',
                'Entity_Type',
                'Entity_ID'
            ]);

            $table->foreign('User_ID')
                ->references('User_ID')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pins');
    }
};