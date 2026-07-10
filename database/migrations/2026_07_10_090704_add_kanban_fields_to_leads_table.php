<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('Position')->nullable();
            $table->timestamp('Status_Changed_At')->nullable();
            $table->string('Lost_Reason')->nullable();
            $table->index('Status');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['Position', 'Status_Changed_At', 'Lost_Reason']);
            $table->dropIndex(['Status']);
        });
    }
};