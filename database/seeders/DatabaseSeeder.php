<?php

namespace Database\Seeders;

use App\Jobs\MonedaRegularizacion;
use App\Models\FieldDefinition;
use App\Models\Seguridad\Acciones;
use Faker\Factory as Faker;
use App\Models\Parametros\BinCol;
use App\Models\Parametros\Ciudad;
use App\Models\Parametros\Color;
use App\Models\Parametros\Comuna;
use App\Models\Seguridad\Roles;
use App\Models\User;
use App\Models\Seguridad\Empresa;
use App\Models\Seguridad\EmpresaOpciones;
use App\Models\Parametros\Etapa;

use App\Models\Parametros\Grupo;
use App\Models\Parametros\Maquinas;
use App\Models\Seguridad\Module;
use App\Models\Seguridad\ModuleOpt;
use App\Models\Seguridad\ModuleRol;
use App\Models\Parametros\Moneda;
use App\Models\Parametros\MovRechazo;
use App\Models\Seguridad\Opciones;
use App\Models\Parametros\Pais;
use App\Models\Parametros\Producto;
use App\Models\Parametros\Proveedor;
use App\Models\Parametros\Region;
use App\Models\Seguridad\RolesModule;
use App\Models\Parametros\SubGrupo;
use App\Models\Parametros\Talla;
use App\Models\SubOpciones;
use App\Models\Parametros\UnidadMed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder 
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(  )
    {
       
        
        Empresa::create([
            'empId'   => 1,
            'empDes'  =>'Agileticl EIRL',
            'empDir'  =>'Av Altamirano 1419',
            'empRut'  =>'76.350.147-7',
            'empGiro' =>'Desarrollo software',
            'empFono' => '+569997551015',
            'empImg'  => ''
          ]);
   
        Roles::create([        
          'rolDes' =>'SUPER',
          'empId'  => 1
        ]);
        
       
        User::create([
            'name'      => 'SUPER',
            'email'     => 'adm@contacto.cl',
            'rolId'     => 1,
            'activado'  => 'A',
            'imgName'   => '',
            'token'     => '',
            'password'  => bcrypt('admin'),
            'empId'     => 1
         ]);

       
        Moneda::create([
            'monCod' => 'CLP',
            'monDes' => 'PESO CHILENO',
            'empId'  => 1
        ]);

     

        Moneda::create([
            'monCod'     => 'US',
            'monDes'     => 'DOLAR',
            'empId'      => 1,
            'monIntVal'  => 'dolar',
            'monIntArray'=> 'Dolares',
            'monInt'     => 'S',
        ]);

        Moneda::create([
            'monCod'     => 'UF',
            'monDes'     => 'UF',
            'empId'      => 1,
            'monIntVal'  => 'uf',
            'monIntArray'=> 'UFs',
            'monInt'     => 'S',
        ]);

       

        UnidadMed::create([
            'empId' => 1,
            'unDes' =>'UNIDAD',
            'unCod' =>'UN'
        ]);

         //OPCIONES
         $json = file_get_contents("database/data_prd/Opciones.json");
         $data = json_decode($json);
         foreach ($data as $obj) {


             $create = Opciones::create(array(                 
                 'optDes'   => $obj->optDes,
                 'optLink'  => $obj->optLink              
             ));

             $idOpt = $obj->optId;


             $json = file_get_contents("database/data_prd/Acciones.json");
             $data = json_decode($json);
    
            foreach ($data as $obj2) {
                if($obj2->optId == $idOpt){
                    Acciones::create(array(                 
                        'accDes'     => $obj2->accDes,
                        'accUrl'     => $obj2->accUrl,
                        'accetaDes'  => $obj2->accetaDes,
                        'acceVig'    => $obj2->acceVig,  
                        'optId'      => $create->id,
                        'accType'    => $obj2->accType,
                        'accMessage' => $obj2->accMessage            
                    ));
                }
            }


         }

        Module:: create([
            'empId'  => 1,
            'molDes' => 'Seguridad',
            'molIcon'=> 'pi pi-shield'

        ]);

        ModuleOpt:: create([
            'molId' => 1,
            'empId' => 1,            
            'optId' => 2
        ]);

        ModuleOpt:: create([
            'molId' => 1,
            'empId' => 1,            
            'optId' => 3
        ]);

        ModuleOpt:: create([
            'molId' => 1,
            'empId' => 1,            
            'optId' => 4
        ]);

        ModuleOpt:: create([
            'molId' => 1,
            'empId' => 1,            
            'optId' => 5
        ]);

        ModuleOpt::create([
            'molId' => 1,
            'empId' => 1,            
            'optId' => 6
        ]);

      

        EmpresaOpciones::create([
            'empId' =>1,
            'optId' =>2
        ]);
        EmpresaOpciones::create([
            'empId' =>1,
            'optId' =>3
        ]);
        EmpresaOpciones::create([
            'empId' =>1,
            'optId' =>4
        ]);
        EmpresaOpciones::create([
            'empId' =>1,
            'optId' =>5
        ]);
        EmpresaOpciones::create([
            'empId' =>1,
            'optId' =>6
        ]);
        ModuleRol::create([
            'empId' => 1,
            'molId' => 1,
            'rolId' => 1
        ]);


      
 
      //COLORES
      $json = file_get_contents("database/data_prd/Color.json");
      $data = json_decode($json);
      foreach ($data as $obj) {
          Color::create(array(
              'colCod' => $obj->colCod,
              'colDes' => $obj->colDes,
              'empId'  => $obj->empId               
          ));
      }


     //PAIS
     $json = file_get_contents("database/data_prd/Pais.json");
     $data = json_decode($json);
     foreach ($data as $obj) {
         Pais::create(array(
             'paiCod'     => $obj->Cod_Pais,
             'paiDes'     => $obj->Pais_Des,
             'empId'      => 1               
         ));
     }

     $json = file_get_contents("database/data_prd/Region.json");
     $data = json_decode($json);
 
     foreach ($data as $obj) {
         $idPai = Pais::select('paiId')->where('paiCod' , $obj->PaiCod)->get();
         $xidPai = 0;
         foreach($idPai as $item){
                 $xidPai = $item->paiId;
         }
         Region::create(array(
             'empId'  => 1,
             'paiId'  => $xidPai, 
             'regCod' => $obj->RegCod,
             'regDes' => $obj->RegDes
         ));
       }

       $json = file_get_contents("database/data_prd/Ciudad.json");
       $data = json_decode($json);
   
       foreach ($data as $obj) {
           $idPai = Region::select('parm_pais.paiId', 'parm_region.regId')
           ->join('parm_pais', 'parm_pais.paiId' , '=' , 'parm_region.paiId')
           ->where('paiCod' , $obj->PaiCod )
           ->where('regCod' , $obj->RegCod )
           ->get();

           $xidPai = 0;
           $idReg = 0;

           foreach($idPai as $item){
                   $xidPai = $item->paiId;
                   $idReg  = $item->regId;
           }
           Ciudad::create(array(
               'empId'  => 1,
               'paiId'  => $xidPai, 
               'regId'  => $idReg,
               'ciuCod' => $obj->CiuCod,
               'ciuDes' => $obj->CiuDes
           ));
         }
        
         $json = file_get_contents("database/data_prd/Comuna.json");
         $data = json_decode($json);
     
         foreach ($data as $obj) {
             $idPai = Ciudad::select('parm_pais.paiId', 'parm_region.regId', 'parm_ciudad.ciuId')
             ->join('parm_pais', 'parm_pais.paiId' , '=' , 'parm_ciudad.paiId')
             ->join('parm_region', 'parm_region.regId' , '=' , 'parm_ciudad.regId')           
             ->where('paiCod' , $obj->PaiCod )
             ->where('regCod' , $obj->RegCod )
             ->where('ciuCod' , $obj->CiuCod )
             ->get();

             $xidPai = 0;
             $idReg = 0;
             $idCiu = 0;
             foreach($idPai as $item){
                     $xidPai = $item->paiId;
                     $idReg  = $item->regId;
                     $idCiu  = $item->ciuId;
             }
             Comuna::create(array(
                 'empId'  => 1,
                 'paiId'  => $xidPai, 
                 'regId'  => $idReg,
                 'ciuId'  => $idCiu,
                 'comCod' => $obj->ComCod,
                 'comDes' => $obj->ComDes
             ));
           } 

           $json = file_get_contents("database/data_prd/Proveedor.json");
           $data = json_decode($json);
       
           foreach ($data as $request) {
          
      
          $comCod = strval($request->ComCod);
          $comCod = trim($comCod);
          $datos = Comuna::select('paiId', 'regId', 'ciuId', 'comId')
          ->where('comCod', $comCod )->get();
  
            
          foreach($datos as $item){
              $idPai = $item->paiId;
              $idReg = $item->regId;           
              $idCiu = $item->ciuId;
              $idCom = $item->comId;  
          }


          Proveedor::create([
              'empId'    => 1,
              'prvRut'   => $request->PRVRUT,
              'prvNom'   => $request->PrvNom,
              'prvNom2'  => $request->PrvNom2,
              'prvGiro'  => strval($request->PrvGiro),
              'prvDir'   => $request->PrvDir,
              'prvNum'   => $request->PrvNum,
              'prvTel'   => $request->PrvTel,
              'prvMail'  => $request->PrvMail,
              'prvCli'   => $request->prvCli,
              'prvPrv'   => $request->prvPrv,
              'paiId'    => $idPai,
              'regId'    => $idReg,
              'comId'    => $idCom,
              'ciuId'    => $idCiu,
              'prvAct'   => 'S'
           ]);         
          }
        
        //GRUPOS

        $json = file_get_contents("database/data_prd/Grupo.json");
        $data = json_decode($json);      
        foreach ($data as $request) {   
            $affected = Grupo::create([
                'grpCod' => $request->GrpCod,
                'grpDes' => $request->GrdDes,
                'empId'  =>1
            ]);        
        }

        //SUB GRUPO
        $json = file_get_contents("database/data_prd/SubGrupo.json");
        $data = json_decode($json);
    
        foreach ($data as $request) {
            $xgrpCod = $request->GrpCod;

            $datos = Grupo::select('grpId')->where('grpCod', $xgrpCod )->get();   
            
            foreach($datos as $item){
                $idGrp = $item->grpId;
            }

            SubGrupo::create([
                'grpId'   => $idGrp,
                'empId'   => 1,
                'grpsCod' => $request->GrpScod,
                'grpsDes' => $request->GrpSDes
            ]);
        }


        //Tallas
        
        Talla::create([
            'empId' => 1,
            'tallaCod' => 'S',
            'tallaDes' => 'S'
        ]);
        Talla::create([
            'empId' => 1,
            'tallaCod' => 'M',
            'tallaDes' => 'M'
        ]);
        Talla::create([
            'empId' => 1,
            'tallaCod' => 'L',
            'tallaDes' => 'L'
        ]);

        Talla::create([
            'empId' => 1,
            'tallaCod' => 'XL',
            'tallaDes' => 'XL'
        ]);



        //Color

        $json = file_get_contents("database/data_prd/Color.json");
        $data = json_decode($json);

        foreach ($data as $request) {
            Color::create([
                'empId' => 1,   
                'colCod' => $request->colCod,
                'colDes' => $request->colDes
            ]);
        }
        

        //Productos
        $json = file_get_contents("database/data_prd/Productos.json");
        $data = json_decode($json);

        foreach ($data as $request) {
            Producto::create([
             
                    'prdCod' => $request->prdCod,
                    'prdDes' => $request->prdDes,
                    'prdObs' => $request->prdObs,
                    'prdRap' => $request->prdRap,
                    'prdEan' => $request->prdEan,
                    'prdTip' => $request->prdTip,
                    'prdCost' => $request->prdCost,
                    'prdNet' => $request->prdNet,
                    'prdBrut' => $request->prdBrut,
                    'prdInv' => $request->prdInv,
                    'prdPes' => $request->prdPes,
                    'prdMin' => $request->prdMin,
                    'monId' => $request->monId,
                    'grpId' => $request->grpId,
                    'grpsId' => $request->grpsId,
                    'unId' => $request->unId,
                    'colId' => $request->colId,
                    'empId' => 1,
                    'prdIdExt' => $request->prdIdExt,
                    'prdUrl' => $request->prdUrl,
                    'prdMig' => $request->prdMig,
                    'tallaId' => $request->tallaId,
                    'prdAncho' => $request->prdAncho,
                    'prdLargo' => $request->prdLargo,
                    'prdAlto' => $request->prdAlto,
                    'prdPeso' => $request->prdPeso,
                    'prdVolumen' => $request->prdVolumen

            ]);
        }
        //views
        DB::unprepared(file_get_contents('database/sqlviews/create-view-template.sql'));  


        // Obtener todas las tablas de la base de datos (MySQL específico)
        $tables = DB::select('SHOW TABLES');
        $databaseName = DB::getDatabaseName();
        $tableExcluded = ['field_definitions' , 'cache' , 'cache_locks' , 'failed_jobs' ,   'job_batches' , 'jobs' , 'migrations' , 'sessions' , 'telescope_entries' , 'telescope_entries_tags' , 'telescope_monitoring' , 'webhook_oms'];

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

        $job = new MonedaRegularizacion();
        dispatch($job); 

  }

  protected function getColumnType($table, $column)
  {
    return DB::getSchemaBuilder()->getColumnType($table, $column);
  }
}
