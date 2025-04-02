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
        Schema::create('sd_tip_clase', function (Blueprint $table) {
            $table->bigIncrements('clasTipId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');         
            $table->string('clasTipDes')->nullable();
            $table->char('clasTip'); //Clase de tipo
            $table->timestamps();
        });

        Schema::table('vent_ordenes', function (Blueprint $table) {
            $table->bigInteger('clasTipId')->unsigned(); 
         
        });

           // Tabla Lineas de Ordenes
        Schema::table('vent_lineas_ordenes', function (Blueprint $table) {          
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro'); 
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm'); 
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
