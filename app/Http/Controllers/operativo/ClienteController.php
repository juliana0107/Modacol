<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClienteController extends Controller


{
    public function index(Request $request)
    {
        // Consulta base de clientes
        $query = Cliente::query();

        // Aplicar filtros
        if ($request->filled('nombre')) {
            $query->where('Nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('correo')) {
            $query->where('Correo', 'like', '%' . $request->correo . '%');
        }

        if ($request->filled('estado')) {
            $query->where('Activo', $request->estado);
        }

        $clientes = $query->get();

        return view('operativo.cruds.clientes.index', compact('clientes'));
    }


    public function create() {
        return view('operativo.cruds.clientes.create');
    }

    public function store(Request $request) {
        Cliente::create($request->all());
        return redirect()->route('operativo.cruds.clientes.index')->with('success', 'Cliente creado correctamente');
    }

        public function edit($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('operativo.cruds.clientes.edit', compact('cliente'));
    }

        public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());
        return redirect()->route('operativo.cruds.clientes.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function toggle($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->Activo = !$cliente->Activo;
        $cliente->save();
        return redirect()->route('operativo.cruds.clientes.index');
    }

// Método para exportar clientes en formato CSV
    public function downloadReport(Request $request)
    {
        $query = Cliente::query();

        if ($request->filled('nombre')) {
            $query->where('Nombre', 'like', '%' . $request->nombre . '%');
        }

        if ($request->filled('correo')) {
            $query->where('Correo', 'like', '%' . $request->correo . '%');
        }

        if ($request->filled('estado')) {
            $query->where('Activo', $request->estado);
        }

        $clientes = $query->get();

        // Generar el archivo CSV
        $filename = 'clientes_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['ID', 'Empresa', 'Nombre', 'Identificación', 'Contacto', 'Correo', 'Estado']); // Encabezado

        foreach ($clientes as $cliente) {
            fputcsv($handle, [
                $cliente->Id,
                $cliente->Empresa,
                $cliente->Nombre,
                $cliente->Identificacion,
                $cliente->Contacto,
                $cliente->Correo,
                $cliente->Activo ? 'Activo' : 'Inactivo'
            ]);
        }

        fclose($handle);

        return response()->stream(function() use ($handle) {
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    // Método para exportar clientes en formato Excel
    public function exportarExcel(Request $request)
    {
        try {
            $tipo = $request->get('tipo', 'completo');
            $clientes = Cliente::query();

            // Aplicar filtros si existen en la request
            if ($request->filled('nombre')) {
                $clientes->where('Nombre', 'like', '%' . $request->nombre . '%');
            }

            if ($request->filled('correo')) {
                $clientes->where('Correo', 'like', '%' . $request->correo . '%');
            }

            if ($request->has('estado') && $request->estado !== '') {
                $clientes->where('Activo', $request->estado);
            }

            $clientes = $clientes->get();

            // Si no hay clientes, retornar mensaje
            if ($clientes->isEmpty()) {
                return redirect()->back()->with('warning', 'No hay clientes para exportar con los filtros aplicados.');
            }

            $filename = $this->generateClientFilename($tipo);
            
            return $this->generateClientExcel($clientes, $tipo, $filename);

        } catch (\Exception $e) {
            \Log::error('Error exporting Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al exportar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Generar archivo Excel para clientes
     */
    private function generateClientExcel($clientes, $tipo, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Configurar propiedades del documento
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('Reporte de Clientes')
            ->setSubject('Clientes exportados desde el sistema');

        // Generar contenido según el tipo
        if ($tipo === 'detallado') {
            $this->generateDetailedClientSheet($sheet, $clientes);
        } else {
            $this->generateSummaryClientSheet($sheet, $clientes);
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
     * Hoja detallada para clientes
     */
    private function generateDetailedClientSheet($sheet, $clientes)
    {
        // Título del reporte
        $sheet->setCellValue('A1', 'REPORTE DETALLADO DE CLIENTES');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

        // Subtítulo
        $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i:s'));
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());

        // Encabezados de columnas
        $headers = ['ID', 'Nombre', 'Correo', 'Teléfono', 'Estado', 'Fecha de Registro'];
        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:F4')->applyFromArray($this->getHeaderStyle());

        // Datos de clientes
        $row = 5;
        foreach ($clientes as $cliente) {
            $sheet->setCellValue('A' . $row, $cliente->Id);
            $sheet->setCellValue('B' . $row, $cliente->Nombre);
            $sheet->setCellValue('C' . $row, $cliente->Correo);
            $sheet->setCellValue('D' . $row, $cliente->Telefono ?? 'N/A');
            $sheet->setCellValue('E' . $row, $cliente->Activo ? 'ACTIVO' : 'INACTIVO');
            $sheet->setCellValue('F' . $row, $cliente->created_at ? $cliente->created_at->format('d/m/Y') : 'N/A');
            
            // Estilo alternado para filas
            $style = $row % 2 == 0 ? $this->getEvenRowStyle() : $this->getOddRowStyle();
            $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($style);
            
            $row++;
        }

        // Totales
        $this->addClientTotals($sheet, $row, $clientes);
    }

    /**
     * Hoja resumida para clientes
     */
    private function generateSummaryClientSheet($sheet, $clientes)
    {
        $sheet->setCellValue('A1', 'REPORTE RESUMIDO DE CLIENTES');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

        $headers = ['ID', 'Nombre', 'Correo', 'Estado'];
        $sheet->fromArray($headers, null, 'A3');
        $sheet->getStyle('A3:D3')->applyFromArray($this->getHeaderStyle());

        $row = 4;
        foreach ($clientes as $cliente) {
            $sheet->setCellValue('A' . $row, $cliente->Id);
            $sheet->setCellValue('B' . $row, $cliente->Nombre);
            $sheet->setCellValue('C' . $row, $cliente->Correo);
            $sheet->setCellValue('D' . $row, $cliente->Activo ? 'ACTIVO' : 'INACTIVO');
            
            $row++;
        }
    }

    /**
     * Agregar fila de totales para clientes
     */
    private function addClientTotals($sheet, $row, $clientes)
    {
        $totalRow = $row + 1;
        
        $sheet->setCellValue('B' . $totalRow, 'TOTALES:');
        $sheet->setCellValue('E' . $totalRow, '=COUNTIF(E5:E' . ($row-1) . ',"ACTIVO") & " Activos"');
        $sheet->setCellValue('F' . $totalRow, '=COUNTIF(E5:E' . ($row-1) . ',"INACTIVO") & " Inactivos"');

        $sheet->getStyle('B' . $totalRow . ':F' . $totalRow)->applyFromArray($this->getTotalStyle());
    }

    /**
     * Generar nombre de archivo para clientes
     */
    private function generateClientFilename($tipo)
    {
        $timestamp = now()->format('Y-m-d_His');
        return "clientes_{$tipo}_{$timestamp}.xlsx";
    }

    /**
     * ===== MÉTODOS DE ESTILO - IMPLEMENTACIÓN COMPLETA =====
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
                'color' => ['rgb' => '2C3E50'] // Azul oscuro elegante
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
    }

    private function getSubtitleStyle()
    {
        return [
            'font' => [
                'italic' => true,
                'size' => 10,
                'color' => ['rgb' => '7F8C8D'] // Gris
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'ECF0F1'] // Gris claro de fondo
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
                'color' => ['rgb' => '34495E'] // Azul grisáceo
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '2C3E50']
                ],
            ],
        ];
    }

    private function getEvenRowStyle()
    {
        return [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'F8F9FA'] // Gris muy claro
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ],
            ],
        ];
    }

    private function getOddRowStyle()
    {
        return [
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => 'FFFFFF'] // Blanco
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD']
                ],
            ],
        ];
    }

    private function getTotalStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '27AE60'] // Verde éxito
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '219653']
                ],
            ],
        ];
    }
}