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
      Schema::create('sd_mov_pallets', function (Blueprint $table) {
            $table->bigIncrements('movPalletId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('palletId')->unsigned();
            $table->foreign('palletId')->references('palletId')->on('sd_pallets');
            $table->bigInteger('userId')->unsigned();
            $table->foreign('userId')->references('id')->on('users');
            $table->bigInteger('sectorOrigenId')->unsigned();
            $table->foreign('sectorOrigenId')->references('sectorId')->on('sd_cent_alm_sector');
            $table->bigInteger('ubicacionOrigenId')->unsigned();
            $table->foreign('ubicacionOrigenId')->references('ubicacionId')->on('sd_centro_alm_sec_ubi');
            $table->bigInteger('sectorDestinoId')->unsigned();
            $table->foreign('sectorDestinoId')->references('sectorId')->on('sd_cent_alm_sector');
            $table->bigInteger('ubicacionDestinoId')->unsigned()->nullable();           
            $table->enum('movPalletTipo', ['interno','externo'])->default('interno');
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
