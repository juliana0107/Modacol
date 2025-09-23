<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductoController extends Controller
{
    public function index(Request $request) 
    {
        $productos = Producto::query();

        if ($request->filled('Nombre')) {
            $productos->where('Nombre', 'like', '%' . $request->Nombre . '%');
        }

        if ($request->has('Activo')) {
            $productos->where('Activo', $request->Activo);
        }

        if ($request->filled('PrecioMin') && $request->filled('PrecioMax')) {
            $productos->whereBetween('PrecioU', [$request->PrecioMin, $request->PrecioMax]);
        }

        $productos = $productos->get();

        if ($request->has('download')) {
            return $this->downloadReport($productos);
        }


        return view('productos.index', compact('productos'));
    }


    public function create() {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required|string|max:100|unique:Productos,Nombre',  // Único en la tabla 'Productos'
            'Descripcion' => 'required|string|max:255', // Asegurando que la descripción no sea excesivamente larga
            'PrecioU' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/', // Asegurando que tenga 2 decimales
            'Cantidad' => 'required|integer|min:1', // Asegurando que la cantidad sea mayor a 0
        ], [
            // Mensajes personalizados de error
            'Nombre.required' => 'El nombre del producto es obligatorio.',
            'Nombre.unique' => 'Ya existe un producto con este nombre.',
            'Descripcion.required' => 'La descripción es obligatoria.',
            'PrecioU.required' => 'El precio es obligatorio.',
            'PrecioU.numeric' => 'El precio debe ser un número.',
            'PrecioU.min' => 'El precio debe ser mayor que 0.',
            'PrecioU.regex' => 'El precio debe tener hasta 2 decimales.',
            'Cantidad.required' => 'La cantidad es obligatoria.',
            'Cantidad.integer' => 'La cantidad debe ser un número entero.',
            'Cantidad.min' => 'La cantidad debe ser mayor a 0.',
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
            'Nombre' => 'required|string|max:100|unique:Productos,Nombre,' . $id,  // Excluye el nombre del producto actual
            'Descripcion' => 'required|string|max:255',
            'PrecioU' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'Cantidad' => 'required|integer|min:1',
        ], [
            'Nombre.required' => 'El nombre del producto es obligatorio.',
            'Nombre.unique' => 'Ya existe un producto con este nombre.',
            'Descripcion.required' => 'La descripción es obligatoria.',
            'PrecioU.required' => 'El precio es obligatorio.',
            'PrecioU.numeric' => 'El precio debe ser un número.',
            'PrecioU.min' => 'El precio debe ser mayor que 0.',
            'PrecioU.regex' => 'El precio debe tener hasta 2 decimales.',
            'Cantidad.required' => 'La cantidad es obligatoria.',
            'Cantidad.integer' => 'La cantidad debe ser un número entero.',
            'Cantidad.min' => 'La cantidad debe ser mayor a 0.',
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


    protected function downloadReport($productos)
    {
        $filename = 'productos_reporte.csv';
        $handle = fopen('php://output', 'w');

        // Agregar las cabeceras del CSV
        fputcsv($handle, ['ID', 'Nombre', 'Descripción', 'Precio Unitario', 'Cantidad', 'Estado']);

        // Agregar los datos de los productos
        foreach ($productos as $producto) {
            fputcsv($handle, [
                $producto->Id,
                $producto->Nombre,
                $producto->Descripcion,
                $producto->PrecioU,
                $producto->Cantidad,
                $producto->Activo ? 'Activo' : 'Inactivo'
            ]);
        }

        // Enviar los encabezados para la descarga del archivo
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        fpassthru($handle);
    }

    public function exportar() {
        $productos = Producto::all();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado de las columnas
        $sheet->setCellValue('A1', 'Id');
        $sheet->setCellValue('B1', 'Nombre');
        $sheet->setCellValue('C1', 'Descripcion');
        $sheet->setCellValue('D1', 'PrecioU');
        $sheet->setCellValue('E1', 'Cantidad');
        $sheet->setCellValue('F1', 'Activo');

        // Llenar las filas con los datos de productos
        $row = 2;
        foreach ($productos as $producto) {
            $sheet->setCellValue('A' . $row, $producto->Id);
            $sheet->setCellValue('B' . $row, $producto->Nombre);
            $sheet->setCellValue('C' . $row, $producto->Descripcion);
            $sheet->setCellValue('D' . $row, $producto->PrecioU);
            $sheet->setCellValue('E' . $row, $producto->Cantidad);
            $sheet->setCellValue('F' . $row, $producto->Activo ? 'Activo' : 'Inactivo');
            $row++;
        }

        // Crear el archivo Excel
        $writer = new Xlsx($spreadsheet);

        // Crear la respuesta de descarga
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        // Configurar los headers para descargar el archivo
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="productos.xlsx"');

        return $response;
    }

}