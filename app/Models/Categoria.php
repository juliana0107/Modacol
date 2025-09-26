<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias'; // Nombre de la tabla
    protected $primaryKey = 'Id'; // Clave primaria
    public $timestamps = false; // Si no usas timestamps como created_at y updated_at

    protected $fillable = [
        'Id',
        'Tipo_categoria',
    ];

    // Relación con la tabla 'detalle_flujo_caja'
    public function detalleFlujos()
    {
        return $this->hasMany(DetalleFlujoCaja::class, 'Categoria_Id', 'Id');
    }
}


