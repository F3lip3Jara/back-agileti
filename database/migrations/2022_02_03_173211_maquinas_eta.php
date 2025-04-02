<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MaquinasEta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parm_maquinas', function (Blueprint $table) {
            $table->bigIncrements('maqId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('etaId')->unsigned();
            $table->foreign('etaId')->references('etaId')->on('parm_etapa');
            $table->string('maqCod');
            $table->string('maqTip')->nullable();// Esto no va
            $table->string('maqDes');
            $table->unique(['empId','maqId']);
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
