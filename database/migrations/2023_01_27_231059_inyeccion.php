<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Inyeccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_inyeccion', function (Blueprint $table) {
            $table->bigIncrements('inyId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('ordtId')->unsigned();
            $table->foreign('ordtId')->references('ordtId')->on('prod_ord_trabajo');
            $table->bigInteger('ordtdId')->unsigned();
            $table->foreign('ordtdId')->references('ordtdId')->on('prod_ord_trabajo_det');
            $table->string('inyUso');          
            $table->string('inyEst');
            $table->string('inyEstCtl');
            $table->string('inyMaq')->nullable();
            $table->string('inyTip')->nullable();
            $table->string('inyPrdCaja')->nullable();
            $table->string('inyPrdBolsa')->nullable();  
            $table->string('inyLotCaja')->nullable();
            $table->string('inyLotBolsa')->nullable();                  
            $table->longText('inyObs')->nullable();
            $table->string('inyLotSal')->nullable();
            $table->integer('inyTurn')->nullable();
            $table->integer('inyIdMez')->nullable();
            $table->integer('inyCavTot')->nullable();
            $table->integer('inyCavAct')->nullable();
            $table->char('inyLimpieza')->nullable();
            $table->integer('inyReproceso')->nullable();
            $table->integer('inyMerma')->nullable();
            $table->bigInteger('inyExtMez')->nullable();            
            $table->timestamps();
        });

        Schema::create('prod_inyeccion_det', function (Blueprint $table) {
            $table->bigIncrements('inydId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('inyId')->unsigned();
            $table->foreign('inyId')->references('inyId')->on('prod_inyeccion');           
            $table->string('inydEst');
            $table->string('inydHorIni');
            $table->string('inydHorFin')->nullable();
            $table->string('inydUso');
            $table->string('inydRol');            
            $table->string('inydLotExt');         
            $table->string('inydRechazo')->nullable();
            $table->string('inydLimp')->nullable();
            $table->integer('inydCaja')->nullable(); 
            $table->string('inydTipo')->nullable(); 
            $table->bigInteger('inydidMot')->nullable();
            $table->string('inydDefecto')->nullable();
            $table->char('inydSani')->nullable(); 
            $table->integer('inydUnAlm')->nullable();
            $table->string('inydFechVen')->nullable();  
            $table->integer('inydConmutacion')->nullable(); 
            $table->integer('inydPesoCaja')->nullable(); 
            $table->longText('inydObs')->nullable(); 
             
            
           // $table->string('terRechazo')->nullable(); 
           // $table->string('terLimpieza')->nullable();  
           //$table->string('inydtipDe')->nullable();
            $table->timestamps();
        });

        Schema::create('prod_inyeccion_det_peso', function (Blueprint $table) {
            $table->bigIncrements('inypId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('inyId')->unsigned();
            $table->foreign('inyId')->references('inyId')->on('prod_inyeccion');  
            $table->string('inypUso');
            $table->string('inypRol');            
            $table->string('inyptip');  
            $table->decimal('inypPeso' , 10 , 2);                    
            $table->timestamps();
        });

        Schema::create('prod_inyeccion_arch', function (Blueprint $table) {
            $table->bigIncrements('inyarchId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('inyId')->unsigned();
            $table->foreign('inyId')->references('inyId')->on('prod_inyeccion');           
            $table->string('inyarlink');        
            $table->timestamps();
        });

        Schema::create('prod_inyeccion_pallet', function (Blueprint $table) {
            $table->bigIncrements('inypaId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('inyId')->unsigned();
            $table->foreign('inyId')->references('inyId')->on('prod_inyeccion');
            $table->bigInteger('inydId')->unsigned();
            $table->foreign('inydId')->references('inydId')->on('prod_inyeccion_det');                 
            $table->integer('inypaCor');        
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
