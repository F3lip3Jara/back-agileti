<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Empleados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parm_gerencia', function (Blueprint $table) {
            $table->bigIncrements('gerId');
            $table->string('gerDes');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->timestamps();
        });

        Schema::create('parm_empleados', function (Blueprint $table) {
            $table->bigIncrements('emploId');            
            $table->string('emploNom');
            $table->string('emploApe');
            $table->string('emploFecNac')->nullable();
            $table->longText('emploAvatar')->nullable();
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');
            $table->bigInteger('gerId')->unsigned()->nullable();            
            $table->bigInteger('id')->unsigned()->nullable();
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
