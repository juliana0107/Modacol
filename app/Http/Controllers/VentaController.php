<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class VentaController extends Controller
{
    public function index(Request $request)
    {
        $ventas = Venta::with('cliente', 'usuario')
            ->when($request->filled('fecha_inicio'), function ($query) use ($request) {
                return $query->whereDate('Fecha', '>=', $request->fecha_inicio);
            })
            ->when($request->filled('fecha_fin'), function ($query) use ($request) {
                return $query->whereDate('Fecha', '<=', $request->fecha_fin);
            })
            ->when($request->filled('cliente_id'), function ($query) use ($request) {
                return $query->where('Cliente_id', $request->cliente_id);
            })
            ->when($request->filled('usuario_id'), function ($query) use ($request) {
                return $query->where('Usuario_id', $request->usuario_id);
            })
            ->when($request->filled('estado'), function ($query) use ($request) {
                return $query->where('Activo', $request->estado);
            })
            ->get();

        $clientes = Cliente::all();
        $usuarios = Usuario::all();

        return view('ventas.index', compact('ventas', 'clientes', 'usuarios'));
    }

    public function create() {
        $clientes = Cliente::all();
        $usuarios = Usuario::all();
        $productos = Producto::where('Activo', true)->get();
        return view('ventas.create', compact('clientes', 'usuarios', 'productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Fecha' => 'required|date',
            'Cliente_id' => 'required|exists:Clientes,Id',
            'Usuario_id' => 'required|exists:Usuarios,Id',
            'ValorTotal' => 'required|numeric|min:0'
        ]);

        Venta::create($request->all());
        return redirect()->route('ventas.index')->with('success', 'Venta creada correctamente');
    }

   public function edit($id)
    {
        $venta = Venta::findOrFail($id);
        $clientes = Cliente::where('Activo', true)->get();
        $usuarios = Usuario::where('Activo', true)->get();
        
        return view('ventas.edit', compact('venta', 'clientes', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Fecha' => 'required|date',
            'Cliente_id' => 'required|exists:Clientes,Id',
            'Usuario_id' => 'required|exists:Usuarios,Id',
            'ValorTotal' => 'required|numeric|min:0'
        ]);

        $venta = Venta::findOrFail($id);
        $venta->update($request->all());
        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente');
    }
    public function toggle($id){
                $venta = Venta::findOrFail($id);
                $venta->Activo = !$venta->Activo;
                $venta->save();
                return redirect()->route('ventas.index')->with('success', 'Estado de la venta actualizado');
    }

        public function exportarTodo()
        {
        $ventas = Venta::with('cliente', 'usuario', 'detalles.producto')->get();

            // Crear una nueva hoja de cálculo
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Configurar el título
            $sheet->setCellValue('A1', 'REPORTE DE VENTAS');
            $sheet->mergeCells('A1:F1');
            $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

            // Configurar los encabezados
            $headers = ['ID', 'Fecha', 'Cliente', 'Vendedor', 'Valor Total', 'Estado'];
            $sheet->fromArray($headers, null, 'A3');
            $sheet->getStyle('A3:F3')->applyFromArray($this->getHeaderStyle());

            // Escribir los datos de las ventas
            $row = 4;
            foreach ($ventas as $venta) {
                $sheet->setCellValue('A' . $row, $venta->Id);
                $sheet->setCellValue('B' . $row, $venta->Fecha);
                $sheet->setCellValue('C' . $row, $venta->cliente->Nombre);
                $sheet->setCellValue('D' . $row, $venta->usuario->Nombre);
                $sheet->setCellValue('E' . $row, $venta->ValorTotal);
                $sheet->setCellValue('F' . $row, $venta->Activo ? 'Activa' : 'Inactiva');
                $row++;
            }

            // Aplicar bordes a las celdas
            $sheet->getStyle('A3:F' . $row)->applyFromArray($this->getBorderStyle());

            // Descargar el archivo Excel
            $writer = new Xlsx($spreadsheet);
            $filename = 'ventas_completo_' . date('Y-m-d') . '.xlsx';
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

        // Métodos de estilo (similar a los de Proveedores)
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

    // Función para exportar las ventas filtradas a Excel
   public function exportarDetallado(Request $request)
{
    $ventas = Venta::with('cliente', 'usuario', 'detalles.producto')
        ->when($request->filled('fecha_inicio'), function ($query) use ($request) {
            return $query->whereDate('Fecha', '>=', $request->fecha_inicio);
        })
        ->when($request->filled('fecha_fin'), function ($query) use ($request) {
            return $query->whereDate('Fecha', '<=', $request->fecha_fin);
        })
        ->when($request->filled('cliente_id'), function ($query) use ($request) {
            return $query->where('Cliente_id', $request->cliente_id);
        })
        ->when($request->filled('usuario_id'), function ($query) use ($request) {
            return $query->where('Usuario_id', $request->usuario_id);
        })
        ->when($request->filled('estado'), function ($query) use ($request) {
            return $query->where('Activo', $request->estado);
        })
        ->get();

    // Crear una nueva hoja de cálculo
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar el título
    $sheet->setCellValue('A1', 'REPORTE DETALLADO DE VENTAS');
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

    // Configurar los encabezados
    $headers = ['ID', 'Fecha', 'Cliente', 'Vendedor', 'Producto', 'Cantidad', 'IVA', 'Subtotal'];
    $sheet->fromArray($headers, null, 'A3');
    $sheet->getStyle('A3:H3')->applyFromArray($this->getHeaderStyle());

    // Escribir los detalles de las ventas
    $row = 4;
    foreach ($ventas as $venta) {
        foreach ($venta->detalles as $detalle) {
            $sheet->setCellValue('A' . $row, $venta->Id);
            $sheet->setCellValue('B' . $row, $venta->Fecha);
            $sheet->setCellValue('C' . $row, $venta->cliente->Nombre);
            $sheet->setCellValue('D' . $row, $venta->usuario->Nombre);
            $sheet->setCellValue('E' . $row, $detalle->producto->Nombre);
            $sheet->setCellValue('F' . $row, $detalle->Cantidad);
            $sheet->setCellValue('G' . $row, $detalle->Iva);
            $sheet->setCellValue('H' . $row, $detalle->SubTotal);
            $row++;
        }
    }

    // Aplicar bordes a las celdas
    $sheet->getStyle('A3:H' . $row)->applyFromArray($this->getBorderStyle());

    // Descargar el archivo Excel
    $writer = new Xlsx($spreadsheet);
    $filename = 'ventas_detallado_' . date('Y-m-d') . '.xlsx';
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


    // Crear gráfico para el reporte
    private function crearGrafico($sheet)
    {
        // Aquí se puede agregar un gráfico en la hoja de Excel usando PhpSpreadsheet
        // En este caso, vamos a crear un gráfico de barras de ventas totales por cliente

        $labels = ['Cliente 1', 'Cliente 2', 'Cliente 3'];
        $data = [1500, 2000, 2500];

        $sheet->fromArray([$labels], null, 'J1');
        $sheet->fromArray([$data], null, 'J2');

        $dataSeriesLabels = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('String', 'Sheet1!$J$1', null, 1)
        ];
        $dataSeriesValues = [
            new \PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues('Number', 'Sheet1!$J$2', null, 1)
        ];
        $dataSeries = new \PhpOffice\PhpSpreadsheet\Chart\DataSeries(
            \PhpOffice\PhpSpreadsheet\Chart\DataSeries::TYPE_BARCHART,
            null,
            range(0, count($dataSeriesLabels) - 1),
            $dataSeriesLabels,
            $dataSeriesValues
        );

        $chart = new \PhpOffice\PhpSpreadsheet\Chart\Chart(
            'chart1',
            new Title('Ventas Totales por Cliente'),
            new Legend(Legend::POSITION_RIGHT, null, false),
            new PlotArea(null, [$dataSeries])
        );

        // Add the chart to the worksheet
        $sheet->addChart($chart);
    }
}
