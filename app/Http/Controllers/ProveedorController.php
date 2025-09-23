<?php
// app/Http/Controllers/ProveedorController.php
namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'RazonSocial' => 'required|string|max:100',
            'Identificacion' => 'required|numeric|unique:Proveedores,Identificacion',
            'Direccion' => 'required|string|max:150',
            'Correo' => 'required|email|unique:Proveedores,Correo',
            'Contacto' => 'required|string|max:100'
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
            'RazonSocial' => 'required|string|max:100',
            'Identificacion' => 'required|numeric|unique:Proveedores,Identificacion,' . $id,
            'Direccion' => 'required|string|max:150',
            'Correo' => 'required|email|unique:Proveedores,Correo,' . $id,
            'Contacto' => 'required|string|max:100'
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
}