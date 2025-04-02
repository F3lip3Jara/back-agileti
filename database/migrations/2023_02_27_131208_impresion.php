<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Impresion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_impresion', function (Blueprint $table) {
            $table->bigIncrements('impId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('ordtId')->unsigned();
            $table->foreign('ordtId')->references('ordtId')->on('prod_ord_trabajo');
            $table->bigInteger('ordtdId')->unsigned();
            $table->foreign('ordtdId')->references('ordtdId')->on('prod_ord_trabajo_det');
            $table->string('impUso');          
            $table->string('impEst');
            $table->string('impEstCtl');
            $table->string('impMaq')->nullable();
            $table->string('impTip')->nullable(); 
            $table->string('impPrdCaja')->nullable();
            $table->string('impPrdBolsa')->nullable();  
            $table->string('impLotCaja')->nullable();
            $table->string('impLotBolsa')->nullable();           
            $table->longText('impObs')->nullable();
            $table->string('impLotSal')->nullable();
            $table->integer('impTurn')->nullable();
            $table->integer('impReproceso')->nullable();
            $table->integer('impBasura')->nullable();
            $table->integer('impMerma')->nullable();
            $table->integer('impidTer')->nullable();            
            $table->timestamps();
        });

        Schema::create('prod_impresion_det', function (Blueprint $table) {
            $table->bigIncrements('impdId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('impId')->unsigned();
            $table->foreign('impId')->references('impId')->on('prod_impresion');
            $table->string('impdHorIni');
            $table->string('impdHorFin')->nullable();
            $table->integer('impdPesoCaja')->nullable(); 
            $table->integer('impdCajaAcu')->nullable(); 
            $table->bigInteger('impdidMot')->nullable();
            $table->string('impdDefecto')->nullable();
            $table->string('impdUso');
            $table->string('impdRol');            
            $table->string('impdTip');
            $table->string('impdEst');
            $table->timestamps();
        });


        Schema::create('prod_impresion_det_peso', function (Blueprint $table) {
            $table->bigIncrements('imppId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('impId')->unsigned();
            $table->foreign('impId')->references('impId')->on('prod_impresion');  
            $table->string('imppUso');
            $table->string('imppRol');            
            $table->string('imppTip');  
            $table->decimal('impPeso' , 10 , 2);                    
            $table->timestamps();
        });

        Schema::create('prod_impresion_tinta', function (Blueprint $table) {
            $table->bigIncrements('imptId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('impId')->unsigned();
            $table->foreign('impId')->references('impId')->on('prod_impresion');                       
            $table->string('imptPrd')->nullable();
            $table->string('imptPrdLote');
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
