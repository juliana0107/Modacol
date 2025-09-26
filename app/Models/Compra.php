<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'Id';
    public $timestamps = false; // Si no usas timestamps

    protected $fillable = [
        'Fecha_Inicio',
        'Fecha_Entrega',
        'Proveedor_Id',
        'Usuario_Id',
        'Total',
        'Activo'
    ];

    // Relación con Proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'Proveedor_Id');
    }

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Usuario_Id');
    }

    // Relación con DetalleCompra (Productos comprados)
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'Compra_Id');
    }
    public function show($id)
{
    $compra = Compra::with(['proveedor', 'detalles.producto', 'usuario'])
                    ->findOrFail($id);
    
    return view('compras.show', compact('compra'));
}
}
