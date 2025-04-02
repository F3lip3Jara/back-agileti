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
        Schema::create('sd_orden', function (Blueprint $table) {
            $table->bigIncrements('ordId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa'); 
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro'); 
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm'); 
            $table->string('ordNumber', 20); // Número de onda
            $table->integer('ordQty'); // Cantidad de orden
            $table->string('ordestatus', 20); // Estado del pedido  (Liberado , Verificado, Pendiente , Transito)
            $table->string('ordTip', 20)->nullable(); // Tipo Salida / Entrada
            $table->string('ordTipDes', 100)->nullable(); // Tipo Salida / Entrada
            $table->string('ordClase', 100)->nullable(); // Tipo Salida / Entrada
            $table->string('ordClaseDes', 100)->nullable(); // Tipo Salida / Entrada
            $table->string('ordHdrCustShortText1', 255)->nullable(); // Direccion
            $table->string('ordHdrCustShortText2', 100)->nullable(); // Ciudad
            $table->string('ordHdrCustShortText3', 100)->nullable(); // Región
            $table->string('ordHdrCustShortText4', 100)->nullable(); // Identificación de orden migrado
            $table->string('ordHdrCustShortText5', 100)->nullable(); // Estado de la orden
            $table->string('ordHdrCustShortText6', 100)->nullable(); // Teléfono          
            $table->string('ordHdrCustShortText7', 100)->nullable(); // Nombre
            $table->string('ordHdrCustShortText8', 100)->nullable(); // Email
            $table->string('ordHdrCustShortText9', 100)->nullable(); // Courier
            $table->string('ordHdrCustShortText10', 20)->nullable(); // Latitud de la orden
            $table->string('ordHdrCustShortText11', 20)->nullable(); // Latitud de la orden
            $table->string('ordHdrCustShortText12', 20)->nullable(); // Clase de documento
            $table->string('ordHdrCustShortText13', 20)->nullable(); // Ruta
            $table->longText('ordHdrCustLongText1')->nullable(); // Place
            $table->timestamps();
        });

        Schema::create('sd_orden_det', function (Blueprint $table) {
            $table->bigIncrements('orddId');            
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa'); 
            $table->bigInteger('centroId')->unsigned();
            $table->foreign('centroId')->references('centroId')->on('sd_centro'); 
            $table->bigInteger('almId')->unsigned();
            $table->foreign('almId')->references('almId')->on('sd_centro_alm'); 
            $table->bigInteger('ordId')->unsigned();
            $table->foreign('ordId')->references('ordId')->on('sd_orden'); 
            $table->string('orddNumber', 20); // Número de onda
            $table->integer('orddQtySol'); // Cantidad de orden
            $table->integer('orddQtyAsig'); // Cantidad de orden
            $table->string('ordDtlCustShortText1', 255)->nullable(); // Prioridad
            $table->string('ordDtlCustShortText2', 100)->nullable(); // Jerarquia1
            $table->string('ordDtlCustShortText3', 100)->nullable(); // Jerarquia1
            $table->string('ordDtlCustShortText4', 100)->nullable(); // Jerarquia3
            $table->string('ordDtlrCustShortText5',100)->nullable(); // Jerarquia4
            $table->string('ordDtlCustShortText6', 100)->nullable(); // Jerarquia5
            $table->string('ordDtlCustShortText7', 100)->nullable(); // Sector
            $table->string('ordDtlCustShortText8', 100)->nullable(); // Sector Numero
            $table->string('ordDtlCustShortText9', 100)->nullable(); // Posición
            $table->string('ordDtlCustShortText10', 100)->nullable(); // Clave Sector - numero - Posicion
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::dropIfExists('sd_orden_det');
        Schema::dropIfExists('sd_orden');
    }
};
