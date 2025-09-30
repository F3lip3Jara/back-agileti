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
        
        Schema::create('sd_pallets', function (Blueprint $table) {
            $table->bigIncrements('palletId');
            $table->string('pall_codigo');           // código o etiqueta del pallet (para imprimir con barcode)
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm');
            $table->bigInteger('sectorId')->unsigned();
            $table->foreign('sectorId')->references('sectorId')->on('sd_cent_alm_sector');
            $table->bigInteger('ubicacionId')->unsigned()->nullable();           
            $table->enum('pall_estado', ['activo', 'traslado', 'despachado'])->default('activo');
            $table->timestamps();
        });

        Schema::create('sd_pallets_det', function (Blueprint $table) {
            $table->bigIncrements('palletDetId');
            $table->bigInteger('palletId')->unsigned();
            $table->foreign('palletId')->references('palletId')->on('sd_pallets');
            $table->bigInteger('iblpnId')->unsigned();
            $table->foreign('iblpnId')->references('iblpnId')->on('sd_iblpns');
            $table->timestamps();
         
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sd_pallets_det');
        Schema::dropIfExists('sd_pallets');
    }
};
