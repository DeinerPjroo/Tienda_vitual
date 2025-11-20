<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\Carrito;
use App\Models\ItemCarrito;
use App\Models\ItemPedido;
use App\Models\Direccion;
use App\Models\Pago;

class PedidoController extends Controller
{
    /**
     * Mostrar página de checkout
     */
    public function checkout()
    {
        $usuario = Auth::user();

        // Obtener carrito activo
        $carrito = Carrito::where('usuario_id', $usuario->id)
            ->where('estado', 'activo')
            ->first();

        if (!$carrito || $carrito->items->count() == 0) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío');
        }

        // Obtener items del carrito con sus relaciones
        $items = $carrito->items()
            ->with(['variacion.prenda.imagenes'])
            ->get();

        // Verificar stock disponible
        foreach ($items as $item) {
            if (!$item->variacion->hayStock($item->cantidad)) {
                return redirect()->route('carrito.index')
                    ->with('error', "Stock insuficiente para {$item->variacion->prenda->nombre}");
            }
        }

        // Obtener direcciones del usuario
        $direcciones = Direccion::where('usuario_id', $usuario->id)->get();

        // Calcular totales
        $subtotal = $items->sum(function($item) {
            return $item->precio_unitario * $item->cantidad;
        });

        $envio = $subtotal >= 150000 ? 0 : 7000;
        $total = $subtotal + $envio;

        return view('checkout', compact('items', 'direcciones', 'subtotal', 'envio', 'total', 'carrito'));
    }

    /**
     * Crear el pedido
     */
    public function crear(Request $request)
    {
        $request->validate([
            'direccion_id' => 'required|exists:direcciones,id',
            'metodo_pago' => 'required|in:contraentrega,tarjeta,transferencia,pse',
            'nota' => 'nullable|string|max:500'
        ]);

        $usuario = Auth::user();

        // Verificar que la dirección pertenezca al usuario
        $direccion = Direccion::where('id', $request->direccion_id)
            ->where('usuario_id', $usuario->id)
            ->firstOrFail();

        // Obtener carrito activo
        $carrito = Carrito::where('usuario_id', $usuario->id)
            ->where('estado', 'activo')
            ->with(['items.variacion.prenda'])
            ->firstOrFail();

        if ($carrito->items->count() == 0) {
            return redirect()->route('carrito.index')
                ->with('error', 'Tu carrito está vacío');
        }

        // Usar transacción para garantizar integridad
        DB::beginTransaction();

        try {
            // Calcular totales
            $subtotal = 0;
            foreach ($carrito->items as $item) {
                // Verificar stock nuevamente
                if (!$item->variacion->hayStock($item->cantidad)) {
                    throw new \Exception("Stock insuficiente para {$item->variacion->prenda->nombre}");
                }
                $subtotal += $item->precio_unitario * $item->cantidad;
            }

            $costoEnvio = $subtotal >= 150000 ? 0 : 7000;
            $total = $subtotal + $costoEnvio;

            // Crear el pedido
            $pedido = Pedido::create([
                'usuario_id' => $usuario->id,
                'direccion_id' => $direccion->id,
                'total' => $subtotal,
                'costo_envio' => $costoEnvio,
                'impuestos' => 0, // Por ahora sin impuestos
                'estado' => 'pendiente',
                'nota' => $request->nota,
                'fecha_pedido' => now()
            ]);

            // Crear items del pedido y decrementar stock
            foreach ($carrito->items as $item) {
                // Crear item del pedido
                ItemPedido::create([
                    'pedido_id' => $pedido->id,
                    'variacion_id' => $item->variacion_id,
                    'nombre_prenda_snapshot' => $item->variacion->prenda->nombre,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario
                ]);

                // Decrementar stock
                $item->variacion->decrementarStock($item->cantidad);
            }

            // Crear registro de pago
            Pago::create([
                'pedido_id' => $pedido->id,
                'metodo' => $request->metodo_pago,
                'monto' => $total,
                'moneda' => 'COP',
                'estado' => $request->metodo_pago === 'contraentrega' ? 'pendiente' : 'pendiente'
            ]);

            // Vaciar el carrito
            $carrito->items()->delete();
            $carrito->update(['estado' => 'completado']);

            DB::commit();

            return redirect()->route('pedido.confirmacion', $pedido->id)
                ->with('success', '¡Pedido realizado exitosamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('checkout')
                ->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar confirmación del pedido
     */
    public function confirmacion($id)
{
    $usuario = Auth::user();

    $pedido = Pedido::where('id', $id)
        ->where('usuario_id', $usuario->id)
        ->firstOrFail();

    return redirect()->route('pedidos')
        ->with('confirmacion', true)
        ->with('pedido_id', $pedido->id);
}




    /**
     * Ver detalle de un pedido
     */
    public function detalle($id)
    {
        $usuario = Auth::user();

        $pedido = Pedido::where('id', $id)
            ->where('usuario_id', $usuario->id)
            ->with(['items.variacion.prenda.imagenes', 'direccion', 'pagos'])
            ->firstOrFail();

        return view('pedido-detalle', compact('pedido'));
    }

    /**
     * Cancelar un pedido
     */
    public function cancelar($id)
    {
        $usuario = Auth::user();

        $pedido = Pedido::where('id', $id)
            ->where('usuario_id', $usuario->id)
            ->with(['items.variacion'])
            ->firstOrFail();

        // Solo se pueden cancelar pedidos pendientes o pagados (no enviados)
        if (!in_array($pedido->estado, ['pendiente', 'pagado'])) {
            return back()->with('error', 'Este pedido no puede ser cancelado');
        }

        DB::beginTransaction();

        try {
            // Devolver stock
            foreach ($pedido->items as $item) {
                $item->variacion->incrementarStock($item->cantidad);
            }

            // Actualizar estado del pedido
            $pedido->update(['estado' => 'cancelado']);

            // Si había pago, marcarlo como reembolsado
            if ($pedido->pagos()->where('estado', 'aprobado')->exists()) {
                $pedido->pagos()->update(['estado' => 'reembolsado']);
            }

            DB::commit();

            return back()->with('success', 'Pedido cancelado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar el pedido');
        }
    }

    /**
     * Rastrear pedido
     */
    public function rastrear($id)
    {
        $usuario = Auth::user();

        $pedido = Pedido::where('id', $id)
            ->where('usuario_id', $usuario->id)
            ->with(['items.variacion.prenda.imagenes', 'direccion'])
            ->firstOrFail();

        return view('pedido-rastreo', compact('pedido'));
    }
}