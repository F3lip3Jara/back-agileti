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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('linear_issue_id')->nullable()->index();
            $table->string('title');
            $table->string('status')->default('pending')->index(); // pending, running, completed, failed, cancelled
            $table->string('agent')->default('ollama'); // ollama, antigravity
            $table->mediumText('prompt')->nullable();
            $table->mediumText('output')->nullable();
            $table->mediumText('logs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
