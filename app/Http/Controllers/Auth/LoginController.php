<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Si ya está autenticado, redirigir al dashboard correspondiente
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user()->Id_Rol);
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'Correo' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
        'Correo' => $request->Correo,
        'password' => $request->password, 
        'Activo' => 1
        ];  

        if (Auth::attempt($credentials, $request->has('remember'))) {
        return $this->redirectToDashboard(Auth::user()->Id_Rol);
    }

    // Si la autenticación falla, verificamos si el usuario existe pero está inactivo
    $usuario = Usuario::where('Correo', $request->Correo)->first();

    if ($usuario && $usuario->Activo == 0) {
        return back()->with('error', 'Usuario inactivo. Contacte al administrador.');
    }

        return back()->with('error', 'Credenciales incorrectas.');
}

    protected function redirectToDashboard($rolId)
    {
        switch ($rolId) {
            case 1: return redirect()->route('admin.dashboard');
            case 2: return redirect()->route('operativo.dashboard');
            case 3: return redirect()->route('caja.dashboard');
            default: 
                Auth::logout();
                return redirect()->route('login')->with('error', 'Rol no válido');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}