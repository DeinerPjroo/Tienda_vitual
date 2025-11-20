<?php

// ============================================
// App\Models\Variacion.php (CORREGIDO)
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variacion extends Model
{
    use HasFactory;

    protected $table = 'variaciones';

    protected $fillable = [
        'prenda_id',
        'sku',
        'color',
        'talla',
        'stock',
        'precio_adicional'
    ];

    protected $casts = [
        'stock' => 'integer',
        'precio_adicional' => 'decimal:2',
    ];

    // ✅ Tu tabla usa created_at y updated_at (estándar de Laravel)
    public $timestamps = true;

    /**
     * Relación con Prenda
     */
    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }

    /**
     * Relación con Items de Carrito
     */
    public function itemsCarrito()
    {
        return $this->hasMany(ItemCarrito::class, 'variacion_id');
    }

    /**
     * Relación con Items de Pedido
     */
    public function itemsPedido()
    {
        return $this->hasMany(ItemPedido::class, 'variacion_id');
    }

    /**
     * Obtener el precio final de la variación
     */
    public function getPrecioFinalAttribute()
    {
        $prenda = $this->prenda;
        $precioBase = $prenda->precio + $this->precio_adicional;
        $descuento = $prenda->descuento ?? 0;
        
        return $precioBase * (1 - $descuento / 100);
    }

    /**
     * Verificar si hay stock disponible
     */
    public function hayStock($cantidad = 1)
    {
        return $this->stock >= $cantidad;
    }

    /**
     * Decrementar stock
     */
    public function decrementarStock($cantidad)
    {
        if (!$this->hayStock($cantidad)) {
            return false;
        }

        $this->decrement('stock', $cantidad);
        return true;
    }

    /**
     * Incrementar stock (para devoluciones)
     */
    public function incrementarStock($cantidad)
    {
        $this->increment('stock', $cantidad);
    }
}