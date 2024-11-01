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
        Schema::create('open_closes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); //accounting id who open and close day
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('open_at')->nullable();
            $table->timestamp('close_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('open_closes');
    }
};
