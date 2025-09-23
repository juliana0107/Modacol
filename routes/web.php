<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OperativoController;
use App\Http\Controllers\CajaController;

// Ruta principal - redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas públicas (para invitados)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    // Si necesitas registro, descomenta estas líneas:
    // Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    // Route::post('register', [RegisterController::class, 'register']);
    
    // Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboards según rol
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/operativo/dashboard', [OperativoController::class, 'dashboard'])->name('operativo.dashboard');
    Route::get('/caja/dashboard', [CajaController::class, 'dashboard'])->name('caja.dashboard');
    
    // Redirección automática según rol
    Route::get('/redirect', function () {
        $usuario = auth()->user();
        switch ($usuario->Id_Rol) {
            case 1: return redirect()->route('admin.dashboard');
            case 2: return redirect()->route('operativo.dashboard');
            case 3: return redirect()->route('caja.dashboard');
            default: return redirect()->route('login')->with('error', 'Rol no válido');
        }
    })->name('redirect');
    
    // Rutas CRUD protegidas (accesibles según los middlewares de cada controlador)
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('ventas', VentaController::class);
    Route::resource('proveedores', ProductoController::class);
    Route::resource('compras', CompraController::class);
    
    // Rutas toggle protegidas
    Route::post('productos/{id}/toggle', [ProductoController::class, 'toggle'])->name('productos.toggle');
    Route::post('clientes/{id}/toggle', [ClienteController::class, 'toggle'])->name('clientes.toggle');
    Route::post('ventas/{id}/toggle', [VentaController::class, 'toggle'])->name('ventas.toggle');
    Route::post('usuarios/{id}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
    Route::post('proveedores/{id}/toggle', [ProveedorController::class, 'toggle'])->name('ventas.toggle');
    Route::post('compras/{id}/toggle', [CompraController::class, 'toggle'])->name('usuarios.toggle');

    Route::get('usuarios/report', [UsuarioController::class, 'downloadReport'])->name('usuarios.report');


});