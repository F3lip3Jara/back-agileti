<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Extursion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_extrusion', function (Blueprint $table) {
            $table->bigIncrements('extId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->string('extUsu');
            $table->string('extLotSal');
            $table->decimal('extAnbob', 10, 2)->nullable();
            $table->string('extEst');
            $table->string('extEstCtl');
            $table->string('extMaq');
            $table->string('extFor')->nullable();
            $table->string('exteta')->nullable();
            $table->string('extPrdCod')->nullable();
            $table->string('extprdId')->nullable();
            $table->string('extPrdDes')->nullable();
            $table->integer('extTurn');
            $table->integer('extMezId');
            $table->integer('extMotId')->nullable();
            $table->string('extMotDes')->nullable();
            $table->longText('extObs')->nullable();
            $table->decimal('extKilApr', 10, 2)->nullable();
            $table->decimal('extKilR', 10, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('prod_extrusion_det', function (Blueprint $table) {
            $table->bigIncrements('extdId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('extId')->unsigned();
            $table->foreign('extId')->references('extId')->on('prod_extrusion');
            $table->decimal('extdIzq' , 10 , 2);
            $table->decimal('extdCen' , 10 , 2);
            $table->decimal('extdDer' , 10 , 2);
            $table->string('extdEst');
            $table->string('extdHorIni');
            $table->string('extdHorFin');
            $table->string('extdUso');
            $table->string('extdRol');
            $table->string('extdTip');
            $table->longText('extdObs')->nullable();
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
