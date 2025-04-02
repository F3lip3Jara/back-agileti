<?php

namespace Database\Seeders;

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
use App\Models\SubOpciones;
use App\Models\Parametros\UnidadMed;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
           
            'empDes'  =>'Agileticl EIRL',
            'empDir'  =>'Av Altamirano 1419',
            'empRut'  =>'76.350.147-7',
            'empGiro' =>'Desarrollo software',
            'empFono' => '+569997551015',
            'empImg'  => ''
          ]);


        $faker = Faker::create();
        
       
        Etapa::create([
                'empId'  =>1,
                'etaDes' => 'MEZCLA' ,
                'etaProd'=> 'S']);

        Etapa::create([
                'empId'  =>1,
                'etaDes' => 'EXTRUSIÓN' ,
                'etaProd'=> 'S']);

        Etapa::create([
                'empId'  =>1,
                'etaDes' => 'TERMOFORMADO' ,
                'etaProd'=> 'S']);
    
        Etapa::create([
                'empId'  =>1,
                'etaDes' => 'ENVASADO' ,
                'etaProd'=> 'S']);

        Etapa::create([
                'empId'  =>1,
                'etaDes' => 'INYECCIÓN' ,
                'etaProd'=> 'S']);

        Etapa::create([
                'empId'  =>1,
                'etaDes' => 'IMPRESIÓN' ,
                'etaProd'=> 'S']);


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
             Opciones::create(array(                 
                 'optDes'   => $obj->optDes,
                 'optLink'  => $obj->optLink              
             ));
         }

         //Acciones 
         $json = file_get_contents("database/data_prd/Acciones.json");
         $data = json_decode($json);
         foreach ($data as $obj) {
             Acciones::create(array(                 
                 'accDes'     => $obj->accDes,
                 'accUrl'     => $obj->accUrl,
                 'accetaDes'  => $obj->accetaDes,
                 'acceVig'    => $obj->acceVig,  
                 'optId'      => $obj->optId,
                 'accType'    => $obj->accType,
                 'accMessage' => $obj->accMessage            
             ));
         }

    
        //Sub Opciones
       /* $json = file_get_contents("database/data_prd/Sub_Opt.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            SubOpciones::create(array(
                'optId'     => $obj->idOpt,
                'optSDes'   => $obj->optSDes, 
                'optSLink'  => $obj->optSLink              
            ));
        }
*/
        
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


  /*      $json = file_get_contents("database/data_prd/Menu.json");
        $data = json_decode($json);
    
        foreach ($data as $request) {
            ModuleOpt::create([          
                'empId'=>1,
                'idRol'=>$request->idRol,
                'idMol'=>$request->idMol,
                'idOpt'=>$request->idOpt
            ]); 
        }*/ 

    /*    $json = file_get_contents("database/data_prd/Maquina.json");
        $data = json_decode($json);

        foreach($data as $request){
            Maquinas::create(['idEta' => $request->idEta ,
                               'maqCod'=> $request->maqCod ,
                               'maqTip'=> '',
                               'maqDes'=> $request->maqDes ,
                               'empId'=> 1 ]);
        }

        $json = file_get_contents("database/data_prd/Motivo.json");
        $data = json_decode($json);

        foreach($data as $request){
            MovRechazo::create([
                'motDes' => $request->motDes,
                'empId'  =>1,
                'etaID'  => $request->idEta
            ]);
        }
*/
      

      
 
      //COLORES
      $json = file_get_contents("database/data_prd/Color.json");
      $data = json_decode($json);
      foreach ($data as $obj) {
          Color::create(array(
              'colCod' => $obj->ColCod,
              'colDes' => $obj->ColDes,
              'empId'  => 1                
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

        
        $faker = Faker::create();      
        $json = file_get_contents("database/data_prd/Productos.json");
        $data = json_decode($json);
    
        foreach ($data as $request) {

            /*'idMon'    => $request->idMon,
            'idGrp'    => $request->idGrp,
            'idSubGrp' => $request->idSubGrp,
            'idUn'     => $request->idUn,
            'idCol'    => $request->idCol
            */

            $idMon    = 0;
            $idGrp    = 0;
            $idSubGrp = 0;
            $idCol    = 0;
            $idUn     = 0;
            
            $moneda   = Moneda::select('monId')->where('monCod', $request->monCod)->get();
            
            foreach($moneda as $itemx){
                $idMon = $itemx->monId;
            }

            $grupos = SubGrupo::select('grpId' , 'grpsId')->where('grpScod',$request->grpScod)->get();
          
            foreach($grupos as $item){
                $idGrp    = $item->grpId;
                $idSubGrp = $item->grpsId;
            }

            $xcolcod = '';
            $xcolcod = strval($request->colCod);      
            
            $colores = Color::select('colId')->where('colCod',$xcolcod)->get();
            
            foreach($colores as $color){
                $idCol = $color->colId;        
            }      
     
            $unidad = UnidadMed::select('unId')->where('unCod',$request->unCod)->get();

            foreach($unidad as $item){
                $idUn = $item->unId;
            }

        }
           

            //views
 DB::unprepared(file_get_contents('database/sqlviews/create-view-template.sql'));  


  }
}
