<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_daily_calendar_id')->constrained('gym_daily_calendars')->onDelete('cascade');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_quota')->default(20);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_slots');
    }
};
