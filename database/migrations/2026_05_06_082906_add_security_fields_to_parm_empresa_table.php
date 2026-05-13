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
        Schema::table('parm_empresa', function (Blueprint $table) {
            $table->integer('empTiempoIdle')->default(900)->comment('Tiempo en segundos de inactividad para mostrar alerta');
            $table->integer('empTiempoTimeout')->default(60)->comment('Tiempo en segundos después del Idle para cerrar sesión');
            $table->integer('empTiempoExpiracionToken')->default(1440)->comment('Tiempo de vigencia del Personal Access Token en minutos');
            $table->string('empZonaHoraria', 100)->default('America/Santiago')->comment('Zona horaria para validación de caducidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parm_empresa', function (Blueprint $table) {
            $table->dropColumn(['empTiempoIdle', 'empTiempoTimeout', 'empTiempoExpiracionToken', 'empZonaHoraria']);
        });
    }
};
