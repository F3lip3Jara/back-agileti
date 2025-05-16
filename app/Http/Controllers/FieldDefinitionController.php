<?php

namespace App\Http\Controllers;

use App\Jobs\LogSistema;
use App\Models\FieldDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FieldDefinitionController extends Controller
{
    
    public function index(){

        $fieldDefinition = FieldDefinition::select('id', 'field_name', 'label', 'data_type', 'is_filterable')->get();
        return response()->json($fieldDefinition);
    }

    public function updateFromExcel(Request $request) {
     
        
            $data = $request->all();
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se recibieron datos para actualizar'
                ], 400);
            }
            $configField = $data['configField'];

            $updated = 0;
            $errors = [];
        
            foreach ($configField as $item) {
                try {
                    // Validar campos requeridos
                    if (!isset($item['id']) || !isset($item['field_name']) || !isset($item['label']) || 
                        !isset($item['data_type']) || !isset($item['is_filterable'])) {
                        $errors[] = "Fila con ID {$item['id']} no tiene todos los campos requeridos";
                        continue;
                    }

                    // Validar is_filterable
                    if ($item['is_filterable'] !== 0 && $item['is_filterable'] !== 1) {
                        $errors[] = "El campo is_filterable debe ser 0 o 1 para el ID {$item['id']}";
                        continue;
                    }
                   
                    $affected = FieldDefinition::where('id', $item['id'])->update([
                        'label' => $item['label'],                       
                        'is_filterable' => $item['is_filterable']
                    ]);
                  
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Error al actualizar el campo con ID {$item['id']}: " . $e->getMessage();
                    return response()->json([
                        'success' => false,
                        'message' => 'Se encontraron errores durante la actualización',
                        'errors' => $errors,
                        'updated' => $updated
                    ], 400);
                }
            }

            if (count($errors) > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Se encontraron errores durante la actualización',
                    'errors' => $errors,
                    'updated' => $updated
                ], 400);
            }

            DB::commit();
            $name        = $request['name'];
            $empId       = $request['empId'];
           if($updated > 0){
                    $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accDes'], $request->log['0']['accTip']);
                    dispatch($job);            
                    $resources = array(
                        array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                    );
                    return response()->json($resources, 200);
           }else{
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron campos para actualizar',
                'updated' => $updated
            ], 400);
           }
            
          
        

        
    }
}
