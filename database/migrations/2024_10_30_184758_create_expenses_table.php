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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expenses_key')->nullable(); 
            $table->decimal('expenses_value', 10, 2)->default(0);
            $table->unsignedBigInteger('transaction_id'); //accounting id who open and close day
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
