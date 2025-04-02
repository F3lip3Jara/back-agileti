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
        Schema::table('sd_centro', function (Blueprint $table) {
       
            $table->string('cenContacto')->nullable(); // Persona o número de contacto
            $table->string('centEmail')->nullable(); // Correo de contacto
            $table->time('cenHoraApertura')->nullable(); // Horario de apertura
            $table->time('cenHoraCierre')->nullable(); // Horario de cierre
            $table->integer('cenStockLimitWeb')->nullable(); // Stock máximo disponible para ventas web
            $table->integer('cenStockLimiteRepo')->nullable(); // Stock para reabastecimiento interno
            $table->enum('cenEstado', ['activo', 'inactivo'])->default('activo'); // Estado del centro
            $table->string('cenTelefono')->nullable(); // Extensión telefónica si aplica
            $table->string('cenLat')->nullable();
            $table->string('cenLong')->nullable();  
            
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
