<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Proveedor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parm_proveedor', function (Blueprint $table) {
            $table->bigIncrements('prvId')->unsigned();
            $table->bigInteger('empId')->unsigned();          
            $table->string('prvRut');
            $table->string('prvNom');
            $table->string('prvNom2')->nullable();
            $table->string('prvGiro')->nullable();
            $table->string('prvNum')->nullable();
            $table->string('prvDir')->nullable();
            $table->string('prvTel')->nullable();
            $table->char('prvCli');
            $table->char('prvPrv');
            $table->string('prvMail')->nullable();
            $table->string('prvAct');
            $table->foreign('empId')->references('empId')->on('parm_empresa');           
            $table->bigInteger('paiId')->unsigned();
            $table->foreign('paiId')->references('paiId')->on('parm_pais');
            $table->bigInteger('regId')->unsigned();
            $table->foreign('regId')->references('regId')->on('parm_region');
            $table->bigInteger('comId')->unsigned();
            $table->foreign('comId')->references('comId')->on('parm_comuna');
            $table->bigInteger('ciuId')->unsigned();
            $table->foreign('ciuId')->references('ciuId')->on('parm_ciudad');
            $table->timestamps();
        });

        Schema::create('parm_prv_suc', function (Blueprint $table) {
            $table->bigInteger('empId')->unsigned();          
            $table->bigInteger('prvId')->unsigned();           
            $table->bigIncrements('prvdId')->unsigned();
            $table->string('prvdDir');
            $table->string('prvdNum');
            $table->bigInteger('paiId')->unsigned();
            $table->foreign('paiId')->references('paiId')->on('parm_pais');
            $table->bigInteger('regId')->unsigned();
            $table->foreign('regId')->references('regId')->on('parm_region');
            $table->bigInteger('comId')->unsigned();
            $table->foreign('comId')->references('comId')->on('parm_comuna');
            $table->bigInteger('ciuId')->unsigned();
            $table->foreign('ciuId')->references('ciuId')->on('parm_ciudad');          
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

    }
}
