<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\MenuRol;
use Illuminate\Support\Collection;
use App\Models\Seguridad\MenuSubModulo;
use App\Models\Seguridad\ModuleRol;


class MenuController extends Controller
{
    public function index($empId , $rolId)
    {   

        $menu = ModuleRol::select('*')
            ->join('segu_modulo', 'segu_emp_mol_rol.molId', '=', 'segu_modulo.molId')
            ->where('segu_emp_mol_rol.rolId', $rolId)
            ->where('segu_emp_mol_rol.empId', $empId)
            ->orderBy('segu_emp_mol_rol.molId', 'asc')
            ->get();    

        $datos = [];

        $menu->each(function ($items) use (&$datos , &$empId , &$rolId) {
           
            $opciones         = $this->procesarOpciones( $items->molId, $empId , $rolId );  
            $mergedCollection = $opciones;
          
            $datos[] = [
                'molId'    => $items->molId,
                'rolId'    => $items->rolId,
                'molDes'   => $items->molDes,
                'molIcon'  => $items->molIcon,
                'opciones' => $mergedCollection
            ];
        });

        return $datos;
    }

    private function procesarOpciones($molId , $empId , $rolId ) {

    $opciones = MenuRol::select('*')
        ->where('rolId', $rolId)
        ->where('empId', $empId)
        ->where('molId', $molId)
        ->orderBy('optId', 'asc')
        ->get();   

    $menu      = [];
    $childrens = [];
    // Procesar opciones
   $opciones->each(function ($item) use (&$menu) {
        $data = [
            'optId'       => 0,
            'optDes'      => $item->optDes,
            'optLink'     => $item->optLink,
            'optSub'      => 'N',
            'molId'       => $item->molId,
            'childrens'   => [],
        ];

        $menu[] = $data;
    });

    // Procesar subopciones
    $data = MenuSubModulo::select('*')
    ->where('rolId', $rolId)
    ->where('empId', $empId)
    ->where('molId', $molId)
    ->get();
    
    $datosAgrupados = collect($data)->groupBy('molsDes');
  
    foreach($datosAgrupados as $item){

        $molsDes   = $item[0]['molsDes'];    
        $childrens = [];
        foreach($item as $chil){ 
            if($molsDes == $chil->molsDes){
              $childrens [] =   [
                    'name'           => $chil->optDes,
                    'url'            => $chil->optLink,
                    'icon'           =>  'pi-fj pi pi-circle',
                    
                ];
            }
        }
        $data = [
            'optId'       => 0,
            'optDes'      => $molsDes,
            'optLink'     => $molsDes,
            'optSub'      => 'S',
            'molId'       => $molId,
            'childrens'   => $childrens
        ];

        $menu[] = $data;
      
    }

    
    return $menu;
    }

  
 /*   public function indexRolOpt(Request $request)
    {
        $datos = ModuleOpt::select('*')
            ->join('roles_opt', 'roles_mod_opt.idOpt', '=', 'roles_opt.idOpt')
            ->join('roles', 'roles_mod_opt.idRol', '=', 'roles.idRol')
            ->join('roles_module', 'roles_mod_opt.idMol', '=', 'roles_module.idMol')
            ->where('roles_mod_opt.empId', 1)
            ->get();
        return response()->json($datos, 200);
    }*/

   
}
