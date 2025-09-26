<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
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


        return view('operativo.cruds.productos.index', compact('productos'));
    }


    public function create() {
        return view('operativo.cruds.productos.create');
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
        return redirect()->route('operativo.cruds.productos.index')->with('success', 'Producto creado correctamente');
    }

    public function edit($id){
        $producto = Producto::findOrFail($id);
        return view('operativo.cruds.productos.edit', compact('producto'));
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
        return redirect()->route('operativo.cruds.productos.index')->with('success', 'Producto actualizado correctamente');
    }

   public function toggle($id) {
        $producto = Producto::findOrFail($id);
        $producto->Activo = !$producto->Activo;
        $producto->save();
        return redirect()->route('operativo.cruds.productos.index')->with('success', 'Estado del producto actualizado');    } 


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

    public function exportarExcel(Request $request)
    {
        $tipo = $request->get('tipo', 'completo');
        $productos = Producto::query();

        // Aplicar filtros si existen
        if ($request->filled('Nombre')) {
            $productos->where('Nombre', 'like', '%' . $request->Nombre . '%');
        }

        if ($request->has('Activo') && $request->Activo !== '') {
            $productos->where('Activo', $request->Activo);
        }

        $productos = $productos->get();

        $filename = $this->generateFilename($tipo);
        
        return $this->generateExcel($productos, $tipo, $filename);
    }

    /**
     * Generar archivo Excel con estilos profesionales
     */
    private function generateExcel($productos, $tipo, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('Reporte de Productos')
            ->setSubject('Productos exportados desde el sistema');

        if ($tipo === 'detallado') {
            $this->generateDetailedSheet($sheet, $productos);
        } else {
            $this->generateSummarySheet($sheet, $productos);
        }

        // Autoajustar columnas
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Hoja detallada con formato elegante
     */
    private function generateDetailedSheet($sheet, $productos)
    {
        // Título del reporte
        $sheet->setCellValue('A1', 'REPORTE DETALLADO DE PRODUCTOS');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

        // Subtítulo
        $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());

        // Encabezados de columnas
        $headers = ['ID', 'Nombre', 'Descripción', 'Precio Unitario', 'Cantidad', 'Estado', 'Fecha de Registro'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:G4')->applyFromArray($this->getHeaderStyle());

        // Datos de productos
        $row = 5;
        foreach ($productos as $producto) {
            $sheet->setCellValue('A' . $row, $producto->Id);
            $sheet->setCellValue('B' . $row, $producto->Nombre);
            $sheet->setCellValue('C' . $row, $producto->Descripcion);
            $sheet->setCellValue('D' . $row, $producto->PrecioU);
            $sheet->setCellValue('E' . $row, $producto->Cantidad);
            $sheet->setCellValue('F' . $row, $producto->Activo ? 'ACTIVO' : 'INACTIVO');
            $sheet->setCellValue('G' . $row, $producto->Fecha ? \Carbon\Carbon::parse($producto->Fecha)->format('d/m/Y') : 'N/A');
            
            // Estilo alternado para filas
            $style = $row % 2 == 0 ? $this->getEvenRowStyle() : $this->getOddRowStyle();
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($style);
            
            $row++;
        }

        // Formato numérico para precios
        $sheet->getStyle('D5:D' . ($row-1))->getNumberFormat()->setFormatCode('#,##0.00');

        // Totales
        $this->addTotals($sheet, $row, $productos);
    }

    /**
     * Hoja resumida
     */
    private function generateSummarySheet($sheet, $productos)
    {
        $sheet->setCellValue('A1', 'REPORTE RESUMIDO DE PRODUCTOS');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

        $headers = ['ID', 'Nombre', 'Precio Unitario', 'Cantidad', 'Estado'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:E3')->applyFromArray($this->getHeaderStyle());

        $row = 4;
        foreach ($productos as $producto) {
            $sheet->setCellValue('A' . $row, $producto->Id);
            $sheet->setCellValue('B' . $row, $producto->Nombre);
            $sheet->setCellValue('C' . $row, $producto->PrecioU);
            $sheet->setCellValue('D' . $row, $producto->Cantidad);
            $sheet->setCellValue('E' . $row, $producto->Activo ? 'ACTIVO' : 'INACTIVO');
            $row++;
        }
    }

    /**
     * Agregar fila de totales
     */
    private function addTotals($sheet, $row, $productos)
    {
        $totalRow = $row + 1;
        
        $sheet->setCellValue('C' . $totalRow, 'TOTALES:');
        $sheet->setCellValue('D' . $totalRow, '=SUM(D5:D' . ($row-1) . ')');
        $sheet->setCellValue('E' . $totalRow, '=SUM(E5:E' . ($row-1) . ')');
        $sheet->setCellValue('F' . $totalRow, '=COUNTIF(F5:F' . ($row-1) . ',"ACTIVO") & " Activos"');

        $sheet->getStyle('C' . $totalRow . ':F' . $totalRow)->applyFromArray($this->getTotalStyle());
        $sheet->getStyle('D' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
    }

    /**
     * Estilos para Excel
     */
    private function getTitleStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '2C3E50']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
    }

    private function getHeaderStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '34495E']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
    }

    private function getEvenRowStyle()
    {
        return [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'F8F9FA']
            ],
        ];
    }

    private function getOddRowStyle()
    {
        return [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFFFF']
            ],
        ];
    }

    private function getTotalStyle()
    {
        return [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'D4AF37']
            ],
        ];
    }

    private function getSubtitleStyle()
    {
        return [
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '7F8C8D']
            ],
        ];
    }

    private function generateFilename($tipo)
    {
        $timestamp = now()->format('Y-m-d_His');
        return "productos_{$tipo}_{$timestamp}.xlsx";
    }
}