<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleFlujoCaja extends Model
{
    use HasFactory;

    protected $table = 'detalle_flujo_caja';
    protected $primaryKey = 'Id';
    public $timestamps = false; // Si no se usa el sistema de timestamps

    protected $fillable = [
        'Flujo_Id',
        'Fecha',
        'Cliente_Id',
        'Proveedor_Id',
        'Tipo_Movimiento',
        'Monto',
        'Categoria_Id'
    ];

    // Relación con flujo_caja
    public function flujoCaja() {
        return $this->belongsTo(FlujoCaja::class, 'Flujo_Id');
    }

    // Relación con Cliente
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'Cliente_Id');
    }

    // Relación con Proveedor
    public function proveedor() {
        return $this->belongsTo(Proveedor::class, 'Proveedor_Id');
    }

    // Relación con Categoria
    public function categoria() {
        return $this->belongsTo(Categoria::class, 'Categoria_Id');
    }
}
