<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios'; // Nombre de tu tabla
    protected $fillable = [
        'rol_id', 'nombre', 'apellido', 'correo', 'contraseña', 'telefono'
    ];

    protected $hidden = [
        'contraseña'
    ];

    // Asegurarte de que la contraseña se guarde en hash
    public function setContraseñaAttribute($value)
    {
        $this->attributes['contraseña'] = Hash::make($value);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}
