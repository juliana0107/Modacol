<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;

class DetalleVentaController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DetalleVenta $detalleVenta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DetalleVenta $detalleVenta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    DB::beginTransaction();

    try {
        // Encuentra la venta
        $venta = Venta::findOrFail($id);

        // Paso 1: Restaurar stock de productos previamente vendidos en esta venta.
        $detallesAntiguos = DetalleVenta::where('Venta_id', $venta->Id)->get();
        foreach ($detallesAntiguos as $detalle) {
            $producto = Producto::find($detalle->Producto_id);
            // Restaurar el stock de los productos vendidos anteriormente
            $producto->Cantidad += $detalle->Cantidad;
            $producto->save();
        }

        // Paso 2: Eliminar los detalles antiguos de la venta (no se deben eliminar antes de restaurar el stock).
        DetalleVenta::where('Venta_id', $venta->Id)->delete();

        // Paso 3: Crear nuevos detalles de la venta
        foreach ($request->detalles as $detalle) {
            $producto = Producto::find($detalle['Producto_id']);
            
            // Verificar si hay suficiente stock para la cantidad solicitada
            if ($producto->Cantidad < $detalle['Cantidad']) {
                return redirect()->back()->withErrors(['error' => 'No hay suficiente stock para el producto: ' . $producto->Nombre]);
            }
            
            // Actualizamos el stock restando la cantidad vendida
            $producto->Cantidad -= $detalle['Cantidad'];
            $producto->save();

            // Crear un nuevo detalle de la venta
            $subTotal = $detalle['Cantidad'] * $producto->PrecioU;
            $iva = $subTotal * ($producto->Iva / 100);
            
            $detalleVenta = new DetalleVenta();
            $detalleVenta->Venta_id = $venta->Id;
            $detalleVenta->Producto_id = $producto->Id;
            $detalleVenta->Cantidad = $detalle['Cantidad'];
            $detalleVenta->SubTotal = $subTotal;
            $detalleVenta->Iva = $iva;
            $detalleVenta->save();
        }

        // Commit de la transacción si todo fue exitoso
        DB::commit();

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente.');

    } catch (\Exception $e) {
        // Rollback en caso de error
        DB::rollBack();
        return redirect()->back()->withErrors(['error' => 'Error en la actualización de la venta: ' . $e->getMessage()]);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    private function calcularTotalVenta($detalles) {
    $total = 0;
    foreach ($detalles as $detalle) {
        $producto = Producto::find($detalle['Producto_id']);
        if ($producto) {
            $subtotal = $detalle['Cantidad'] * $producto->PrecioU;
            $iva = $subtotal * ($producto->Iva / 100);
            $total += $subtotal + $iva;
        }
    }
    return $total;
}

}
