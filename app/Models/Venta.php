<?php
// app/Models/Venta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'Ventas';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = [
        'Fecha',
        'Usuario_id',
        'Cliente_id',
        'ValorTotal',
        'Activo'
    ];

    protected $casts = [
        'ValorTotal' => 'decimal:2',
        'Activo' => 'boolean'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Cliente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'Usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'Venta_id');
    }
}