<?php
// app/Http/Controllers/CompraController.php
namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use App\Models\Usuario;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CompraController extends Controller
{
    public function index(Request $request)
{
    // Obtener las compras con los detalles de productos
    $compras = Compra::with('proveedor', 'usuario', 'detalles.producto') // Incluir los detalles y productos
        ->when($request->filled('fecha_inicio'), function ($query) use ($request) {
            return $query->whereDate('Fecha_Inicio', '>=', $request->fecha_inicio);
        })
        ->when($request->filled('fecha_fin'), function ($query) use ($request) {
            return $query->whereDate('Fecha_Inicio', '<=', $request->fecha_fin);
        })
        ->when($request->filled('proveedor_id'), function ($query) use ($request) {
            return $query->where('Proveedor_Id', $request->proveedor_id);
        })
        ->when($request->filled('producto_id'), function ($query) use ($request) {
            return $query->whereHas('detalles', function ($q) use ($request) {
                $q->where('Producto_Id', $request->producto_id);
            });
        })
        ->when($request->filled('usuario_id'), function ($query) use ($request) {
            return $query->where('Usuario_Id', $request->usuario_id);
        })
        ->when($request->filled('estado'), function ($query) use ($request) {
            return $query->where('Activo', $request->estado);
        })
        ->get();

    $proveedores = Proveedor::all(); // Obtener todos los proveedores
    $usuarios = Usuario::all(); // Obtener todos los usuarios
    $productos = Producto::all(); // Obtener todos los productos
    $compras = Compra::all();


    return view('compras.index', compact('compras', 'proveedores', 'usuarios', 'productos'));
}


    public function create()
    {
        $proveedores = Proveedor::where('Activo', true)->get();
        $productos = Producto::where('Activo', true)->get();
        return view('compras.create', compact('proveedores', 'productos'));
    }

   public function store(Request $request)
{
    // Validación de los datos de la compra
    $request->validate([
        'Proveedor_Id' => 'required|exists:Proveedores,Id',
        'FechaCompra' => 'required|date',
        'Estado' => 'required|in:Pendiente,Completada,Cancelada',
        'productos' => 'required|array|min:1',
        'productos.*.id' => 'required|exists:Productos,Id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio' => 'required|numeric|min:0'
    ]);

    // Calcular el total de la compra
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

    // Crear los detalles de la compra
    foreach ($request->productos as $producto) {
        DetalleCompra::create([
            'Compra_Id' => $compra->Id,
            'Producto_Id' => $producto['id'],
            'Cantidad' => $producto['cantidad'],
            'PrecioU' => $producto['precio'],
            'Subtotal' => $producto['cantidad'] * $producto['precio']
        ]);

        // Actualizar el stock del producto (si la compra está completada)
        if ($request->Estado == 'Completada') {
            $prod = Producto::find($producto['id']);
            $prod->Stock += $producto['cantidad']; // Aumentar el stock del producto
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

    // Revertir el stock si la compra estaba completada
    if ($compra->Estado == 'Completada') {
        foreach ($compra->detalles as $detalle) {
            $producto = Producto::find($detalle->Producto_Id);
            $producto->Stock -= $detalle->Cantidad; // Decrementar el stock
            $producto->save();
        }
    }

    // Calcular el nuevo total
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

    // Eliminar los detalles anteriores
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

        // Actualizar el stock del producto (si la compra está completada)
        if ($request->Estado == 'Completada') {
            $prod = Producto::find($producto['id']);
            $prod->Stock += $producto['cantidad']; // Aumentar el stock del producto
            $prod->save();
        }
    }

    return redirect()->route('compras.index')
        ->with('success', 'Compra actualizada correctamente');
}
     public function toggle($id)
{
    $compra = Compra::findOrFail($id);
    $compra->Activo = !$compra->Activo;  // Alterna el valor de 'Activo'
    $compra->save();

    return redirect()->route('compras.index')->with('success', 'Estado de la compra actualizado');
}


    public function exportarTodo()
    {
        $compras = Compra::with('proveedor', 'usuario', 'detalles.producto')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Fecha de Inicio');
        $sheet->setCellValue('C1', 'Proveedor');
        $sheet->setCellValue('D1', 'Vendedor');
        $sheet->setCellValue('E1', 'Total');
        $sheet->setCellValue('F1', 'Estado');

        $row = 2;
        foreach ($compras as $compra) {
            $sheet->setCellValue('A' . $row, $compra->Id);
            $sheet->setCellValue('B' . $row, $compra->Fecha_Inicio);
            $sheet->setCellValue('C' . $row, $compra->proveedor->RazonSocial);
            $sheet->setCellValue('D' . $row, $compra->usuario->Nombre);
            $sheet->setCellValue('E' . $row, $compra->Total);
            $sheet->setCellValue('F' . $row, $compra->Activo ? 'Activa' : 'Inactiva');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'compras_completo_' . date('Y-m-d') . '.xlsx';

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0'
            ]
        );
    }

    // Función para exportar compras filtradas
    public function exportarDetallado(Request $request)
    {
        $compras = Compra::with('proveedor', 'usuario', 'detalles.producto')
            ->when($request->filled('fecha_inicio'), function ($query) use ($request) {
                return $query->whereDate('Fecha_Inicio', '>=', $request->fecha_inicio);
            })
            ->when($request->filled('fecha_fin'), function ($query) use ($request) {
                return $query->whereDate('Fecha_Inicio', '<=', $request->fecha_fin);
            })
            ->when($request->filled('proveedor_id'), function ($query) use ($request) {
                return $query->where('Proveedor_Id', $request->proveedor_id);
            })
            ->when($request->filled('usuario_id'), function ($query) use ($request) {
                return $query->where('Usuario_Id', $request->usuario_id);
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                return $query->where('Activo', $request->estado);
            })
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Fecha de Inicio');
        $sheet->setCellValue('C1', 'Proveedor');
        $sheet->setCellValue('D1', 'Vendedor');
        $sheet->setCellValue('E1', 'Producto');
        $sheet->setCellValue('F1', 'Cantidad');
        $sheet->setCellValue('G1', 'Precio');
        $sheet->setCellValue('H1', 'Subtotal');

        $row = 2;
        foreach ($compras as $compra) {
            foreach ($compra->detalles as $detalle) {
                $sheet->setCellValue('A' . $row, $compra->Id);
                $sheet->setCellValue('B' . $row, $compra->Fecha_Inicio);
                $sheet->setCellValue('C' . $row, $compra->proveedor->RazonSocial);
                $sheet->setCellValue('D' . $row, $compra->usuario->Nombre);
                $sheet->setCellValue('E' . $row, $detalle->producto->Nombre);
                $sheet->setCellValue('F' . $row, $detalle->Cantidad);
                $sheet->setCellValue('G' . $row, $detalle->PrecioU);
                $sheet->setCellValue('H' . $row, $detalle->Cantidad * $detalle->PrecioU);
                $row++;
            }
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'compras_detallado_' . date('Y-m-d') . '.xlsx';

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0'
            ]
        );
    }
}

   