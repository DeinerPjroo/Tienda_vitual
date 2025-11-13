<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'correo',
        'password',
        'telefono',
        'fecha_nacimiento',
        'activo'
    ];

    public $timestamps = true;

    // Evitar que Laravel encripte password automáticamente
    protected $hidden = [
        'password'
    ];
}
