<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      /*  Schema::create('sd_tip_mov', function (Blueprint $table) {
            $table->bigIncrements('tipMovId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');   
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('parm_centro');         
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('parm_almacen');     
            $table->string('tipmovDes', 20)->nullable();
            $table->integer('tipmovCod'); 
            $table->timestamps();
        });*/
        
        Schema::create('sd_stocks', function (Blueprint $table) {
            $table->bigIncrements('stockId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');         
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');         
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm'); 
            $table->bigInteger('prdId')->unsigned();
            $table->foreign('prdId')->references('prdId')->on('parm_producto'); 
            $table->integer('stockQty');
            $table->string('stockEst', 1)->default('T');
            $table->timestamps();
        });

        Schema::create('sd_stocks_mov', function (Blueprint $table) {
            $table->bigIncrements('stockMovId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');         
            $table->string('stockMovTip', 1);
            $table->integer('stockMovQty'); 
            $table->bigInteger('prdId')->unsigned();
            $table->foreign('prdId')->references('prdId')->on('parm_producto'); 
            $table->string('stockMovHdrCustShortText1', 100)->nullable(); // Centro ID
            $table->string('stockMovHdrCustShortText2', 100)->nullable(); // Centro Desc
            $table->string('stockMovHdrCustShortText3', 100)->nullable(); // Almacen ID
            $table->string('stockMovHdrCustShortText4', 100)->nullable(); // Almacen Desc
            $table->string('stockMovHdrCustShortText5', 100)->nullable(); // Usuario ID
            $table->string('stockMovHdrCustShortText6', 100)->nullable(); // Usuario Desc
            $table->timestamps();
        });

        Schema::create('sd_inventory_adjustments', function (Blueprint $table) {
            $table->bigIncrements('invAdjId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');         
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro'); 
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm');
            $table->string('invAdjTip', 1);
            $table->string('invAdjHdrCustShortText1', 100)->nullable(); // observaciones 1
            $table->string('invAdjHdrCustShortText2', 100)->nullable(); // observaciones 2
            $table->timestamps();
        });

        Schema::create('sd_inventory_adjustments_det', function (Blueprint $table) {
            $table->bigIncrements('invAdjDetId'); 
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');                
            $table->bigInteger('invAdjId')->unsigned();
            $table->foreign('invAdjId')->references('invAdjId')->on('sd_inventory_adjustments');
            $table->bigInteger('prdId')->unsigned();
            $table->foreign('prdId')->references('prdId')->on('parm_producto');
            $table->integer('invAdjDetQty');
            $table->timestamps();
        });

        Schema::create('sd_iblpns', function (Blueprint $table) {
            $table->bigIncrements('iblpnId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');                 
            $table->bigInteger('prdId')->unsigned();
            $table->foreign('prdId')->references('prdId')->on('parm_producto');
            $table->integer('iblpnQty');
            $table->string('iblpnOriginalBarcode', 100)->nullable();
            $table->string('iblpnStatus', 1)->default('P');
            $table->string('iblpnType', 1);
            $table->string('iblpnHdrCustShortText1', 100)->nullable(); //
            $table->string('iblpnHdrCustShortText2', 100)->nullable(); // 
            $table->string('iblpnHdrCustShortText3', 100)->nullable(); // 
            $table->string('iblpnHdrCustShortText4', 100)->nullable(); // 
            $table->string('iblpnHdrCustShortText5', 100)->nullable(); // 
            $table->string('iblpnHdrCustShortText6', 100)->nullable(); // 
            $table->timestamps();
        });  

        Schema::create('sd_stocks_iblpns', function (Blueprint $table) {
            $table->bigIncrements('stockIblpnId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');         
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');         
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm'); 
            $table->bigInteger('iblpnId')->unsigned();
            $table->foreign('iblpnId')->references('iblpnId')->on('sd_iblpns');          
            $table->bigInteger('prdId')->unsigned();
            $table->foreign('prdId')->references('prdId')->on('parm_producto');             
            $table->integer('stockIblpnQty');
            $table->timestamps();
        });

        Schema::create('sd_stocks_iblpns_temp', function (Blueprint $table) {
            $table->bigIncrements('stockTblpnId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');         
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');         
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm'); 
            $table->json('stockTblpnJson')->nullable();
            $table->char('stockTstatus', 1)->default('P');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
