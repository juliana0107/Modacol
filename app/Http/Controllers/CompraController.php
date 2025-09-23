<?php
// app/Http/Controllers/CompraController.php
namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with('proveedor')->get();
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('Activo', true)->get();
        $productos = Producto::where('Activo', true)->get();
        return view('compras.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Proveedor_Id' => 'required|exists:Proveedores,Id',
            'FechaCompra' => 'required|date',
            'Estado' => 'required|in:Pendiente,Completada,Cancelada',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:Productos,Id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0'
        ]);

        // Calcular total
        $total = 0;
        foreach ($request->productos as $producto) {
            $total += $producto['cantidad'] * $producto['precio'];
        }

        // Crear la compra
        $compra = Compra::create([
            'Proveedor_Id' => $request->Proveedor_Id,
            'FechaCompra' => $request->FechaCompra,
            'Total' => $total,
            'Estado' => $request->Estado,
            'Observaciones' => $request->Observaciones
        ]);

        // Crear los detalles de compra
        foreach ($request->productos as $producto) {
            DetalleCompra::create([
                'Compra_Id' => $compra->Id,
                'Producto_Id' => $producto['id'],
                'Cantidad' => $producto['cantidad'],
                'PrecioUnitario' => $producto['precio'],
                'Subtotal' => $producto['cantidad'] * $producto['precio']
            ]);

            // Actualizar stock del producto (si la compra está completada)
            if ($request->Estado == 'Completada') {
                $prod = Producto::find($producto['id']);
                $prod->Stock += $producto['cantidad'];
                $prod->save();
            }
        }

        return redirect()->route('compras.index')
            ->with('success', 'Compra registrada correctamente');
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'detalles.producto'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function edit($id)
    {
        $compra = Compra::with('detalles')->findOrFail($id);
        $proveedores = Proveedor::where('Activo', true)->get();
        $productos = Producto::where('Activo', true)->get();
        
        return view('compras.edit', compact('compra', 'proveedores', 'productos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Proveedor_Id' => 'required|exists:Proveedores,Id',
            'FechaCompra' => 'required|date',
            'Estado' => 'required|in:Pendiente,Completada,Cancelada',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:Productos,Id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0'
        ]);

        $compra = Compra::findOrFail($id);
        
        // Revertir stock si la compra estaba completada
        if ($compra->Estado == 'Completada') {
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::find($detalle->Producto_Id);
                $producto->Stock -= $detalle->Cantidad;
                $producto->save();
            }
        }

        // Calcular nuevo total
        $total = 0;
        foreach ($request->productos as $producto) {
            $total += $producto['cantidad'] * $producto['precio'];
        }

        // Actualizar la compra
        $compra->update([
            'Proveedor_Id' => $request->Proveedor_Id,
            'FechaCompra' => $request->FechaCompra,
            'Total' => $total,
            'Estado' => $request->Estado,
            'Observaciones' => $request->Observaciones
        ]);

        // Eliminar detalles anteriores
        DetalleCompra::where('Compra_Id', $compra->Id)->delete();

        // Crear nuevos detalles
        foreach ($request->productos as $producto) {
            DetalleCompra::create([
                'Compra_Id' => $compra->Id,
                'Producto_Id' => $producto['id'],
                'Cantidad' => $producto['cantidad'],
                'PrecioUnitario' => $producto['precio'],
                'Subtotal' => $producto['cantidad'] * $producto['precio']
            ]);

            // Actualizar stock del producto (si la compra está completada)
            if ($request->Estado == 'Completada') {
                $prod = Producto::find($producto['id']);
                $prod->Stock += $producto['cantidad'];
                $prod->save();
            }
        }

        return redirect()->route('compras.index')
            ->with('success', 'Compra actualizada correctamente');
    }

    public function destroy($id)
    {
        $compra = Compra::findOrFail($id);
        
        // Revertir stock si la compra estaba completada
        if ($compra->Estado == 'Completada') {
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::find($detalle->Producto_Id);
                $producto->Stock -= $detalle->Cantidad;
                $producto->save();
            }
        }
        
        // Eliminar detalles
        DetalleCompra::where('Compra_Id', $compra->Id)->delete();
        
        // Eliminar compra
        $compra->delete();

        return redirect()->route('compras.index')
            ->with('success', 'Compra eliminada correctamente');
    }
     public function toggle($id)
    {
        $compra = Compra::findOrFail($id);
        $compra->Estado = $compra->Estado == 'Completada' ? 'Pendiente' : 'Completada';
        $compra->save();
        return redirect()->route('compras.index')->with('success', 'Estado de la compra actualizado');
    }

}

   