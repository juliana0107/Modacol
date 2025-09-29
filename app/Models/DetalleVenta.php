<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'Detalles_Ventas';

    public $timestamps = false;
    protected $fillable = 
    [
        'Venta_id', 
        'Producto_id', 
        'Cantidad', 
        'Iva', 
        'SubTotal',
        'Activo'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'Venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'Producto_id');
    }
}

