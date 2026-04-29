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


        Schema::create('gym_profiles', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type')->default('student');
            $table->string('gender')->nullable();
            $table->string('activity_level')->nullable();
            $table->float('weight', 3, 1)->nullable();
            $table->float('height', 3, 1)->nullable();
            $table->text('medical_conditions')->nullable();
            $table->text('routine')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
