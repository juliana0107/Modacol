<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProveedorController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($request->filled('RazonSocial')) {
            $query->where('RazonSocial', 'like', '%' . $request->RazonSocial . '%');
        }

        if ($request->filled('Correo')) {
        $query->where('Correo', 'like', '%' . $request->Correo . '%');
        }

        if ($request->filled('Direccion')) {
            $query->where('Direccion', 'like', '%' . $request->Direccion . '%');
        }

        if ($request->filled('Activo')) {
            $query->where('Activo', $request->Activo);
        }

        $proveedores = $query->get();

        return view('proveedores.index', compact('proveedores'))
            ->with('filters', $request->only(['RazonSocial', 'Correo', 'Direccion', 'Activo']));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'RazonSocial' => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
            'Identificacion' => 'required|numeric|unique:Proveedores,Identificacion',
            'Direccion' => 'required|string|max:150',
            'Correo' => 'required|email|unique:Proveedores,Correo',
            'Contacto' => 'required|numeric|digits:10',
        ]);

        Proveedor::create($request->all());
        
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado correctamente');
    }

    public function edit($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'RazonSocial' =>'required|regex:/^[a-zA-Z\s]+$/|max:255',
            'Identificacion' => 'required|numeric|unique:Proveedores,Identificacion,' . $id,
            'Direccion' => 'required|string|max:150',
            'Correo' => 'required|email|unique:Proveedores,Correo,' . $id,
            'Contacto' => 'required|numeric|digits:10'
        ]);

        $proveedor = Proveedor::findOrFail($id);
        $proveedor->update($request->all());
        
        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente');
    }

    public function toggle($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->Activo = !$proveedor->Activo;
        $proveedor->save();
        
        $message = $proveedor->Activo ? 'activado' : 'inactivado';
        return redirect()->route('proveedores.index')
            ->with('success', "Proveedor $message correctamente");
    }

    public function exportAll()
    {
        $proveedores = Proveedor::all();
        return $this->generateExcel($proveedores, 'proveedores_completo.xlsx');
    }

    public function exportFiltered(Request $request)
    {
        $query = Proveedor::query();

        if ($request->filled('RazonSocial')) {
            $query->where('RazonSocial', 'like', '%' . $request->RazonSocial . '%');
        }

           if ($request->filled('Correo')) {
        $query->where('Correo', 'like', '%' . $request->Correo . '%');
        }

        if ($request->filled('Direccion')) {
            $query->where('Direccion', 'like', '%' . $request->Direccion . '%');
        }

        if ($request->filled('Activo')) {
            $query->where('Activo', $request->Activo);
        }

        $proveedores = $query->get();
        return $this->generateExcel($proveedores, 'proveedores_filtrado.xlsx');
    }
    private function generateExcel($proveedores, $fileName)
        {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Título
            $sheet->setCellValue('A1', 'REPORTE DETALLADO DE PROVEEDORES');
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

            // Encabezados
            $headers = ['ID', 'Razón Social', 'Identificación', 'Dirección', 'Correo', 'Contacto', 'Estado'];
            $sheet->fromArray($headers, null, 'A3');
            $sheet->getStyle('A3:G3')->applyFromArray($this->getHeaderStyle());

            // Contenido
            $row = 4;
            foreach ($proveedores as $proveedor) {
                $sheet->setCellValue('A' . $row, $proveedor->Id);
                $sheet->setCellValue('B' . $row, $proveedor->RazonSocial);
                $sheet->setCellValue('C' . $row, $proveedor->Identificacion);
                $sheet->setCellValue('D' . $row, $proveedor->Direccion);
                $sheet->setCellValue('E' . $row, $proveedor->Correo);
                $sheet->setCellValue('F' . $row, $proveedor->Contacto);
                $sheet->setCellValue('G' . $row, $proveedor->Activo ? 'ACTIVO' : 'INACTIVO');
                $row++;
            }

            // Aplicar bordes a todas las celdas
            $sheet->getStyle('A3:G' . $row)->applyFromArray($this->getBorderStyle());

            // Escribir el archivo
            $writer = new Xlsx($spreadsheet);
            $response = new StreamedResponse(function () use ($writer) {
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', "attachment;filename=\"{$fileName}\"");
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        }

// Método para el estilo de Título
private function getTitleStyle()
{
    return [
        'font' => [
            'bold' => true,
            'size' => 16,
            'color' => ['argb' => 'FFFFFF'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => '4F81BD'],
        ],
    ];
}

// Método para el estilo de los encabezados
private function getHeaderStyle()
{
    return [
        'font' => [
            'bold' => true,
            'color' => ['argb' => 'FFFFFF'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => '1F4E79'],
        ],
    ];
}

// Método para los bordes de las celdas
private function getBorderStyle()
{
    return [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
    ];
}
}