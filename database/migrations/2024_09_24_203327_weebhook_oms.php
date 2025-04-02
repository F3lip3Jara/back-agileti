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
       
        Schema::create('parm_tipo_pago', function (Blueprint $table) {
            $table->bigIncrements('tipPagId');
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');        
            $table->string('tipCod');
            $table->string('tipDes');
            $table->timestamps();
        });


        // Tabla WebhookOms
        Schema::create('webhook_oms', function (Blueprint $table) {
            $table->bigIncrements('omshId');
            $table->longText('json');
            $table->longText('header')->nullable();
            $table->longText('session')->nullable();
            $table->string('x_wc_webhook_topic')->nullable();
            $table->string('x_wc_webhook_resource')->nullable();
            $table->string('x_wc_webhook_event')->nullable();
            $table->string('web_estado')->nullable();
            $table->timestamps();
        });

        // Tabla Clientes
        Schema::create('parm_clientes', function (Blueprint $table) {
            $table->bigIncrements('cliId');
            $table->bigInteger('empId')->unsigned();
            $table->string('cliemail')->unique();
            $table->string('clinombre');
            $table->string('cliapellido');
            $table->string('cliempresa')->nullable();
            $table->string('clidireccion_1');
            $table->string('clidireccion_2')->nullable();
            $table->string('cliciudad');
            $table->string('clicomuna');
            $table->string('clipais');
            $table->string('clitelefono')->nullable();
            $table->string('cliidExt');
            $table->timestamps();

            // Relación con parm_empresa
            $table->foreign('empId')->references('empId')->on('parm_empresa')->onDelete('cascade');
        });

        // Tabla Ordenes
        Schema::create('vent_ordenes', function (Blueprint $table) {
            $table->bigIncrements('opedId');
            $table->bigInteger('empId')->unsigned();
            $table->bigInteger('cliId')->unsigned(); // Agregado cliId
            $table->integer('opedparentid')->default(0);
            $table->string('opedstatus');
            $table->string('opedmoneda');
            $table->string('opedversion');
            $table->timestamp('opedfechaCreacion'); // Cambio de date a timestamp
            $table->boolean('opedpreciosIncluyenImpuestos');
            $table->decimal('opeddescuentoTotal', 10, 2);
            $table->decimal('opeddescuentoImpuesto', 10, 2);
            $table->decimal('opedenvioTotal', 10, 2);
            $table->decimal('opedenvioImpuesto', 10, 2);
            $table->decimal('opedimpuestoCarrito', 10, 2);
            $table->decimal('opedtotal', 10, 2);
            $table->decimal('opedtotalImpuesto', 10, 2);
            $table->string('opedclaveOrden');
            $table->string('opedMetodoPago');
            $table->string('opedtituloMetodoPago');
            $table->string('opeddireccionIpCliente');
            $table->longText('opedPlace');   
            $table->string('opedEntrega');            
            $table->string('userAgentCliente');
            $table->string('opedcarritoHash');
            $table->string('opedidExt');
            $table->string('opedComCod')->nullable();
            $table->timestamps();

            // Relaciones
           // $table->foreign('cliId')->references('cliId')->on('parm_clientes')->onDelete('cascade');
           // $table->foreign('empId')->references('empId')->on('parm_empresa')->onDelete('cascade');
        });

        // Tabla Lineas de Ordenes
        Schema::create('vent_lineas_ordenes', function (Blueprint $table) {
            $table->bigIncrements('opeddId');
            $table->bigInteger('empId')->unsigned();
            $table->bigInteger('opedId')->unsigned();
            $table->integer('opeddproductoId');
            $table->string('opeddnombreProducto');
            $table->integer('opeddcantidad');
            $table->decimal('opeddsubtotal', 10, 2);
            $table->decimal('opeddtotal', 10, 2);
            $table->timestamps();

            // Relaciones
            $table->foreign('empId')->references('empId')->on('parm_empresa')->onDelete('cascade');
            $table->foreign('opedId')->references('opedId')->on('vent_ordenes')->onDelete('cascade');
        });

        Schema::create('vent_tranbank', function (Blueprint $table) {
            $table->bigIncrements('traId');
            $table->bigInteger('opedId')->unsigned();
            $table->foreign('opedId')->references('opedId')->on('vent_ordenes');     
            $table->bigInteger('empId')->unsigned();
            $table->foreign('empId')->references('empId')->on('parm_empresa');            
            $table->longText('json')->nullable();
            $table->longText('token_ws')->nullable();
            $table->string('transtatus')->nullable();
            $table->string('transtip')->nullable();
            $table->integer('trancentra')->nullable();
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
