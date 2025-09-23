<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;

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
        'Nombre' => 'required|string|max:100',
        'Correo' => 'required|email|unique:Usuarios,Correo', // Verifica que el correo sea único
        'Contraseña' => 'required|string|min:4|confirmed', // Confirmación de la contraseña (repetir contraseña en el formulario)
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
        'Nombre' => 'required|string|max:100',
        'Correo' => 'required|email|unique:Usuarios,Correo,' . $usuario->Id, // Asegura que el correo sea único, excepto el actual
        'Id_Rol' => 'required|exists:Roles,Id', // Verifica que el rol exista
        'Contraseña' => 'nullable|string|min:4|confirmed', // La contraseña puede no ser modificada, si se pasa debe ser confirmada
    ]);

    // Actualizamos los datos del usuario
    $data = $request->only(['Nombre', 'Correo', 'Id_Rol']);
    
    if ($request->filled('Contraseña')) {
        $data['Contraseña'] = bcrypt($request->Contraseña); // Encriptamos la nueva contraseña
    }

    $usuario->update($data); // Actualiza el usuario

    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado con éxito');
}

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado');
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
}