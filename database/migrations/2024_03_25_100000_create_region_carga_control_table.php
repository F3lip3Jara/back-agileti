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
        Schema::create('parm_region_carga_control', function (Blueprint $table) {
            $table->bigIncrements('regCargaId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('paiId')->unsigned();
            $table->foreign('paiId')->references('paiId')->on('parm_pais');
            $table->string('regCargaEst')->default('P'); // P: Pendiente, E: En proceso, C: Completado, F: Fallido
            $table->integer('regCargaTotal')->default(0);
            $table->integer('regCargaProgreso')->default(0);
            $table->text('regCargaError')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parm_region_carga_control');
    }
}; 