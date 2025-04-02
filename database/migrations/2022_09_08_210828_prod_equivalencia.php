<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProdEquivalencia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parm_prd_equivalencia', function (Blueprint $table) {
            $table->bigIncrements('equiId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('prdId')->unsigned();
            $table->foreign('prdId')->references('prdId')->on('parm_producto');
            $table->integer('equiPrdBulto')->nullable();
            $table->integer('equiBultPallet')->nullable();
            $table->integer('equiPrdBins')->nullable();            
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
