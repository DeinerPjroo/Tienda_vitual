<?php

// ============================================
// App\Models\ItemPedido.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    use HasFactory;

    protected $table = 'items_pedido';

    protected $fillable = [
        'pedido_id',
        'variacion_id',
        'nombre_prenda_snapshot',
        'cantidad',
        'precio_unitario'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'creado_en' => 'datetime'
    ];

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null; // Esta tabla NO tiene updated_at

    public $timestamps = true;

    /**
     * Relación con Pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Relación con Variación
     */
    public function variacion()
    {
        return $this->belongsTo(Variacion::class, 'variacion_id');
    }

    /**
     * Obtener subtotal del item (calculado automáticamente en BD)
     */
    public function getSubtotalAttribute()
    {
        return $this->precio_unitario * $this->cantidad;
    }
}
