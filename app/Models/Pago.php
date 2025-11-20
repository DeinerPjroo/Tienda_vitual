<?php

// ============================================
// App\Models\Pago.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'pedido_id',
        'metodo',
        'referencia_transaccion',
        'monto',
        'moneda',
        'estado',
        'pagado_en'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'pagado_en' => 'datetime',
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
     * Verificar si el pago está aprobado
     */
    public function estaAprobado()
    {
        return $this->estado === 'aprobado';
    }

    /**
     * Marcar pago como aprobado
     */
    public function aprobar($referenciaTransaccion = null)
    {
        $this->update([
            'estado' => 'aprobado',
            'pagado_en' => now(),
            'referencia_transaccion' => $referenciaTransaccion ?? $this->referencia_transaccion
        ]);

        // Actualizar estado del pedido a 'pagado'
        $this->pedido->actualizarEstado('pagado');
    }

    /**
     * Obtener nombre del método de pago
     */
    public function getMetodoNombreAttribute()
    {
        $metodos = [
            'contraentrega' => 'Contra Entrega',
            'tarjeta' => 'Tarjeta de Crédito/Débito',
            'transferencia' => 'Transferencia Bancaria',
            'pse' => 'PSE'
        ];

        return $metodos[$this->metodo] ?? $this->metodo;
    }
}