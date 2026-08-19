<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('company', 'Country_Code')) {
            return;
        }

        Schema::table('company', function (Blueprint $table) {
            $table->string('Country_Code', 10)->nullable()->default('+60')->after('Company_No');
        });
    }

    public function down(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn('Country_Code');
        });
    }
};