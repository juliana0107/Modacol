<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Role;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nombre' => 'required',
            'Correo' => 'required|email|unique:Usuarios,Correo',
            'Contraseña' => 'required|min:4',
            'Id_Rol' => 'required|exists:Roles,Id',
        ]);

        Usuario::create($request->all());
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
            'Nombre' => 'required',
            'Correo' => 'required|email|unique:Usuarios,Correo,'.$usuario->Id,
            'Id_Rol' => 'required|exists:Roles,Id',
        ]);

       
        $data = $request->only(['Nombre', 'Correo', 'Id_Rol']);
         if ($request->filled('Contraseña')) {
        $data['Contraseña'] = $request->Contraseña;
        $usuario->update($data);
    
    return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado');
    }
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
}