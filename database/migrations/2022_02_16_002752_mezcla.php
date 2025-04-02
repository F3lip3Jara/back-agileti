<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Mezcla extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produ_mezcla', function (Blueprint $table) {
            $table->bigIncrements('mezId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->string('mezUsu');
            $table->string('mezLotSal');
            $table->decimal('mezKil', 10, 2);
            $table->char('mezTip');
            $table->string('mezEst');
            $table->string('mezEstCtl');
            $table->string('mezMaq');
            $table->string('mezidEta');
            $table->string('mezprdCod');
            $table->string('mezidPrd');
            $table->string('mezprdDes');
            $table->integer('mezTurn');
            $table->longText('mezObs');
            $table->timestamps();
        });

        Schema::create('mezcla_det', function (Blueprint $table) {
            $table->bigIncrements('mezdId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('mezId')->unsigned();
            $table->foreign('mezId')->references('mezId')->on('produ_mezcla');
            $table->bigInteger('mezdidPrd');
            $table->string('mezdprdCod');
            $table->string('mezdprdTip');
            $table->string('mezdprdDes');
            $table->string('mezdLotIng');
            $table->string('mezdUso');
            $table->string('mezdManual')->nullable();
            $table->decimal('mezdKil' , 10 , 2);
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
