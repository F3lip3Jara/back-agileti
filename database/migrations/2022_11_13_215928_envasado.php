<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Envasado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_envasado', function (Blueprint $table) {
            $table->bigIncrements('envId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('terId')->unsigned();
            $table->foreign('terId')->references('terId')->on('prod_termoformado');
            $table->integer('envTurn'); 
            $table->string('envLotSal');
            $table->string('envPrdCaja')->nullable();
            $table->string('envPrdBolsa')->nullable();  
            $table->string('envLotCaja')->nullable();
            $table->string('envLotBolsa')->nullable(); 
            $table->string('envMaq'); 
            $table->char('envEst'); 
            $table->char('envEstCtl'); 
            $table->longText('envObs')->nullable(); 
            $table->timestamps();
        });

        Schema::create('prod_envasado_det', function (Blueprint $table) {
            $table->bigIncrements('envdId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('envId')->unsigned();
            $table->foreign('envId')->references('envId')->on('prod_envasado');
            $table->string('envdHorIni');
            $table->string('envdHorFin')->nullable();
            $table->integer('envdCaja')->nullable(); 
            $table->integer('envdPallet')->nullable(); 
            $table->timestamps();
        });

        Schema::create('prod_envasado_arch', function (Blueprint $table) {
            $table->bigIncrements('envarId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('envId')->unsigned();
            $table->foreign('envId')->references('envId')->on('prod_envasado');           
            $table->string('envLink');        
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
