<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('parm_un_medida', function (Blueprint $table) {
            $table->bigIncrements('unId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');            
            $table->string('unCod');
            $table->string('unDes');
            $table->timestamps();
        });

        Schema::create('parm_grupo', function (Blueprint $table) {
            $table->bigIncrements('grpId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->string('grpCod');
            $table->string('grpDes');
            $table->timestamps();
        });

        Schema::create('parm_sub_grupo', function (Blueprint $table) {
            $table->bigIncrements('grpsId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('grpId')->unsigned();
            $table->foreign('grpId')->references('grpId')->on('parm_grupo');
            $table->string('grpsCod');
            $table->string('grpsDes');
            $table->timestamps();
        });
        Schema::create('parm_color', function (Blueprint $table) {
            $table->bigIncrements('colId')->unsigned();
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->string('colCod');
            $table->string('colDes');         
            $table->timestamps();
        });




        Schema::create('parm_producto', function (Blueprint $table) {
            $table->bigIncrements('prdId');
            $table->string('prdCod')->index();
            $table->string('prdDes');
            $table->longText('prdObs')->nullable();
            $table->string('prdRap');
            $table->string('prdEan');
            $table->string('prdTip');
            $table->double('prdCost');
            $table->double('prdNet');
            $table->double('prdBrut');
            $table->char('prdInv');
            $table->string('prdPes')->nullable();
            $table->string('prdMin');
            $table->string('prdIdExt')->nullable();
            $table->longText('prdUrl')->nullable();
            $table->char('prdMig')->nullable();
            
            $table->timestamps();
        });

       

        Schema::table('parm_producto', function (Blueprint $table) {
            $table->bigInteger('monId')->unsigned();
            $table->foreign('monId')->references('monId')->on('parm_moneda');
            $table->bigInteger('grpId')->unsigned();
            $table->foreign('grpId')->references('grpId')->on('parm_grupo');
            $table->bigInteger('grpsId')->unsigned();
            $table->foreign('grpsId')->references('grpsId')->on('parm_sub_grupo');
            $table->bigInteger('colId')->unsigned();
            $table->foreign('colId')->references('colId')->on('parm_color');
            $table->bigInteger('unId')->unsigned();
            $table->foreign('unId')->references('unId')->on('parm_un_medida');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
