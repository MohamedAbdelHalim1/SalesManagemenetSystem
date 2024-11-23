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
        Schema::table('coins', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['open_close_id']);
            // Drop the open_close_id column
            $table->dropColumn('open_close_id');
            // Add the new transaction_id column
            $table->unsignedBigInteger('transaction_id')->after('id');
            // Add the new foreign key constraint
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coins', function (Blueprint $table) {
            // Drop the foreign key constraint for transaction_id
            $table->dropForeign(['transaction_id']);
            // Drop the transaction_id column
            $table->dropColumn('transaction_id');
            // Add the open_close_id column back
            $table->unsignedBigInteger('open_close_id')->after('id');
            // Add the foreign key constraint back for open_close_id
            $table->foreign('open_close_id')->references('id')->on('open_closes')->onDelete('cascade');
        });
    }
};
