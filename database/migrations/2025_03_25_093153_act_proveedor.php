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
        Schema::table('parm_proveedor', function (Blueprint $table) {
            $table->string('prvLat')->nullable();
            $table->string('prvLong')->nullable();
            $table->string('prvPlace')->nullable();
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
