<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'direcciones';

    // ✅ NO definas CREATED_AT ni UPDATED_AT
    // Laravel usará automáticamente created_at y updated_at
    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'nombre_completo',
        'telefono',
        'direccion_linea1',
        'direccion_linea2',
        'ciudad',
        'departamento',
        'codigo_postal',
        'pais',
        'predeterminada'
    ];

    protected $casts = [
        'predeterminada' => 'boolean',
    ];

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con Pedidos
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'direccion_id');
    }

    // ============================================
    // ACCESORES (GETTERS)
    // ============================================

    /**
     * Obtener la dirección completa en una sola línea
     * Ejemplo: "Calle 123, Apt 4B, Bogotá, Cundinamarca, 110111, Colombia"
     */
    public function getDireccionCompletaAttribute()
    {
        $partes = array_filter([
            $this->direccion_linea1,
            $this->direccion_linea2,
            $this->ciudad,
            $this->departamento,
            $this->codigo_postal,
            $this->pais
        ]);
        
        return implode(', ', $partes);
    }

    /**
     * Obtener la dirección formateada para mostrar en varias líneas
     */
    public function getDireccionFormateadaAttribute()
    {
        $lineas = [$this->direccion_linea1];
        
        if ($this->direccion_linea2) {
            $lineas[] = $this->direccion_linea2;
        }
        
        $lineas[] = "{$this->ciudad}, {$this->departamento}";
        $lineas[] = "{$this->codigo_postal}, {$this->pais}";
        
        return implode("\n", $lineas);
    }

    /**
     * Obtener iniciales del nombre para el avatar
     */
    public function getInicialesAttribute()
    {
        $palabras = explode(' ', $this->nombre_completo);
        $iniciales = '';
        
        foreach (array_slice($palabras, 0, 2) as $palabra) {
            $iniciales .= mb_substr($palabra, 0, 1);
        }
        
        return mb_strtoupper($iniciales);
    }

    // ============================================
    // MÉTODOS ÚTILES
    // ============================================

    /**
     * Establecer esta dirección como predeterminada
     * y quitar la marca de las demás del mismo usuario
     */
    public function establecerComoPredeterminada()
    {
        // Quitar predeterminada de todas las direcciones del usuario
        $this->usuario->direcciones()->update(['predeterminada' => 0]);
        
        // Establecer esta como predeterminada
        $this->update(['predeterminada' => 1]);
    }

    /**
     * Verificar si esta dirección es la predeterminada
     */
    public function esPredeterminada()
    {
        return (bool) $this->predeterminada;
    }

    /**
     * Verificar si la dirección está completa
     */
    public function estaCompleta()
    {
        return !empty($this->nombre_completo) &&
               !empty($this->telefono) &&
               !empty($this->direccion_linea1) &&
               !empty($this->ciudad) &&
               !empty($this->departamento) &&
               !empty($this->codigo_postal) &&
               !empty($this->pais);
    }

    /**
     * Obtener un resumen corto de la dirección
     * Ejemplo: "Calle 123, Bogotá"
     */
    public function getResumenAttribute()
    {
        return "{$this->direccion_linea1}, {$this->ciudad}";
    }

    // ============================================
    // EVENTOS DEL MODELO
    // ============================================

    /**
     * Acciones automáticas al crear una dirección
     */
    protected static function boot()
    {
        parent::boot();

        // Si es la primera dirección del usuario, hacerla predeterminada
        static::creating(function ($direccion) {
            if (!$direccion->usuario->direcciones()->exists()) {
                $direccion->predeterminada = true;
            }
        });

        // Al eliminar, si era predeterminada, establecer otra como predeterminada
        static::deleting(function ($direccion) {
            if ($direccion->predeterminada) {
                $siguiente = $direccion->usuario
                    ->direcciones()
                    ->where('id', '!=', $direccion->id)
                    ->first();
                
                if ($siguiente) {
                    $siguiente->update(['predeterminada' => 1]);
                }
            }
        });
    }
}