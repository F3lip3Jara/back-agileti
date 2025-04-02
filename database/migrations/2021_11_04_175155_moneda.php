<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Moneda extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parm_moneda', function (Blueprint $table) {
            $table->bigIncrements('monId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');          
            $table->string('monCod');
            $table->string('monDes');
            $table->string('monInt')->nullable();
            $table->string('monIntVal')->nullable();
            $table->string('monIntArray')->nullable();
            $table->unique(['empId', 'monId']);
            $table->timestamps();
        });

        Schema::create('parm_moneda_conversion', function (Blueprint $table) {
            $table->bigIncrements('moncId');
            $table->bigInteger('monId')->unsigned();
            $table->foreign('monId')->references('monId')->on('parm_moneda');
            $table->date('moncFecha')->nullable();
            $table->double('moncValor')->nullable();
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
