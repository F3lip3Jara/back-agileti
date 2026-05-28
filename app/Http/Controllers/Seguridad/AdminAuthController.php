<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Mostrar el formulario de inicio de sesión administrativo.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->rolId === 1) {
                return redirect()->intended('/admin/dashboard');
            }
        }
        return view('auth.admin-login');
    }

    /**
     * Mostrar el panel de control con las herramientas administrativas.
     */
    public function showDashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Procesar la solicitud de inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Validar estrictamente el Rol SUPER (rolId = 1)
            if ($user->rolId === 1 && trim($user->activado) === 'A') {
                $request->session()->regenerate();
                return redirect()->intended('/admin/dashboard');
            }

            // Si no es SUPER, cerrar sesión de inmediato y rechazar
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'access' => 'Acceso restringido. Solo los usuarios con rol SUPER pueden ingresar.',
            ]);
        }

        return back()->withErrors([
            'name' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Cerrar la sesión administrativa.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
