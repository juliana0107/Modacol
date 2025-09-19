<?php
// app/Http/Controllers/CajaController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }

        if (Auth::user()->Id_Rol !== 3) {
            return redirect()->route('login')->with('error', 'Acceso no autorizado para caja');
        }

        return view('caja.dashboard', [
            'usuario' => Auth::user()
        ]);
    }
}