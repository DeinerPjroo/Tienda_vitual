<?php

// ============================================
// App\Models\Prenda.php (CORREGIDO)
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prenda extends Model
{
    use HasFactory;

    protected $table = 'prendas';

    protected $fillable = [
        'categoria_id',
        'sku',
        'nombre',
        'descripcion_corta',
        'descripcion',
        'precio',
        'descuento',
        'activo'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'descuento' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // ✅ Tu tabla usa created_at y updated_at (estándar de Laravel)
    public $timestamps = true;

    /**
     * Relación con Categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    /**
     * Relación con Variaciones
     */
    public function variaciones()
    {
        return $this->hasMany(Variacion::class, 'prenda_id');
    }

    /**
     * Relación con Imágenes
     */
    public function imagenes()
    {
        return $this->hasMany(ImagenPrenda::class, 'prenda_id')->orderBy('posicion');
    }

    /**
     * Obtener precio con descuento aplicado
     */
    public function getPrecioFinalAttribute()
    {
        return $this->precio * (1 - $this->descuento / 100);
    }

    /**
     * Obtener primera imagen
     */
    public function getPrimeraImagenAttribute()
    {
        return $this->imagenes->first();
    }

    /**
     * Verificar si tiene descuento
     */
    public function tieneDescuento()
    {
        return $this->descuento > 0;
    }

    /**
     * Obtener stock total de todas las variaciones
     */
    public function getStockTotalAttribute()
    {
        return $this->variaciones->sum('stock');
    }
}