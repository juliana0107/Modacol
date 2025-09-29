<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'Productos';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Fecha',
        'Nombre',
        'Descripcion',
        'PrecioU',
        'Cantidad',
        'Activo'
    ];

    protected $casts = [
        'PrecioU' => 'decimal:2',
        'Cantidad' => 'integer',
        'Activo' => 'boolean'
    ];

    public function detallesVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'Producto_id');
    }
}