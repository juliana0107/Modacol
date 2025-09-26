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
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FlujoCajaController;

// Ruta principal - redirige al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas públicas (para invitados)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
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


    Route::get('/flujos-caja/exportar/csv', [FlujoCajaController::class, 'downloadReport'])->name('flujoCaja.exportCsv');
    Route::get('/flujos-caja/exportar/excel', [FlujoCajaController::class, 'exportarExcel'])->name('flujoCaja.exportExcel');

    Route::get('/flujoCaja/{id}/edit', [FlujoCajaController::class, 'edit'])->name('flujoCaja.edit');
    Route::get('/flujoCaja/{id}', [FlujoCajaController::class, 'show'])->name('flujoCaja.show');


    Route::get('productos/exportar/excel', [ProductoController::class, 'exportarExcel'])->name('productos.exportar.excel');
    Route::get('usuarios/report', [UsuarioController::class, 'downloadReport'])->name('usuarios.report');
    Route::get('usuarios/exportar/excel', [UsuarioController::class, 'exportarExcel'])->name('usuarios.exportar.excel');
    Route::get('clientes/exportar-excel', [ClienteController::class, 'exportarExcel'])->name('clientes.exportar-excel');
    Route::get('ventas/exportar/todo', [VentaController::class, 'exportarTodo'])->name('ventas.exportarTodo');
    Route::get('ventas/exportar/detallado', [VentaController::class, 'exportarDetallado'])->name('ventas.exportarDetallado');    
    Route::get('proveedores/export/all', [ProveedorController::class, 'exportAll'])->name('proveedores.export.all');
    Route::get('proveedores/export/filtered', [ProveedorController::class, 'exportFiltered'])->name('proveedores.export.filtered');
    Route::get('compras/exportar-todo', [CompraController::class, 'exportarTodo'])->name('compras.exportarTodo');
    Route::get('compras/exportar-detallado', [CompraController::class, 'exportarDetallado'])->name('compras.exportarDetallado');
    

    // Rutas CRUD protegidas (accesibles según los middlewares de cada controlador)
    Route::resource('usuarios', UsuarioController::class);
    Route::resource('clientes', ClienteController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('ventas', VentaController::class);
    Route::resource('proveedores', ProveedorController::class);
    Route::resource('compras', CompraController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('flujoCaja', FlujoCajaController::class);

    Route::resource('operativo.cruds.usuarios', UsuarioController::class);
    Route::resource('operativo.cruds.clientes', ClienteController::class);
    Route::resource('operativo.cruds.productos', ProductoController::class);
    Route::resource('operativo.cruds.ventas', VentaController::class);
    Route::resource('operativo.cruds.proveedores', ProveedorController::class);
    Route::resource('operativo.cruds.compras', CompraController::class);
    
    // Rutas toggle protegidas
    Route::post('productos/{id}/toggle', [ProductoController::class, 'toggle'])->name('productos.toggle');
    Route::post('clientes/{id}/toggle', [ClienteController::class, 'toggle'])->name('clientes.toggle');
    Route::post('ventas/{id}/toggle', [VentaController::class, 'toggle'])->name('ventas.toggle');
    Route::post('usuarios/{id}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
    Route::post('proveedores/{id}/toggle', [ProveedorController::class, 'toggle'])->name('proveedores.toggle');
    Route::post('compras/{id}/toggle', [CompraController::class, 'toggle'])->name('compras.toggle');
    Route::post('categorias/{id}/toggle', [CategoriaController::class, 'toggle'])->name('categorias.toggle');
    Route::post('flujoCaja/{id}/toggle', [FlujoCajaController::class, 'toggle'])->name('flujoCaja.toggle');  

    Route::prefix('categorias')->name('categorias.')->middleware('auth')->group(function () {
    Route::get('/', [CategoriaController::class, 'index'])->name('index');
    Route::post('/', [CategoriaController::class, 'store'])->name('store');
    Route::get('{id}', [CategoriaController::class, 'show'])->name('show');
    Route::put('{id}', [CategoriaController::class, 'update'])->name('update');
    Route::post('toggle/{id}', [CategoriaController::class, 'toggle'])->name('toggle');
    Route::get('exportar-excel', [CategoriaController::class, 'exportarExcel'])->name('exportar-excel');

});




});