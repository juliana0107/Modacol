<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlujoCaja extends Model
{
    use HasFactory;

    protected $table = 'flujo_caja';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Fecha',
        'Saldo_Neto',
        'Saldo_Final',
        'Activo'
    ];

    // Relación con detalles de flujo de caja
    public function detallesFlujo() {
        return $this->hasMany(DetalleFlujoCaja::class, 'Flujo_Id');
    }
}
