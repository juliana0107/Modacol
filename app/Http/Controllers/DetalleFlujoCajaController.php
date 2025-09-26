<?php

namespace App\Http\Controllers;

use App\Models\DetalleFlujoCaja;
use App\Models\FlujoCaja;
use Illuminate\Http\Request;

class DetalleFlujoCajaController extends Controller
{
    // Mostrar todos los detalles de flujo de caja
    public function index(Request $request)
    {
        $detalles = DetalleFlujoCaja::with(['flujoCaja', 'cliente', 'proveedor', 'categoria'])->get();
        return view('detalleFlujoCaja.index', compact('detalles'));
    }

    // Crear un nuevo detalle de flujo de caja
    public function create()
    {
        $flujoCajas = FlujoCaja::all();
        return view('detalleFlujoCaja.create', compact('flujoCajas'));
    }

    // Almacenar un nuevo detalle de flujo de caja
    public function store(Request $request)
    {
        $request->validate([
            'Flujo_Id' => 'required|exists:flujo_caja,Id',
            'Fecha' => 'required|date',
            'Tipo_Movimiento' => 'required|in:Ingreso,Egreso',
            'Monto' => 'required|numeric',
            'Categoria_Id' => 'required|exists:categorias,Id',
        ]);

        DetalleFlujoCaja::create($request->all());
        return redirect()->route('detalleFlujoCaja.index')->with('success', 'Detalle de flujo de caja creado correctamente');
    }

    // Editar un detalle de flujo de caja
    public function edit($id)
    {
        $detalle = DetalleFlujoCaja::findOrFail($id);
        $flujoCajas = FlujoCaja::all();
        return view('detalleFlujoCaja.edit', compact('detalle', 'flujoCajas'));
    }

    // Actualizar un detalle de flujo de caja
    public function update(Request $request, $id)
    {
        $request->validate([
            'Flujo_Id' => 'required|exists:flujo_caja,Id',
            'Fecha' => 'required|date',
            'Tipo_Movimiento' => 'required|in:Ingreso,Egreso',
            'Monto' => 'required|numeric',
            'Categoria_Id' => 'required|exists:categorias,Id',
        ]);

        $detalle = DetalleFlujoCaja::findOrFail($id);
        $detalle->update($request->all());
        return redirect()->route('detalleFlujoCaja.index')->with('success', 'Detalle de flujo de caja actualizado correctamente');
    }
}
