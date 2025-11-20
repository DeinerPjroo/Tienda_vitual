<?php
// ============================================
// App\Models\Pedido.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'usuario_id',
        'direccion_id',
        'total',
        'costo_envio',
        'impuestos',
        'estado',
        'nota'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'costo_envio' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'fecha_pedido' => 'datetime',
        'actualizado_en' => 'datetime'
    ];

    const CREATED_AT = 'fecha_pedido';
    const UPDATED_AT = 'actualizado_en';

    public $timestamps = true;

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con Dirección
     */
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'direccion_id');
    }

    /**
     * Relación con Items del Pedido
     */
    public function items()
    {
        return $this->hasMany(ItemPedido::class, 'pedido_id');
    }

    /**
     * Relación con Pagos
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'pedido_id');
    }

    /**
     * Obtener el total final (total + envío + impuestos)
     */
    public function getTotalFinalAttribute()
    {
        return $this->total + $this->costo_envio + $this->impuestos;
    }

    /**
     * Obtener cantidad total de items
     */
    public function getCantidadTotalItemsAttribute()
    {
        return $this->items->sum('cantidad');
    }

    /**
     * Verificar si el pedido puede ser cancelado
     */
    public function puedeCancelarse()
    {
        return in_array($this->estado, ['pendiente', 'pagado']);
    }

    /**
     * Verificar si el pedido está completado
     */
    public function estaCompletado()
    {
        return $this->estado === 'entregado';
    }

    /**
     * Actualizar estado del pedido
     */
    public function actualizarEstado($nuevoEstado)
    {
        $estadosValidos = ['pendiente', 'pagado', 'procesando', 'enviado', 'entregado', 'cancelado', 'reembolsado'];
        
        if (!in_array($nuevoEstado, $estadosValidos)) {
            return false;
        }

        $this->update(['estado' => $nuevoEstado]);
        return true;
    }
}















