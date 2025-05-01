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
        Schema::create('field_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // nombre real del campo en la base de datos
            $table->string('field_name'); // nombre real del campo en la base de datos
            $table->string('label'); // cómo se mostrará en el frontend
            $table->text('description')->nullable(); // descripción de negocio
            $table->string('data_type'); // string, integer, boolean, etc.
            $table->json('options')->nullable(); // para enums o campos con valores fijos
            $table->boolean('is_filterable')->default(true); // si se puede usar como filtro
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
