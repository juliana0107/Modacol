<?php
// app/Http/Controllers/OperativoController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperativoController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        if (Auth::user()->Id_Rol !== 2) {
            return redirect()->route('login')->with('error', 'Acceso no autorizado para operativo');
        }

        return view('operativo.dashboard', [
            'usuario' => Auth::user()
        ]);
    }
}