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
        Schema::table('parm_producto', function (Blueprint $table) {
            $table->bigInteger('prdAlto')->unsigned();
            $table->bigInteger('prdAncho')->unsigned();
            $table->bigInteger('prdLargo')->unsigned();
            $table->bigInteger('prdPeso')->unsigned();
            $table->bigInteger('prdVolumen')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     
    }
};
