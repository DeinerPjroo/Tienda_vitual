<?php
// ============================================
// App\Models\Carrito.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carritos';

    protected $fillable = [
        'usuario_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'string',
    ];

    public $timestamps = true;

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Relación con Items del Carrito
     */
    public function items()
    {
        return $this->hasMany(ItemCarrito::class, 'carrito_id');
    }

    /**
     * Obtener el subtotal del carrito
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function($item) {
            return $item->subtotal;
        });
    }

    /**
     * Obtener el total de items en el carrito
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('cantidad');
    }

    /**
     * Calcular costo de envío
     */
    public function getCostoEnvioAttribute()
    {
        return $this->subtotal >= 150000 ? 0 : 7000;
    }

    /**
     * Obtener el total del carrito (subtotal + envío)
     */
    public function getTotalAttribute()
    {
        return $this->subtotal + $this->costo_envio;
    }

    /**
     * Limpiar el carrito
     */
    public function vaciar()
    {
        $this->items()->delete();
    }

    /**
     * Marcar carrito como completado
     */
    public function completar()
    {
        $this->update(['estado' => 'completado']);
    }
}


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


// ============================================
// App\Models\Variacion.php
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
     * (precio de la prenda + precio adicional - descuento)
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

    /**
     * Scope para variaciones con stock
     */
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Obtener nombre completo de la variación
     */
    public function getNombreCompletoAttribute()
    {
        $parts = [];
        
        if ($this->color) {
            $parts[] = "Color: {$this->color}";
        }
        
        if ($this->talla) {
            $parts[] = "Talla: {$this->talla}";
        }
        
        return implode(' | ', $parts);
    }
}


// ============================================
// App\Models\Prenda.php (actualizado)
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
     * Relación con Reseñas
     */
    public function resenas()
    {
        return $this->hasMany(Reseña::class, 'prenda_id');
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

    /**
     * Verificar si tiene stock disponible
     */
    public function tieneStock()
    {
        return $this->stock_total > 0;
    }

    /**
     * Scope para productos activos
     */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para productos con descuento
     */
    public function scopeConDescuento($query)
    {
        return $query->where('descuento', '>', 0);
    }
}


// ============================================
// App\Models\ImagenPrenda.php
// ============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenPrenda extends Model
{
    use HasFactory;

    protected $table = 'imagenes_prenda';

    // Esta tabla usa 'creado_en' en lugar de 'created_at'
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null; // No tiene updated_at

    protected $fillable = [
        'prenda_id',
        'url',
        'texto_alternativo',
        'posicion'
    ];

    protected $casts = [
        'posicion' => 'integer',
    ];

    public $timestamps = false; // Cambiado a false ya que solo tiene creado_en

    /**
     * Relación con Prenda
     */
    public function prenda()
    {
        return $this->belongsTo(Prenda::class, 'prenda_id');
    }
}


