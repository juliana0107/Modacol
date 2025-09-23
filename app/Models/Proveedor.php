<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'Proveedores';
    protected $primaryKey = 'Id';
    
    // Desactivar timestamps automáticos
    public $timestamps = false;

    protected $fillable = [
        'RazonSocial',
        'Identificacion',
        'Direccion',
        'Correo',
        'Contacto',
        'Activo'
    ];

    protected $casts = [
        'Identificacion' => 'integer',
        'Contacto' => 'string',
        'Activo' => 'boolean'
    ];

    // Relación con compras (si existe esa relación)
    public function compras()
    {
        return $this->hasMany(Compra::class, 'Proveedor_Id');
    }
}