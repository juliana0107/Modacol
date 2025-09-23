<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Producto;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function index() {
        $ventas = Venta::with('cliente', 'usuario')->get();
        return view('ventas.index', compact('ventas'));
    }

    public function create() {
        $clientes = Cliente::all();
        $usuarios = Usuario::all();
        $productos = Producto::where('Activo', true)->get();
        return view('ventas.create', compact('clientes', 'usuarios', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Fecha' => 'required|date',
            'Cliente_id' => 'required|exists:Clientes,Id',
            'Usuario_id' => 'required|exists:Usuarios,Id',
            'ValorTotal' => 'required|numeric|min:0'
        ]);

        Venta::create($request->all());
        return redirect()->route('ventas.index')->with('success', 'Venta creada correctamente');
    }

   public function edit($id)
    {
        $venta = Venta::findOrFail($id);
        $clientes = Cliente::where('Activo', true)->get();
        $usuarios = Usuario::where('Activo', true)->get();
        
        return view('ventas.edit', compact('venta', 'clientes', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Fecha' => 'required|date',
            'Cliente_id' => 'required|exists:Clientes,Id',
            'Usuario_id' => 'required|exists:Usuarios,Id',
            'ValorTotal' => 'required|numeric|min:0'
        ]);

        $venta = Venta::findOrFail($id);
        $venta->update($request->all());
        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente');
    }
public function toggle($id){
        $venta = Venta::findOrFail($id);
        $venta->Activo = !$venta->Activo;
        $venta->save();
        return redirect()->route('ventas.index')->with('success', 'Estado de la venta actualizado');
     }
}
