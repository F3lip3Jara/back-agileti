<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BinCorrelativo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parm_bins_col', function (Blueprint $table) {
            $table->bigIncrements('colbId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('etaId')->unsigned();
            $table->foreign('etaId')->references('etaId')->on('parm_etapa');
            $table->bigInteger('colbnum')->unsigned();
            $table->char('colbtip')->nullable();
            $table->timestamps();
        });

        Schema::create('parm_bins_col_his', function (Blueprint $table) {
            $table->bigIncrements('colbdId');
            $table->bigInteger('colbId')->unsigned();
            $table->foreign('colbId')->references('colbId')->on('parm_bins_col');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('etaId')->unsigned();
            $table->foreign('etaId')->references('etaId')->on('parm_etapa');
            $table->bigInteger('colbnum_h')->unsigned();
            $table->char('colbtip_h')->nullable();
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
