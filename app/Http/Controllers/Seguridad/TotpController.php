<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Jobs\LogSistema;
use App\Models\Parametros\Empleado;
use App\Models\Seguridad\Empresa;
use App\Models\Seguridad\Roles;
use Illuminate\Http\Request;
use App\Models\User;
use OTPHP\TOTP;
use Milon\Barcode\DNS2D;
use Illuminate\Support\Str;

class TotpController extends Controller
{
    /**
     * Verificar si un usuario ya tiene TOTP configurado
     */
    public function status(Request $request)
    {
        $request = request()->all();
        $username = $request['username'];

        $user = User::where('name', $username)->first();
        
        if (!$user) {
            return response()->json([
                'error' => 'Usuario no encontrado'
            ], 404);
        }
        
        return response()->json([
            'hasTotp' => $user->twofa_enabled ?? false
        ]);
    }

    /**
     * Configurar TOTP para un usuario
     */
    public function setup(Request $request)
    {
        $request = request()->all();
        $username = $request['username'];

        $user = User::where('name', $username)->first();
        
        if (!$user) {
            return response()->json([
                'error' => 'Usuario no encontrado'
            ], 404);
        }
        
        if ($user->twofa_enabled) {
            return response()->json([
                'error' => 'TOTP ya está configurado para este usuario'
            ], 400);
        }

        // Generar secret y códigos de respaldo
        $secret = $this->generateSecret();
        $backupCodes = $this->generateBackupCodes();
        
        // Generar código QR - usar el campo correcto
        $qrCodeUrl = $this->generateQrCode($user->name, $secret);
        
        // Guardar secret temporalmente (no activar hasta verificación)
        $user->update([
            'twofa_secret' => $secret,
            'backup_codes' => json_encode($backupCodes)
        ]);

        return response()->json([
            'qrCode' => $qrCodeUrl,
            'secret' => $secret,
            'backupCodes' => $backupCodes
        ]);
    }

    /**
     * Verificar código TOTP
     */
    public function verify(Request $request)
    {
        $request = request()->all();
        $username = $request['username'];
        $code = $request['code'];

        $user = User::where('name', $username)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
        
        if (!$user->twofa_secret) {
            return response()->json([
                'success' => false,
                'message' => 'TOTP no configurado para este usuario'
            ], 400);
        }

        // Verificar código TOTP
        $valid = $this->verifyTotpCode($user->twofa_secret, $code);
        
        if ($valid) {
            // Si es la primera verificación, activar TOTP
            if (!$user->twofa_enabled) {
                $user->update(['twofa_enabled' => true]);
            }
            $token = Str::random(60);
            $user  = User::where('name', $username)->first();
            $activo = trim($user->activado);

            if ($activo == 'A') {
                $idUser = $user->id;
                User::where('id', $idUser)
                    ->update(['token' => $token]);             
                 
                $crf = csrf_token();
                $imgx = Empleado::select('emploAvatar', 'emploNom' , 'emploApe')->where('id', $idUser)->get();

                if(sizeof($imgx) > 0){
                  $img    = $imgx[0]['emploAvatar'];
                  $empNom = $imgx[0]['emploNom'];
                  $empApe = $imgx[0]['emploApe'];
                }else{
                    $img = '';
                }
               
                $xrol           =  Roles::select('rolDes')->where('rolId', $user->rolId)->get();
                $rol            =  $xrol[0]['rolDes'];                
                $xempresa       =  Empresa::select('empDes', 'empImg')->where('empId', $user->empId)->get();
                $empresa        =  $xempresa[0]['empDes'];
                $imgEmp         =  '';
                $controller     =  new MenuController;
                $menu           =  $controller->index($user->empId , $user->rolId);   
              
                $resources =
                    array(
                        'id'       => $user->id,
                        'name'     => $user->name,
                        'token'    => $token,
                        'reinicio' => $user->reinicio,
                        'crf'      => $crf,
                        'img'      => $img,
                        'rol'      => $rol,
                        'empresa'  => $empresa,
                        'menu'     => $menu,
                        'imgEmp'   => $imgEmp,
                        'empNom'   => $empNom,
                        'empApe'   => $empApe,
                        'error'    => '0'
                    );
                
                  $etaId    = 1;
                  $etaDesId = 1;
                  $name     = $user->name;
                  $empId    = $user->empId; 
                 // $encrypted =bcrypt($resources);
                $job = new LogSistema($etaId , $etaDesId , $name , $empId , 'LOGEO DE USUARIO POR TOTP' , 'success');
                  dispatch($job);
               //   event(new MensajeEvent('Hola desde el servidor'));
                
               
                return response()->json([
                    'success' => true,
                    'message' => 'Código TOTP válido',
                    'resources' => $resources
                ]);
            }

        
        $etaId    = 1;
        $etaDesId = 3;
        $name     = $username;
        $empId    = 0;
        $job = new LogSistema($etaId , $etaDesId , $name , $empId , 'Error usuario no logeado' , 'success');
        dispatch($job);


        return response()->json([
            'success' => false,
            'message' => 'Código TOTP inválido'
        ], 400);
    }
 }

    /**
     * Desactivar TOTP
     */
    public function disable(Request $request)
    {
        $request = request()->all();
        $username = $request['username'];

        $user = User::where('name', $username)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
        
        $user->update([
            'twofa_secret' => null,
            'twofa_enabled' => false,
            'backup_codes' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'TOTP desactivado correctamente'
        ]);
    }

    /**
     * Generar secret TOTP
     */
    private function generateSecret()
    {
        $totp = TOTP::create();
        return $totp->getSecret();
    }

    /**
     * Generar códigos de respaldo
     */
    private function generateBackupCodes($count = 8)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }
        return $codes;
    }

    /**
     * Generar código QR
     */
    private function generateQrCode($username, $secret)
    {
        try {
            $companyName = 'Agileti';
            $totp = TOTP::create($secret);
            $totp->setLabel($username);
            $totp->setIssuer($companyName);
            $dns2d = new DNS2D();
            $otpauth = $totp->getProvisioningUri();
            $qrcode = $dns2d->getBarcodeSVG($otpauth, 'QRCODE', 5, 5);
            
            // Usar SimpleSoftwareIO\QrCode como alternativa más simple
          
            return 'data:image/svg+xml;base64,' . base64_encode($qrcode);
            
        } catch (\Exception $e) {
            // Fallback: generar QR simple sin dependencias externas
            $dns2d = new DNS2D();
            $otpauth = "otpauth://totp/{$username}?secret={$secret}&issuer=Agileti";
            $qrcode = $dns2d->getBarcodeSVG($otpauth, 'QRCODE', 5, 5);
            
            return 'data:image/svg+xml;base64,' . base64_encode($qrcode);
        }
    }

    /**
     * Verificar código TOTP
     */
    private function verifyTotpCode($secret, $code)
    {
        $totp = TOTP::create($secret);
       
        return $totp->verify($code);
    }
}
