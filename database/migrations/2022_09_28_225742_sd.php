<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Sd extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sd_centro', function (Blueprint $table) {
            $table->bigIncrements('centroId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->string('cenDes');
            $table->string('cenDir');
            $table->longText('cenPlace')->nullable();
            $table->integer('cenCap')->nullable();;
            $table->timestamps();
        });

        Schema::create('sd_centro_alm', function (Blueprint $table) {
            $table->bigIncrements('almId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');
            $table->string('almDes');
            $table->char('almTip');
            $table->integer('almCap');
            $table->timestamps();
        });



        Schema::create('sd_cent_alm_sector', function (Blueprint $table) {
            $table->bigIncrements('sectorId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa'); 
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');  
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm');           
            $table->string('secDes');
            $table->string('secCod');
            $table->timestamps();
        });

        


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
