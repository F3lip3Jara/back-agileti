<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_calendar_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gym_branch_id')->constrained('gym_branches')->onDelete('cascade');
            $table->integer('day_of_week'); // 1 = Lunes, 7 = Domingo
            $table->time('open_time');
            $table->time('close_time');
            $table->integer('slot_duration_minutes')->default(60);
            $table->integer('default_max_quota')->default(20);
            $table->boolean('is_open')->default(true);
            $table->timestamps();

            // Un solo día de la semana por sucursal
            $table->unique(['gym_branch_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_calendar_configs');
    }
};
