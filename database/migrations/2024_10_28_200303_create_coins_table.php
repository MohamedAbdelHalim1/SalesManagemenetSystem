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
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->integer('coin_0_5')->default(0);  
            $table->integer('coin_1')->default(0);    
            $table->integer('coin_10')->default(0);   
            $table->integer('coin_20')->default(0);   
            $table->integer('coin_50')->default(0);   
            $table->integer('coin_100')->default(0);  
            $table->integer('coin_200')->default(0);  
            $table->unsignedBigInteger('open_close_id');
            $table->foreign('open_close_id')->references('id')->on('open_closes')->onDelete('cascade');
            $table->string('money_shortage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins');
    }
};
