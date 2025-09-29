<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
       $roles = Role::all();

    // Comenzamos la consulta de usuarios
    $query = Usuario::with('rol');

    // Aplicamos los filtros si están presentes
    if ($request->filled('nombre')) {
        $query->where('Nombre', 'like', '%' . $request->nombre . '%');
    }

    if ($request->filled('correo')) {
        $query->where('Correo', 'like', '%' . $request->correo . '%');
    }

    if ($request->filled('activo')) {
        $query->where('Activo', $request->activo);
    }

    if ($request->filled('rol')) {
        $query->where('Id_Rol', $request->rol);
    }

    // Ejecutamos la consulta
    $usuarios = $query->get();

    // Retornamos la vista con los datos
    return view('usuarios.index', compact('usuarios', 'roles'));
}

    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
{
    // Validación de campos
    $request->validate([
        'Nombre' => 'required|regex:/^[a-zA-Z\s]+$/|max:255', // Solo letras
        'Correo' => 'required|email|unique:Usuarios,Correo', // Verifica que el correo sea único
        'Contraseña' => 'required|string|min:8', // Confirmación de la contraseña (repetir contraseña en el formulario)
        'Id_Rol' => 'required|exists:Roles,Id', // Verifica que el rol exista en la base de datos
    ]);

    // Crear el nuevo usuario
    $usuario = Usuario::create([
        'Nombre' => $request->Nombre,
        'Correo' => $request->Correo,
        'Contraseña' => bcrypt($request->Contraseña), // Encriptamos la contraseña
        'Id_Rol' => $request->Id_Rol,
        'Activo' => $request->Activo ?? 1, // Asumimos que si no se marca activo, se pone por defecto
    ]);

    return redirect()->route('usuarios.index')->with('success', 'Usuario creado con éxito');
}

    public function edit(Usuario $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario','roles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
        'Nombre' => 'required|regex:/^[a-zA-Z\s]+$/|max:255',
        'Correo' => 'required|email|unique:Usuarios,Correo,' . $usuario->Id, // Asegura que el correo sea único, excepto el actual
        'Id_Rol' => 'required|exists:Roles,Id', // Verifica que el rol exista
        'Contraseña' => 'nullable|string|min:8', // La contraseña puede no ser modificada, si se pasa debe ser confirmada
    ]);

    // Actualizamos los datos del usuario
    $data = $request->only(['Nombre', 'Correo', 'Id_Rol']);
    
    if ($request->filled('Contraseña')) {
        $data['Contraseña'] = bcrypt($request->Contraseña); // Encriptamos la nueva contraseña
    }

    $usuario->update($data); // Actualiza el usuario

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado con éxito');
}
    public function toggle($id) 
{
    $usuario = Usuario::findOrFail($id);
    $usuario->Activo = !$usuario->Activo;
    $usuario->save();
    
    $message = $usuario->Activo ? 'activado' : 'inactivado';
    return redirect()->route('usuarios.index')->with('success', "Usuario $message correctamente");
}

public function downloadReport(Request $request)
{
    // Filtros, si existen, por ejemplo, nombre, correo, etc.
    $query = Usuario::with('rol');

    if ($request->filled('nombre')) {
        $query->where('Nombre', 'like', '%' . $request->nombre . '%');
    }

    if ($request->filled('correo')) {
        $query->where('Correo', 'like', '%' . $request->correo . '%');
    }

    if ($request->filled('activo')) {
        $query->where('Activo', $request->activo);
    }

    if ($request->filled('rol')) {
        $query->where('Id_Rol', $request->rol);
    }

    $usuarios = $query->get();

    // Crear el archivo CSV
    $filename = 'usuarios_' . now()->format('Y-m-d_H-i-s') . '.csv';
    $handle = fopen('php://output', 'w');
    fputcsv($handle, ['ID', 'Nombre', 'Correo', 'Rol', 'Estado']); // Encabezado del CSV

    foreach ($usuarios as $usuario) {
        fputcsv($handle, [
            $usuario->Id,
            $usuario->Nombre,
            $usuario->Correo,
            $usuario->rol->Tipo,
            $usuario->Activo ? 'Activo' : 'Inactivo'
        ]);
    }

    fclose($handle);

    // Enviar el archivo como respuesta
    return response()->stream(function() use ($handle) {
        fclose($handle);
    }, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}

public function exportarExcel(Request $request)
{
    $tipo = $request->get('tipo', 'completo');
    $usuarios = Usuario::query()->with('rol');

    // Aplicar filtros si existen
    if ($request->filled('Nombre')) {
        $usuarios->where('Nombre', 'like', '%' . $request->Nombre . '%');
    }

    if ($request->has('Activo') && $request->Activo !== '') {
        $usuarios->where('Activo', $request->Activo);
    }

    if ($request->filled('rol')) {
        $usuarios->where('Id_Rol', $request->rol);
    }

    $usuarios = $usuarios->get();

    $filename = $this->generateFilename($tipo);
    
    return $this->generateExcel($usuarios, $tipo, $filename);
}


private function generateExcel($usuarios, $tipo, $filename)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar propiedades del documento
    $spreadsheet->getProperties()
        ->setCreator(config('app.name'))
        ->setTitle('Reporte de Usuarios')
        ->setSubject('Usuarios exportados desde el sistema');

    if ($tipo === 'detallado') {
        $this->generateDetailedSheet($sheet, $usuarios);
    } else {
        $this->generateSummarySheet($sheet, $usuarios);
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
 * Hoja detallada con formato
 */
private function generateDetailedSheet($sheet, $usuarios)
{
    $sheet->setCellValue('A1', 'REPORTE DETALLADO DE USUARIOS');
    $sheet->mergeCells('A1:F1');
    $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

    $sheet->setCellValue('A2', 'Generado el: ' . now()->format('d/m/Y H:i:s'));
    $sheet->mergeCells('A2:F2');
    $sheet->getStyle('A2')->applyFromArray($this->getSubtitleStyle());

    $headers = ['ID', 'Nombre', 'Correo', 'Rol', 'Estado', 'Fecha de Registro'];
    $sheet->fromArray($headers, null, 'A4');
    $sheet->getStyle('A4:F4')->applyFromArray($this->getHeaderStyle());

    $row = 5;
    foreach ($usuarios as $usuario) {
    $sheet->setCellValue('A' . $row, $usuario->Id);
    $sheet->setCellValue('B' . $row, $usuario->Nombre);
    $sheet->setCellValue('C' . $row, $usuario->Correo);
    $sheet->setCellValue('D' . $row, $usuario->rol->Tipo);
    $sheet->setCellValue('E' . $row, $usuario->Activo ? 'ACTIVO' : 'INACTIVO');
    
    // Verificar si la fecha es null antes de usar el formato
    $createdAt = $usuario->created_at ? $usuario->created_at->format('d/m/Y') : 'N/A';
    $sheet->setCellValue('F' . $row, $createdAt);

    $style = $row % 2 == 0 ? $this->getEvenRowStyle() : $this->getOddRowStyle();
    $sheet->getStyle('A' . $row . ':F' . $row)->applyFromArray($style);

    $row++;
}

}

/**
 * Hoja resumida
 */
private function generateSummarySheet($sheet, $usuarios)
{
    $sheet->setCellValue('A1', 'REPORTE RESUMIDO DE USUARIOS');
    $sheet->mergeCells('A1:E1');
    $sheet->getStyle('A1')->applyFromArray($this->getTitleStyle());

    $headers = ['ID', 'Nombre', 'Correo', 'Rol', 'Estado'];
    $sheet->fromArray($headers, null, 'A3');
    $sheet->getStyle('A3:E3')->applyFromArray($this->getHeaderStyle());

    $row = 4;
    foreach ($usuarios as $usuario) {
        $sheet->setCellValue('A' . $row, $usuario->Id);
        $sheet->setCellValue('B' . $row, $usuario->Nombre);
        $sheet->setCellValue('C' . $row, $usuario->Correo);
        $sheet->setCellValue('D' . $row, $usuario->rol->Tipo);
        $sheet->setCellValue('E' . $row, $usuario->Activo ? 'ACTIVO' : 'INACTIVO');
        $row++;
    }
}

private function generateFilename($tipo)
{
    $timestamp = now()->format('Y-m-d_His');
    return "usuarios_{$tipo}_{$timestamp}.xlsx";
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

}
