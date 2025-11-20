<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_nacimiento' => 'date',
    ];

    public $timestamps = true;

    // ============================================
    // MÉTODOS DE AUTENTICACIÓN
    // ============================================

    // Indicar a Laravel que el campo de email es 'correo'
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    // ============================================
    // RELACIONES CON OTRAS TABLAS
    // ============================================

    /**
     * Relación con Rol
     * Un usuario pertenece a un rol
     */
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    /**
     * Relación con Pedidos
     * Un usuario tiene muchos pedidos
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'usuario_id');
    }

    /**
     * Relación con Direcciones
     * Un usuario tiene muchas direcciones
     */
    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'usuario_id');
    }

    /**
     * Relación con Carritos
     * Un usuario tiene muchos carritos
     */
    public function carritos()
    {
        return $this->hasMany(Carrito::class, 'usuario_id');
    }

    /**
     * Relación con Reseñas
     * Un usuario tiene muchas reseñas
     */
    public function reseñas()
    {
        return $this->hasMany(Reseña::class, 'usuario_id');
    }

    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================

    /**
     * Obtener el nombre completo del usuario
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function esAdmin()
    {
        return $this->rol && $this->rol->nombre === 'admin';
    }

    /**
     * Verificar si el usuario es cliente
     */
    public function esCliente()
    {
        return $this->rol && $this->rol->nombre === 'cliente';
    }

    /**
     * Obtener el carrito activo del usuario
     */
    public function carritoActivo()
    {
        return $this->carritos()
            ->where('estado', 'activo')
            ->first();
    }

    /**
     * Obtener la dirección predeterminada del usuario
     */
    public function direccionPredeterminada()
    {
        return $this->direcciones()
            ->where('predeterminada', 1)
            ->first();
    }
}