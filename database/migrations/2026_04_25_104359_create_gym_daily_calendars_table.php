<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_daily_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_branch_id')->constrained('gym_branches')->onDelete('cascade');
            $table->date('date');
            $table->boolean('is_holiday')->default(false);
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->integer('slot_duration_minutes')->nullable();
            $table->timestamps();

            // Solo un calendario por día por sede
            $table->unique(['gym_branch_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_daily_calendars');
    }
};
