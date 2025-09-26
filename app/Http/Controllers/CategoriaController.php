<?php
namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CategoriaController extends Controller
{
    // Obtener todas las categorías
    public function index(Request $request)
    {
        $query = Categoria::query();
        
        // Filtros si son enviados desde la vista
        if ($request->has('tipo_categoria')) {
            $query->where('Tipo_categoria', 'like', '%' . $request->tipo_categoria . '%');
        }

        if ($request->has('estado')) {
            $query->where('Activo', $request->estado);
        }

        $categorias = $query->get(); // Obtener las categorías con los filtros

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create'); // Retorna la vista para crear una categoría
    }

    // Método para guardar la nueva categoría
    public function store(Request $request)
    {
        $request->validate([
            'Tipo_categoria' => 'required|string|max:100',
        ]);

        $categoria = Categoria::create([
            'Tipo_categoria' => $request->Tipo_categoria,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    // Obtener una categoría específica
    public function show($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categorias.show', compact('categoria'));
    }

    // Actualizar una categoría
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->Tipo_categoria = $request->Tipo_categoria;
        $categoria->save();
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    // Cambiar el estado de la categoría (activo/inactivo)
    public function toggle($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->Activo = !$categoria->Activo; // Cambiar estado
        $categoria->save();

        return redirect()->route('categorias.index')->with('success', 'Estado de la categoría actualizado.');
    }

     public function exportarExcel()
    {
        // Crear una nueva instancia de Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Definir las cabeceras de la hoja
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Tipo de Categoría');

        // Obtener las categorías de la base de datos
        $categorias = Categoria::all();
        $row = 2; // Empezamos en la fila 2 (debajo de las cabeceras)

        foreach ($categorias as $categoria) {
            $sheet->setCellValue('A' . $row, $categoria->Id);
            $sheet->setCellValue('B' . $row, $categoria->Tipo_categoria);
            $row++;
        }

        // Crear el escritor para guardar el archivo Excel
        $writer = new Xlsx($spreadsheet);

        // Definir el nombre del archivo
        $filename = 'categorias.xlsx';

        // Forzar la descarga del archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Escribir el archivo Excel a la salida
        $writer->save('php://output');
        exit();
    }
}


