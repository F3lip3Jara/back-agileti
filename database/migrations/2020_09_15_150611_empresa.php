<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Empresa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('parm_empresa', function (Blueprint $table) {
            $table->bigIncrements('empId');
            $table->string('empDes');
            $table->string('empDir');
            $table->string('empGiro');
            $table->string('empRut');
            $table->string('empFono');
            $table->string('empNomApp')->nullable();
            $table->string('empMail')->nullable();
            $table->longText('empImg')->nullable();
            $table->longText('empTokenOMS')->nullable();
            $table->timestamps();
        });

       /* Schema::create('parm_empresa_int', function (Blueprint $table) {
            $table->bigIncrements('empPar');
            $table->bigInteger('idEta')->unsigned();
            $table->foreign('idEta')->references('idEta')->on('etapasUser');
            $table->string('emparDes');
            $table->string('emparValor');
            $table->string('emparTip');
            $table->string('emparUrl')->nullable();
            $table->string('emparUser')->nullable();
            $table->string('emparPass')->nullable();
            $table->string('emparToken')->nullable();
            $table->timestamps();
        });*/


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
