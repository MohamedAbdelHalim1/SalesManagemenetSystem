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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_collection');
            $table->string('order_number');
            $table->string('order_delivered');
            $table->decimal('total_cash', 10, 2); 
            $table->decimal('sales_commission', 10, 2); 
            $table->decimal('total_remaining', 10, 2); 
            $table->unsignedBigInteger('user_id'); //sales id
            $table->unsignedBigInteger('open_close_id'); //accounting id who open and close day
            $table->timestamps();

            // Foreign key constraint for user_id if you have a users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); //sales his id come from form
            $table->foreign('open_close_id')->references('id')->on('open_closes')->onDelete('cascade'); //accounting who has session

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
