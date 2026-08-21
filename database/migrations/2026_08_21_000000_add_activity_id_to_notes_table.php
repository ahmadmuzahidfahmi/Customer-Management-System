<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->unsignedBigInteger('Activity_ID')->nullable()->after('Lead_ID');
            $table->index('Activity_ID');
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex(['Activity_ID']);
            $table->dropColumn('Activity_ID');
        });
    }
};
