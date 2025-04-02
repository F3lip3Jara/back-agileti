<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parm_pais', function (Blueprint $table) {
            $table->bigIncrements('paiId')->unsigned();
            $table->string('paiCod');
            $table->string('paiDes');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->unique(['empId', 'paiId']);
            $table->timestamps();
        });

        Schema::create('parm_region', function (Blueprint $table) {
            $table->bigIncrements('regId');
            $table->string('regDes');
            $table->string('regCod');
            $table->bigInteger('empId')->unsigned();           
            $table->bigInteger('paiId')->unsigned();    
            $table->foreign('empId')->references('empId')->on('parm_empresa'); 
            $table->foreign('paiId')->references('paiId')->on('parm_pais');               
            $table->timestamps();
        });

   

        Schema::create('parm_ciudad', function (Blueprint $table) {
            $table->bigIncrements('ciuId');       
            $table->string('ciuDes');
            $table->string('ciuCod');
            $table->bigInteger('empId')->unsigned();           
            $table->bigInteger('paiId')->unsigned();    
            $table->bigInteger('regId')->unsigned();           
            $table->foreign('empId')->references('empId')->on('parm_empresa'); 
            $table->foreign('paiId')->references('paiId')->on('parm_pais');
            $table->foreign('regId')->references('regId')->on('parm_region');                      
            $table->timestamps();
        });

        Schema::create('parm_comuna', function (Blueprint $table) {
            $table->bigIncrements('comId');
            $table->string('comDes');
            $table->string('comCod');
            $table->bigInteger('empId')->unsigned();           
            $table->bigInteger('paiId')->unsigned();    
            $table->bigInteger('regId')->unsigned();
            $table->bigInteger('ciuId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa'); 
            $table->foreign('paiId')->references('paiId')->on('parm_pais');
            $table->foreign('regId')->references('regId')->on('parm_region'); 
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
        //
    }
}
