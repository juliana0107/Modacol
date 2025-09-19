<?php
// app/Http/Controllers/AdminController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Verificación manual de autenticación
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        // Verificar rol
        if (Auth::user()->Id_Rol !== 1) {
            return redirect()->route('login')->with('error', 'Acceso no autorizado para administrador');
        }

        return view('admin.dashboard', [
            'usuario' => Auth::user()
        ]);
    }
}