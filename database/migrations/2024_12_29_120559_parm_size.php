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
        Schema::create('parm_talla', function (Blueprint $table) {
            $table->bigIncrements('tallaId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa'); 
            $table->string('tallaCod');
            $table->string('tallaDes');        
            $table->timestamps();
        });


        Schema::table('parm_producto', function (Blueprint $table) {
            $table->bigInteger('tallaId')->unsigned();
            $table->foreign('tallaId')->references('tallaId')->on('parm_talla');
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
