<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'Roles';
    protected $primaryKey = 'Id';
    protected $fillable = 
    [
        'Tipo',
        'Activo'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'Id_Rol');
    }
}
