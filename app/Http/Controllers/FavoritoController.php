<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Favorito;
use App\Models\Prenda;

class FavoritoController extends Controller
{
    /**
     * Mostrar lista de favoritos del usuario
     */
    public function index()
    {
        $usuario = Auth::user();
        
        // Obtener productos favoritos con sus imágenes
        $favoritos = DB::table('favoritos')
            ->join('prendas', 'favoritos.prenda_id', '=', 'prendas.id')
            ->leftJoin('categorias', 'prendas.categoria_id', '=', 'categorias.id')
            ->leftJoin('imagenes_prenda', function($join) {
                $join->on('prendas.id', '=', 'imagenes_prenda.prenda_id')
                     ->whereRaw('imagenes_prenda.posicion = (SELECT MIN(posicion) FROM imagenes_prenda WHERE prenda_id = prendas.id)');
            })
            ->select(
                'prendas.*',
                'categorias.nombre as categoria_nombre',
                'imagenes_prenda.url as imagen_url',
                'favoritos.id as favorito_id',
                'favoritos.created_at as agregado_en',
                DB::raw('(SELECT SUM(stock) FROM variaciones WHERE prenda_id = prendas.id) as stock_total')
            )
            ->where('favoritos.usuario_id', $usuario->id)
            ->where('prendas.activo', 1)
            ->orderBy('favoritos.created_at', 'desc')
            ->paginate(12);

        return view('favoritos', compact('favoritos'));
    }

    /**
     * Agregar producto a favoritos
     */
    public function agregar(Request $request, $prendaId)
    {
        $usuario = Auth::user();
        
        // Verificar si ya existe
        $existe = Favorito::where('usuario_id', $usuario->id)
            ->where('prenda_id', $prendaId)
            ->first();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Este producto ya está en tus favoritos'
            ], 400);
        }

        // Verificar que el producto existe y está activo
        $prenda = Prenda::where('id', $prendaId)
            ->where('activo', 1)
            ->first();

        if (!$prenda) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Agregar a favoritos
        Favorito::create([
            'usuario_id' => $usuario->id,
            'prenda_id' => $prendaId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado a favoritos'
        ]);
    }

    /**
     * Eliminar producto de favoritos
     */
    public function eliminar(Request $request, $favoritoId)
    {
        $usuario = Auth::user();
        
        $favorito = Favorito::where('id', $favoritoId)
            ->where('usuario_id', $usuario->id)
            ->first();

        if (!$favorito) {
            return response()->json([
                'success' => false,
                'message' => 'Favorito no encontrado'
            ], 404);
        }

        $favorito->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado de favoritos'
        ]);
    }

    /**
     * Toggle favorito (agregar o quitar)
     */
    public function toggle(Request $request, $prendaId)
    {
        $usuario = Auth::user();
        
        $favorito = Favorito::where('usuario_id', $usuario->id)
            ->where('prenda_id', $prendaId)
            ->first();

        if ($favorito) {
            $favorito->delete();
            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'message' => 'Producto eliminado de favoritos'
            ]);
        } else {
            Favorito::create([
                'usuario_id' => $usuario->id,
                'prenda_id' => $prendaId
            ]);
            return response()->json([
                'success' => true,
                'is_favorite' => true,
                'message' => 'Producto agregado a favoritos'
            ]);
        }
    }

    /**
     * Verificar si un producto está en favoritos
     */
    public function verificar($prendaId)
    {
        if (!Auth::check()) {
            return response()->json(['is_favorite' => false]);
        }

        $usuario = Auth::user();
        $esFavorito = Favorito::where('usuario_id', $usuario->id)
            ->where('prenda_id', $prendaId)
            ->exists();

        return response()->json(['is_favorite' => $esFavorito]);
    }
}

