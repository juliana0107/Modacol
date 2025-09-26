<?php

namespace App\Http\Controllers;

use App\Models\DetalleCompra;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DetalleCompraController extends Controller
{
    // Mostrar todos los detalles de compra
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

    return view('compras.index', compact('compras', 'proveedores', 'usuarios', 'productos'));
}


    // Mostrar el formulario para crear un nuevo detalle de compra
    public function create($compraId)
    {
        $compra = Compra::findOrFail($compraId);
        $productos = Producto::where('Activo', true)->get(); // Productos disponibles

        return view('detalle_compras.create', compact('compra', 'productos'));
    }


    public function exportarTodo()
    {
        $detalles = DetalleCompra::with('compra', 'producto')->get();

        // Crear hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Compra ID');
        $sheet->setCellValue('C1', 'Producto');
        $sheet->setCellValue('D1', 'Cantidad');
        $sheet->setCellValue('E1', 'Precio Unitario');
        $sheet->setCellValue('F1', 'Subtotal');

        $row = 2;
        foreach ($detalles as $detalle) {
            $sheet->setCellValue('A' . $row, $detalle->Id);
            $sheet->setCellValue('B' . $row, $detalle->Compra_Id);
            $sheet->setCellValue('C' . $row, $detalle->producto->Nombre);
            $sheet->setCellValue('D' . $row, $detalle->Cantidad);
            $sheet->setCellValue('E' . $row, $detalle->PrecioU);
            $sheet->setCellValue('F' . $row, $detalle->Cantidad * $detalle->PrecioU); // Subtotal
            $row++;
        }

        // Crear y descargar el archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'detalle_compras_' . date('Y-m-d') . '.xlsx';

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

    // Función para exportar los detalles de compra filtrados
    public function exportarDetallado(Request $request)
    {
        $detalles = DetalleCompra::with('compra', 'producto')
            ->when($request->filled('compra_id'), function ($query) use ($request) {
                return $query->where('Compra_Id', $request->compra_id);
            })
            ->get();

        // Crear hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Establecer encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Compra ID');
        $sheet->setCellValue('C1', 'Producto');
        $sheet->setCellValue('D1', 'Cantidad');
        $sheet->setCellValue('E1', 'Precio Unitario');
        $sheet->setCellValue('F1', 'Subtotal');

        $row = 2;
        foreach ($detalles as $detalle) {
            $sheet->setCellValue('A' . $row, $detalle->Id);
            $sheet->setCellValue('B' . $row, $detalle->Compra_Id);
            $sheet->setCellValue('C' . $row, $detalle->producto->Nombre);
            $sheet->setCellValue('D' . $row, $detalle->Cantidad);
            $sheet->setCellValue('E' . $row, $detalle->PrecioU);
            $sheet->setCellValue('F' . $row, $detalle->Cantidad * $detalle->PrecioU); // Subtotal
            $row++;
        }

        // Crear y descargar el archivo
        $writer = new Xlsx($spreadsheet);
        $filename = 'detalle_compras_filtrado_' . date('Y-m-d') . '.xlsx';

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

    // Mostrar el formulario para editar el detalle de compra
    public function edit($id)
    {
        $detalle = DetalleCompra::findOrFail($id);
        $productos = Producto::all(); // Todos los productos disponibles

        return view('detalle_compras.edit', compact('detalle', 'productos'));
    }

    // Actualizar un detalle de compra
    public function update(Request $request, $id)
    {
        $request->validate([
            'Compra_Id' => 'required|exists:compras,Id',
            'Producto_Id' => 'required|exists:productos,Id',
            'PrecioU' => 'required|numeric',
            'Cantidad' => 'required|integer'
        ]);

        $detalle = DetalleCompra::findOrFail($id);
        $detalle->update($request->all());

        return redirect()->route('detalle_compras.index')->with('success', 'Detalle de compra actualizado correctamente');
    }

   public function toggle($id)
    {
        $detalle = DetalleCompra::findOrFail($id);
        $detalle->Activo = !$detalle->Activo;
        $detalle->save();
        return redirect()->route('detalle_compras.index');
    }

}







