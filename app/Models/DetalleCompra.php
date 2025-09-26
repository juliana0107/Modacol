<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalle_compras';
    protected $primaryKey = 'Id';
    
    // Desactivar timestamps automáticos
    public $timestamps = false;

     protected $fillable = [
        'Compra_Id',
        'Producto_Id',
        'PrecioU',
        'Cantidad'
    ];

    protected $casts = [
        'Cantidad' => 'integer',
        'PrecioUnitario' => 'decimal:2',
        'Subtotal' => 'decimal:2'
    ];

    // Relación con compra
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'Compra_Id');
    }

    // Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'Producto_Id');
    }
}
