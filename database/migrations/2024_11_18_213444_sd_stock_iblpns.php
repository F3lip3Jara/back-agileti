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
       
        Schema::create('sd_traslado', function (Blueprint $table) {
            $table->bigIncrements('trasId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');         
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro');         
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm');
            $table->bigInteger('iblpnId')->unsigned(); 
            $table->foreign('iblpnId')->references('iblpnId')->on('sd_iblpns');          
            $table->string('trasTip', 1);            
            $table->string('trassecCod');
            $table->string('trassecDes');            
            $table->string('trasSecDesDes');
            $table->string('trasSecCodDes');
            $table->bigInteger('trasUserid')->unsigned(); 
            $table->string('trasUserName');
            $table->string('trasHdrCustShortText1', 100)->nullable(); // Pallet
            $table->string('trasHdrCustShortText2', 100)->nullable(); // 
            $table->string('trasHdrCustShortText3', 100)->nullable(); // Pallet
            $table->string('trasHdrCustShortText4', 100)->nullable(); //                
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
