<?php
namespace App\Http\Controllers\Parametros;
use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\CalendarioJul;
use Carbon\Carbon;
use Error;
use Exception;
use Illuminate\Http\Request;

class CalendarioJulController extends Controller
{

    public function index(Request $request)
    {
        $data = $request->all();
        $ano  = $data['ano'];
        return CalendarioJul::select('*')->FiltrarOtroValor('calAno', $ano)->get();
    }
    public function ins(Request $request)
    {
        $name        = $request['name'];
        $empId       = $request['empId'];
        $data        = $request->all();
        $ano         = $data['0']['ano'];    
        $obj         = $data['0']['detalle']; 
        

        foreach ($obj as $itemx) {
            $itemx['Dia'];

            for ($i = 1; $i <= 12; $i++) {

                switch ($i) {
                    case 1:
                        $calMes   = $i;
                        $calDes   = 'Enero';
                        $calValor =  $itemx['Enero'];
                        break;
                    case 2:

                        try {
                            $calMes   = $i;
                            $calDes   = 'Febrero';
                            $calValor =  $itemx['Febrero'];
                        } catch (Exception  $e) {
                            $calMes   = $i;
                            $calDes   = 'Febrero';
                            $calValor =  '';
                        }

                        break;
                    case 3:
                        $calMes   = $i;
                        $calDes   = 'Marzo';
                        $calValor =  $itemx['Marzo'];
                        break;
                    case 4:
                        try {
                            $calMes   = $i;
                            $calDes   = 'Abril';
                            $calValor =  $itemx['Abril'];
                        } catch (Exception $e) {
                            $calMes   = $i;
                            $calDes   = 'Abril';
                            $calValor =  '';
                        }
                        break;
                    case 5:
                        $calMes   = $i;
                        $calDes   = 'Mayo';
                        $calValor =  $itemx['Mayo'];
                        break;
                    case 6:
                        try {
                            $calMes   = $i;
                            $calDes   = 'Junio';
                            $calValor =  $itemx['Junio'];
                        } catch (Exception $e) {
                            $calMes   = $i;
                            $calDes   = 'Junio';
                            $calValor = '';
                        }
                        break;
                    case 7:
                        $calMes   = $i;
                        $calDes   = 'Julio';
                        $calValor =  $itemx['Julio'];
                        break;
                    case 8:
                        $calMes   = $i;
                        $calDes   = 'Agosto';
                        $calValor =  $itemx['Agosto'];
                        break;
                    case 9:
                        try {
                            $calMes   = $i;
                            $calDes   = 'Septiembre';
                            $calValor =  $itemx['Septiembre'];
                        } catch (Exception $e) {
                            $calMes   = $i;
                            $calDes   = 'Septiembre';
                            $calValor = '';
                        }
                        break;
                    case 10:
                        $calMes   = $i;
                        $calDes   = 'Octubre';
                        $calValor =  $itemx['Octubre'];
                        break;
                    case 11:
                        try {
                            $calMes   = $i;
                            $calDes   = 'Noviembre';
                            $calValor =  $itemx['Noviembre'];
                        } catch (Exception $e) {
                            $calMes   = $i;
                            $calDes   = 'Noviembre';
                            $calValor = '';
                        }
                        break;
                    case 12:
                        $calMes   = $i;
                        $calDes   = 'Diciembre';
                        $calValor =  $itemx['Diciembre'];
                        break;
                }

                $affected = CalendarioJul::create([
                    'empId'     => 1,
                    'calAno'    => $ano,
                    'calMes'    => $calMes,
                    'calMesDes' => $calDes,
                    'calDia'    => $itemx['Dia'],
                    'calValor'  => $calValor
                ]);
            }
        }
        if (isset($affected)) {
            $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accetaDes']);
            dispatch($job);    
            $resources = array(
                    array("error" => '0', 'mensaje' => $request->log['0']['accMessage'], 'type' => $request->log['0']['accType'])
                );
            return response()->json($resources, 200);
        }
    }
    public function valCal(Request $request)
    {
        $dia    = 0;
        $data = $request->all();
        $ano  = $data['ano'];
        $dia  = CalendarioJul::all()->where('calAno', $ano)->take(1);
        return $dia;           
    }

    public function del(Request $request)
    {
        $data        = $request->all(); 
        $name        = $request['name'];
        $empId       = $request['empId'];
        $ano         = $data['0']['ano'];       
        $affected    = CalendarioJul::where('calAno', $ano)->delete();
        $this->ins($request);
        $job = new LogSistema( $request->log['0']['optId'] , $request->log['0']['accId'] , $name , $empId , $request->log['0']['accetaDes']);
        dispatch($job);    
        
    }

    public function busUltAno(Request $request)
    {
        $ano = CalendarioJul::select('calAno')->orderby('calId', 'DESC')->take(1)->get();
        return $ano;
    }

    public function diaJul(Request $request)
    {
        $fecha = Carbon::now();
        $dia   = CalendarioJul::select('calValor')
            ->where('calDia', $fecha->day)
            ->where('calMes', $fecha->month)
            ->where('calAno', $fecha->year)->get();

        $cod      = '0';

        foreach ($dia as $item) {
            try {
                $calValor = $item->calValor;

                if ($calValor < 100) {
                    $calValor = '0' . $item->calValor;
                }
                $ano      = substr($fecha->year, -2);
                $cod      = $calValor . $ano;
            } catch (Error $er) {
                $calValor = '';
            }
        }
    }
}
