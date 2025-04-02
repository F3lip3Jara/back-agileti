<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdenTrabajo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_ord_trabajo', function (Blueprint $table) {
            $table->bigIncrements('ordtId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('orpId')->unsigned();
            $table->foreign('orpId')->references('orpId')->on('prod_orden');
            $table->string('orptFech')->nullable();
            $table->string('orptUsrG');
            $table->string('orptTurns')->nullable();
            $table->integer('orptEst');
            $table->char('orptPrio');
            $table->timestamps();
        });

       Schema::create('prod_ord_trabajo_det', function (Blueprint $table) {
            $table->bigIncrements('ordtdId');
            $table->bigInteger('ordtId')->unsigned();
            $table->foreign('ordtId')->references('ordtId')->on('prod_ord_trabajo');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('ortidOrp')->unsigned();
            $table->bigInteger('ortidOrpd')->unsigned();
            $table->string('ordtdPrdCod');
            $table->string('ordtdPrdDes');
            $table->integer('ortdSol');
            $table->integer('ortdProd');
            $table->string('orpdObs')->nullable();
            $table->bigInteger('orpdetaId')->nullable()->unsigned(); 
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
