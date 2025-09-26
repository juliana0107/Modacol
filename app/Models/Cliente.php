<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'Clientes';
    protected $primaryKey = 'Id';
    public $timestamps = true;

    protected $fillable = [
        'Empresa',
        'Nombre',
        'Identificacion',
        'Contacto',
        'Correo',
        'Activo'
    ];

    public function ventas() {
        return $this->hasMany(Venta::class, 'Cliente_id');
    }
}

