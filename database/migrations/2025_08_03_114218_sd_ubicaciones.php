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
        Schema::create('sd_centro_alm_sec_ubi', function (Blueprint $table) {
            $table->bigIncrements('ubicacionId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm');
            $table->bigInteger('sectorId')->unsigned();
            $table->foreign('sectorId')->references('sectorId')->on('sd_cent_alm_sector');
            $table->string('ubiDes');
            $table->string('ubiCod');
            $table->integer('ubiAlto');
            $table->integer('ubiAncho');
            $table->integer('ubiLargo');
            $table->integer('ubiVol');
            $table->integer('ubiAct');
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
