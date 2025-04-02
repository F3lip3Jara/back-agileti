<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Notificaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_notificaciones', function (Blueprint $table) {
            $table->bigIncrements('notId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');           
            $table->string('notUso');          
            $table->string('notEst');                  
            $table->longText('notObs')->nullable();
            $table->string('notLotSal')->nullable();     
            $table->timestamps();
        });

        Schema::create('sys_not_visualizacion', function (Blueprint $table) {
            $table->bigIncrements('idNotv');            
            $table->bigInteger('notId')->unsigned();
            $table->foreign('notId')->references('notId')->on('sys_notificaciones');       
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');           
            $table->string('notvUso');  
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
