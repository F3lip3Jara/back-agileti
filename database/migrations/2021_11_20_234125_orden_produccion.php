<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdenProduccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prod_orden', function (Blueprint $table) {
            $table->bigIncrements('orpId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('prvId')->unsigned();
            $table->foreign('prvId')->references('prvId')->on('parm_proveedor');          
            $table->string('orpNumOc');
            $table->string('orpNumRea');
            $table->string('orpFech')->nullable();
            $table->string('orpUsrG');
            $table->string('orpObs')->nullable();
            $table->string('orpTurns')->nullable();
            $table->integer('orpEst');
            $table->integer('orpEstPrc');
            $table->string('orpHdrCustShortText1', 255)->nullable(); // Etapa
            $table->string('orpHdrCustShortText2', 100)->nullable(); // Clase documento
            $table->string('orpHdrCustShortText3', 100)->nullable(); // 
            $table->string('orpHdrCustShortText4', 100)->nullable(); // 
            $table->string('orpHdrCustShortText5', 100)->nullable(); // 
            $table->string('orpHdrCustShortText6', 100)->nullable(); // 
            $table->string('orpHdrCustShortText7', 100)->nullable(); // 
            $table->string('orpHdrCustShortText8', 100)->nullable(); // 
            $table->string('orpHdrCustShortText9', 100)->nullable(); // 
            $table->string('orpHdrCustShortText10', 20)->nullable(); // 
            $table->string('orpHdrCustShortText11', 20)->nullable(); // 
            $table->string('orpHdrCustShortText12', 20)->nullable(); // 
            $table->string('orpHdrCustShortText13', 20)->nullable(); // 
            $table->longText('orpHdrCustLongText1')->nullable(); // 
            $table->timestamps();
        });

        Schema::create('prod_orden_det', function (Blueprint $table) {
            $table->bigIncrements('orpdId');
            $table->bigInteger('orpId')->unsigned();
            $table->foreign('orpId')->references('orpId')->on('prod_orden');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->string('orpdPrdCod');
            $table->string('orpdPrdDes');
            $table->integer('orpdCant');
            $table->string('orpdDtlCustShortText1', 255)->nullable(); // 
            $table->string('orpdDtlCustShortText2', 100)->nullable(); // 
            $table->string('orpdDtlCustShortText3', 100)->nullable(); // 
            $table->string('orpdDtlCustShortText4', 100)->nullable(); // 
            $table->string('orpdDtlrCustShortText5',100)->nullable(); // 
            $table->string('orpdDtlCustShortText6', 100)->nullable(); // 
            $table->string('orpdDtlCustShortText7', 100)->nullable(); // 
            $table->string('orpdDtlCustShortText8', 100)->nullable(); //  
            $table->string('orpdDtlCustShortText9', 100)->nullable(); // 
            $table->string('orpdDtlCustShortText10', 100)->nullable(); // 
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
