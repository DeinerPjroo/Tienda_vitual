<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        
        // Obtener o crear carrito activo del usuario
        $carrito = DB::table('carritos')
            ->where('usuario_id', $usuario->id)
            ->where('estado', 'activo')
            ->first();

        if (!$carrito) {
            // Crear un nuevo carrito si no existe
            $carritoId = DB::table('carritos')->insertGetId([
                'usuario_id' => $usuario->id,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $carrito = DB::table('carritos')->find($carritoId);
        }

        // Obtener items del carrito con información de productos
        $items = DB::table('items_carrito')
            ->join('variaciones', 'items_carrito.variacion_id', '=', 'variaciones.id')
            ->join('prendas', 'variaciones.prenda_id', '=', 'prendas.id')
            ->leftJoin('imagenes_prenda', function($join) {
                $join->on('prendas.id', '=', 'imagenes_prenda.prenda_id')
                     ->whereRaw('imagenes_prenda.posicion = (SELECT MIN(posicion) FROM imagenes_prenda WHERE prenda_id = prendas.id)');
            })
            ->where('items_carrito.carrito_id', $carrito->id)
            ->select(
                'items_carrito.*',
                'prendas.nombre as producto_nombre',
                'prendas.precio',
                'prendas.descuento',
                'variaciones.color',
                'variaciones.talla',
                'variaciones.stock',
                'imagenes_prenda.url as imagen_url'
            )
            ->get();

        // Calcular totales
        $subtotal = 0;
        foreach ($items as $item) {
            $precioFinal = $item->precio * (1 - $item->descuento / 100);
            $subtotal += $precioFinal * $item->cantidad;
        }

        $envio = $subtotal >= 150000 ? 0 : 7000;
        $total = $subtotal + $envio;

        return view('carrito', compact('items', 'subtotal', 'envio', 'total', 'carrito'));
    }

    public function agregar(Request $request)
    {
        $request->validate([
            'prenda_id' => 'required|exists:prendas,id',
            'variacion_id' => 'nullable|exists:variaciones,id',
            'cantidad' => 'required|integer|min:1'
        ]);

        $usuario = Auth::user();

        // Obtener o crear carrito activo
        $carrito = DB::table('carritos')
            ->where('usuario_id', $usuario->id)
            ->where('estado', 'activo')
            ->first();

        if (!$carrito) {
            $carritoId = DB::table('carritos')->insertGetId([
                'usuario_id' => $usuario->id,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $carritoId = $carrito->id;
        }

        // Obtener información del producto
        $producto = DB::table('prendas')->find($request->prenda_id);

        // Si no hay variación específica, obtener la primera disponible
        $variacionId = $request->variacion_id;
        if (!$variacionId) {
            $variacion = DB::table('variaciones')
                ->where('prenda_id', $request->prenda_id)
                ->where('stock', '>', 0)
                ->first();
            
            if (!$variacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto sin stock disponible'
                ], 400);
            }
            
            $variacionId = $variacion->id;
        }

        // Calcular precio con descuento
        $precioFinal = $producto->precio * (1 - $producto->descuento / 100);

        // Verificar si ya existe en el carrito
        $itemExistente = DB::table('items_carrito')
            ->where('carrito_id', $carritoId)
            ->where('variacion_id', $variacionId)
            ->first();

        if ($itemExistente) {
            // Actualizar cantidad
            DB::table('items_carrito')
                ->where('id', $itemExistente->id)
                ->update([
                    'cantidad' => $itemExistente->cantidad + $request->cantidad,
                    'updated_at' => now()
                ]);
        } else {
            // Crear nuevo item
            DB::table('items_carrito')->insert([
                'carrito_id' => $carritoId,
                'variacion_id' => $variacionId,
                'cantidad' => $request->cantidad,
                'precio_unitario' => $precioFinal,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito'
        ]);
    }

    public function actualizar(Request $request, $itemId)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        DB::table('items_carrito')
            ->where('id', $itemId)
            ->update([
                'cantidad' => $request->cantidad,
                'updated_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    public function eliminar($itemId)
    {
        DB::table('items_carrito')->where('id', $itemId)->delete();
        
        return response()->json(['success' => true]);
    }
}