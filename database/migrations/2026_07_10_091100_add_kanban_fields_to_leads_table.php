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
    Schema::table('leads', function (Blueprint $table) {
        $table->integer('Position')->default(0);
        $table->timestamp('Status_Changed_At')->nullable();
        $table->index('Status');
    });
}

public function down(): void
{
    Schema::table('leads', function (Blueprint $table) {
        $table->dropColumn(['Position', 'Status_Changed_At']);
        $table->dropIndex(['Status']);
    });
}
};
