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
      /*  Schema::create('proyecto', function (Blueprint $table) {
            $table->bigIncrements('idProj');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('empresa');        
            $table->string('projDes');
            $table->string('projFech')->nullable();         
            $table->integer('projNeto');
            $table->integer('projIva');
            $table->integer('projTot');
            $table->longText('projCom');
            $table->bigInteger('idPrv')->unsigned();
            $table->foreign('idPrv')->references('idPrv')->on('proveedor');           
            $table->timestamps();
        });*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
