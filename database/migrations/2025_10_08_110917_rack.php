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
        Schema::create('sd_rack', function (Blueprint $table) {
            $table->bigIncrements('rackId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm');
            $table->bigInteger('sectorId')->unsigned();
            $table->foreign('sectorId')->references('sectorId')->on('sd_cent_alm_sector');
            $table->bigInteger('ubicacionId')->unsigned();
            $table->foreign('ubicacionId')->references('ubicacionId')->on('sd_centro_alm_sec_ubi');
            $table->string('rackCod');
            $table->string('rackDes');
            $table->string('rackTipo')->nullable();
            $table->string('rackTipoDes')->nullable();
            $table->string('rackEstado')->nullable();
            $table->string('rackEstadoDes')->nullable();        
            $table->string('rackAncho')->nullable();
            $table->string('rackLargo')->nullable();
            $table->string('rackAlto')->nullable();
            $table->string('rackVolumen')->nullable();
            $table->string('rackPeso')->nullable();
            $table->string('rackPesoVolumen')->nullable();             
            $table->timestamps();
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
