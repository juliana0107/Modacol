<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'Compras';
    protected $primaryKey = 'Id';
    
    // Desactivar timestamps automáticos
    public $timestamps = false;

    protected $fillable = [
        'Proveedor_Id',
        'FechaCompra',
        'Total',
        'Estado',
        'Observaciones'
    ];

    protected $casts = [
        'FechaCompra' => 'datetime',
        'Total' => 'decimal:2',
        'Estado' => 'string'
    ];

    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'Proveedor_Id');
    }

    // Relación con detalles de compra
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'Compra_Id');
    }
}