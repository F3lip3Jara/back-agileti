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
        Schema::create('gym_branch_restrictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gym_branch_id')->constrained('gym_branches')->onDelete('cascade');
            $table->timestamps();

            // Un usuario solo puede estar restringido una vez de la misma sede
            $table->unique(['user_id', 'gym_branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gym_branch_restrictions');
    }
};
