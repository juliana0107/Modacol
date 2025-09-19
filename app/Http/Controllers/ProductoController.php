<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index() {
        $productos = Producto::all();
        return view('productos.index', compact('productos'));
    }

    public function create() {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:100',
            'Descripcion' => 'required|string',
            'PrecioU' => 'required|numeric|min:0',
            'Cantidad' => 'required|integer|min:0'
        ]);

        Producto::create($request->all());
        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente');
    }

    public function edit($id){
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

   public function update(Request $request, $id)
    {
        $request->validate([
            'Nombre' => 'required|string|max:100',
            'Descripcion' => 'required|string',
            'PrecioU' => 'required|numeric|min:0',
            'Cantidad' => 'required|integer|min:0'
        ]);

        $producto = Producto::findOrFail($id);
        $producto->update($request->all());
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente');
    }

   public function toggle($id) {
        $producto = Producto::findOrFail($id);
        $producto->Activo = !$producto->Activo;
        $producto->save();
        return redirect()->route('productos.index')->with('success', 'Estado del producto actualizado');    } 
}