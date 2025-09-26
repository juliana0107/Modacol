<?php

namespace App\Http\Controllers;

use App\Models\FlujoCaja;
use App\Models\DetalleFlujoCaja;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;


class FlujoCajaController extends Controller
{
    // Mostrar todos los flujos de caja
    public function index(Request $request)
    {
        $flujoCajas = FlujoCaja::all();
        return view('flujoCaja.index', compact('flujoCajas'));
    }

    // Crear un nuevo flujo de caja
    public function create()
    {
        
        return view('flujoCaja.create');

    }

    // Almacenar un nuevo flujo de caja
    public function store(Request $request)
    {
        $request->validate([
            'Fecha' => 'required|date',
            'Saldo_Neto' => 'required|numeric',
            'Saldo_Final' => 'required|numeric',
            'Activo' => 'required|boolean',
        ]);

        FlujoCaja::create($request->all());
        return redirect()->route('flujoCaja.index')->with('success', 'Flujo de Caja creado correctamente');
    }

    // Editar un flujo de caja existente
    public function edit($id)
    {
        $flujoCaja = FlujoCaja::findOrFail($id);
        return view('flujoCaja.edit', compact('flujoCaja'));
    }

    // Actualizar un flujo de caja
    public function update(Request $request, $id)
    {
        $request->validate([
            'Fecha' => 'required|date',
            'Saldo_Neto' => 'required|numeric',
            'Saldo_Final' => 'required|numeric',
            'Activo' => 'required|boolean',
        ]);

        $flujoCaja = FlujoCaja::findOrFail($id);
        $flujoCaja->update($request->all());
        return redirect()->route('flujoCaja.index')->with('success', 'Flujo de Caja actualizado correctamente');
    }

    // Mostrar detalles de flujo de caja
    public function show($id)
{
    $flujo = FlujoCaja::findOrFail($id); // Busca el flujo de caja por ID
    return view('flujoCaja.show', compact('flujo')); // Pasa la variable flujo a la vista
}

    // Cambiar el estado de activo a inactivo
    public function toggle($id)
    {
        $flujoCaja = FlujoCaja::findOrFail($id);
        $flujoCaja->Activo = !$flujoCaja->Activo;
        $flujoCaja->save();
        return redirect()->route('flujoCaja.index');
    }
    public function downloadReport(Request $request)
{
    $query = FlujoCaja::query();

    if ($request->filled('fecha')) {
        $query->whereDate('Fecha', $request->fecha);
    }

    if ($request->filled('estado')) {
        $query->where('Activo', $request->estado);
    }

    $flujos = $query->get();

    // Nombre del archivo CSV
    $filename = 'flujos_de_caja_' . now()->format('Y-m-d_H-i-s') . '.csv';
    
    // Abrir el archivo para escribir
    $handle = fopen('php://output', 'w');
    fputcsv($handle, ['ID', 'Fecha', 'Saldo Neto', 'Saldo Final', 'Estado']); // Encabezado

    foreach ($flujos as $flujo) {
        fputcsv($handle, [
            $flujo->Id,
            $flujo->Fecha,
            $flujo->Saldo_Neto,
            $flujo->Saldo_Final,
            $flujo->Activo ? 'Activo' : 'Inactivo'
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
public function exportarExcel(Request $request)
{
    try {
        $flujos = FlujoCaja::query();

        if ($request->filled('fecha')) {
            $flujos->whereDate('Fecha', $request->fecha);
        }

        if ($request->filled('estado')) {
            $flujos->where('Activo', $request->estado);
        }

        $flujos = $flujos->get();

        // Si no hay flujos, redirige con un mensaje
        if ($flujos->isEmpty()) {
            return redirect()->back()->with('warning', 'No hay flujos de caja para exportar con los filtros aplicados.');
        }

        $filename = 'flujos_de_caja_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return $this->generateFlujoCajaExcel($flujos, $filename);
    } catch (\Exception $e) {
        \Log::error('Error exporting Excel: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al exportar el archivo: ' . $e->getMessage());
    }
}

private function generateFlujoCajaExcel($flujos, $filename)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar propiedades del documento
    $spreadsheet->getProperties()
        ->setCreator(config('app.name'))
        ->setTitle('Reporte de Flujos de Caja')
        ->setSubject('Flujos de caja exportados desde el sistema');

    // Encabezados de columnas
    $sheet->setCellValue('A1', 'REPORTE DE FLUJOS DE CAJA');
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

    $headers = ['ID', 'Fecha', 'Saldo Neto', 'Saldo Final', 'Estado'];
    $sheet->fromArray($headers, null, 'A3');
    $sheet->getStyle('A3:E3')->applyFromArray($this->getHeaderStyle());

    // Llenar los datos de los flujos de caja
    $row = 4;
    foreach ($flujos as $flujo) {
        $sheet->setCellValue('A' . $row, $flujo->Id);
        $sheet->setCellValue('B' . $row, $flujo->Fecha);
        $sheet->setCellValue('C' . $row, $flujo->Saldo_Neto);
        $sheet->setCellValue('D' . $row, $flujo->Saldo_Final);
        $sheet->setCellValue('E' . $row, $flujo->Activo ? 'Activo' : 'Inactivo');
        
        $row++;
    }

    // Autoajustar las columnas
    foreach (range('A', $sheet->getHighestColumn()) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    // Escribir el archivo
    $writer = new Xlsx($spreadsheet);
    
    return new StreamedResponse(function () use ($writer) {
        $writer->save('php://output');
    }, 200, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        'Cache-Control' => 'max-age=0',
    ]);
}

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
            'vertical' => Alignment::VERTICAL_CENTER,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
            ],
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



}
