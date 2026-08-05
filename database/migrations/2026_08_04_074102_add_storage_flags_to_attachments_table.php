<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->boolean('Is_On_Local')->default(true)->after('File_Size');
            $table->boolean('Is_On_Drive')->default(true)->after('Is_On_Local');
        });
    }

    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn(['Is_On_Local', 'Is_On_Drive']);
        });
    }
};