<?php

namespace Database\Seeders;

use App\Models\FieldDefinition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Tablas extends Seeder
{
    public function run(): void
    {
        // Obtener todas las tablas de la base de datos (MySQL específico)
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableExcluded = ['field_definitions' , 'cache' , 'cache_locks' , 'failed_jobs' ,   'job_batches' , 'jobs' , 'migrations' , 'sessions' , 'telescope_entries' , 'telescope_entries_tags' , 'telescope_monitoring'];

        foreach ($tables as $tableObj) {
            // MySQL devuelve el nombre de la columna como 'Tables_in_nombrebasededatos'

            
            $table = $tableObj->{"Tables_in_$databaseName"};

            // Saltar la tabla de metadatos para evitar recursividad
            if (in_array($table, $tableExcluded)) continue;

            $columns = Schema::getColumnListing($table);

            foreach ($columns as $column) {
                FieldDefinition::updateOrCreate(
                    ['field_name' => "$column"], // campo calificado con el nombre de la tabla
                    [   
                        'table_name' => $table,
                        'label' => ucfirst(str_replace('_', ' ', $column)),
                        'description' => "Campo '$column' de la tabla '$table'",
                        'data_type' => $this->getColumnType($table, $column),
                        'options' => null,
                        'is_filterable' => true,
                    ]
                );
            }
        }
    }

    protected function getColumnType($table, $column)
    {
        return DB::getSchemaBuilder()->getColumnType($table, $column);
    }
}
