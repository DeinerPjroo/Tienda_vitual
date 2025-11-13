<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function buscar(Request $request)
{
    $query = $request->input('q');
    
    if (empty($query)) {
        return redirect()->route('home')->with('error', 'Por favor ingresa un término de búsqueda');
    }

    // CAMBIO AQUÍ: usar MIN(posicion) en lugar de WHERE posicion = 0
    $productos = DB::table('prendas')
        ->leftJoin('categorias', 'prendas.categoria_id', '=', 'categorias.id')
        ->leftJoin('imagenes_prenda', function($join) {
            $join->on('prendas.id', '=', 'imagenes_prenda.prenda_id')
                 ->whereRaw('imagenes_prenda.posicion = (SELECT MIN(posicion) FROM imagenes_prenda WHERE prenda_id = prendas.id)');
        })
        ->select(
            'prendas.*',
            'categorias.nombre as categoria_nombre',
            'imagenes_prenda.url as imagen_url'
        )
        ->where('prendas.activo', 1)
        ->where(function($q) use ($query) {
            $q->where('prendas.nombre', 'LIKE', "%{$query}%")
              ->orWhere('prendas.descripcion', 'LIKE', "%{$query}%")
              ->orWhere('prendas.descripcion_corta', 'LIKE', "%{$query}%")
              ->orWhere('categorias.nombre', 'LIKE', "%{$query}%");
        })
        ->paginate(12);

    return view('buscar', [
        'productos' => $productos,
        'query' => $query,
        'total' => $productos->total()
    ]);
}

    public function detalle($id)
    {
        // Obtener el producto con su categoría
        $producto = DB::table('prendas')
            ->leftJoin('categorias', 'prendas.categoria_id', '=', 'categorias.id')
            ->select('prendas.*', 'categorias.nombre as categoria_nombre')
            ->where('prendas.id', $id)
            ->where('prendas.activo', 1)
            ->first();

        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        // Obtener todas las imágenes del producto
        $imagenes = DB::table('imagenes_prenda')
            ->where('prenda_id', $id)
            ->orderBy('posicion')
            ->get();

        // Obtener todas las variaciones (colores, tallas, stock)
        $variaciones = DB::table('variaciones')
            ->where('prenda_id', $id)
            ->get();

        // Obtener colores únicos disponibles
        $coloresDisponibles = DB::table('variaciones')
            ->where('prenda_id', $id)
            ->where('stock', '>', 0)
            ->whereNotNull('color')
            ->select('color')
            ->distinct()
            ->get();

        // Obtener tallas únicas disponibles
        $tallasDisponibles = DB::table('variaciones')
            ->where('prenda_id', $id)
            ->where('stock', '>', 0)
            ->whereNotNull('talla')
            ->select('talla')
            ->distinct()
            ->get();

        return view('detalle', compact(
            'producto', 
            'imagenes', 
            'variaciones', 
            'coloresDisponibles', 
            'tallasDisponibles'
        ));
    }
}