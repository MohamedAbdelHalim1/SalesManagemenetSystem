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
            $table->boolean('is_done')->default(false)->after('close_at'); // To indicate whether the day is done
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('open_closes', function (Blueprint $table) {
            $table->dropColumn(['is_done']);
        });
    }
};
