<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{

    protected $table = 'Usuarios';
    protected $primaryKey = 'Id';
    public $timestamps = false;

    protected $fillable = 
    [
        'Nombre', 
        'Correo', 
        'Contraseña', 
        'Id_Rol',
        'Activo'
    ];

    protected $hidden = [
        'Contraseña', 'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->Contraseña;
    }

    public function getAuthIdentifierName()
    {
        return 'Correo';
    }

    public function rol()
    {
        return $this->belongsTo(Role::class, 'Id_Rol');
    }

    public function isAdminGeneral()
    {
        return $this->Id_Rol === 1;
    }

    public function isOperativo()
    {
        return $this->Id_Rol === 2;
    }

    public function isCaja()
    {
        return $this->Id_Rol === 3;
    }

     public function getRoleName()
    {
        $roles = [
            1 => 'Administrador General',
            2 => 'Administrador Operativo', 
            3 => 'Administrador Flujo de Caja'
        ];
        
        return $roles[$this->Id_Rol] ?? 'Sin Rol';
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'Usuario_id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'Usuario_Id');
    }
}

?>