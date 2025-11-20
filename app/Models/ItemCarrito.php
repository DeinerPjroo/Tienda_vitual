
<?php
// ============================================
// App\Models\ItemCarrito.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCarrito extends Model
{
    use HasFactory;

    protected $table = 'items_carrito';

    protected $fillable = [
        'carrito_id',
        'variacion_id',
        'cantidad',
        'precio_unitario'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
    ];

    public $timestamps = true;

    /**
     * Relación con Carrito
     */
    public function carrito()
    {
        return $this->belongsTo(Carrito::class, 'carrito_id');
    }

    /**
     * Relación con Variación
     */
    public function variacion()
    {
        return $this->belongsTo(Variacion::class, 'variacion_id');
    }

    /**
     * Obtener el subtotal del item (calculado automáticamente)
     * La columna 'subtotal' en la BD es VIRTUAL GENERATED
     */
    public function getSubtotalAttribute()
    {
        return $this->precio_unitario * $this->cantidad;
    }

    /**
     * Verificar si hay suficiente stock
     */
    public function tieneStock()
    {
        return $this->variacion->stock >= $this->cantidad;
    }

    /**
     * Incrementar cantidad
     */
    public function incrementarCantidad($cantidad = 1)
    {
        $nuevaCantidad = $this->cantidad + $cantidad;
        
        if ($nuevaCantidad > $this->variacion->stock) {
            return false;
        }

        $this->update(['cantidad' => $nuevaCantidad]);
        return true;
    }

    /**
     * Decrementar cantidad
     */
    public function decrementarCantidad($cantidad = 1)
    {
        $nuevaCantidad = $this->cantidad - $cantidad;
        
        if ($nuevaCantidad < 1) {
            return false;
        }

        $this->update(['cantidad' => $nuevaCantidad]);
        return true;
    }
}
