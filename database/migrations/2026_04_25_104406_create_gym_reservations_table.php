<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gym_slot_id')->constrained('gym_slots')->onDelete('cascade');
            $table->enum('status', ['confirmed', 'cancelled', 'waitlist'])->default('confirmed');
            $table->boolean('attended')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'gym_slot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_reservations');
    }
};
