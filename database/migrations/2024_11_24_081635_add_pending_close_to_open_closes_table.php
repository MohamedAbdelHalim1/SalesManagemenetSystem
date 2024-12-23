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
        Schema::table('open_closes', function (Blueprint $table) {
            $table->boolean('pending_close')->default(false)->after('close_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_closes', function (Blueprint $table) {
            $table->dropColumn('pending_close');
        });
    }
};
