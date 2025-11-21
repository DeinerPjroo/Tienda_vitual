<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    /**
     * Mostrar productos de una categoría específica
     */
    public function mostrarCategoria(Request $request)
    {
        // Obtener el nombre de la categoría desde la ruta actual
        $nombreCategoria = basename($request->path());
        
        // Buscar la categoría por nombre (case insensitive)
        $categoria = Categoria::whereRaw('LOWER(nombre) = ?', [strtolower($nombreCategoria)])->first();
        
        if (!$categoria) {
            // Si no existe la categoría exacta, buscar por coincidencia parcial
            $categoria = Categoria::whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($nombreCategoria) . '%'])->first();
        }

        // Construir query base
        $query = DB::table('prendas')
            ->leftJoin('categorias', 'prendas.categoria_id', '=', 'categorias.id')
            ->leftJoin('imagenes_prenda', function($join) {
                $join->on('prendas.id', '=', 'imagenes_prenda.prenda_id')
                     ->whereRaw('imagenes_prenda.posicion = (SELECT MIN(posicion) FROM imagenes_prenda WHERE prenda_id = prendas.id)');
            })
            ->select(
                'prendas.*',
                'categorias.nombre as categoria_nombre',
                'imagenes_prenda.url as imagen_url',
                DB::raw('(SELECT SUM(stock) FROM variaciones WHERE prenda_id = prendas.id) as stock_total')
            )
            ->where('prendas.activo', 1);

        // Si se encontró la categoría, filtrar por ella
        if ($categoria) {
            $query->where('prendas.categoria_id', $categoria->id);
        } else {
            // Si no se encuentra, buscar por nombre de categoría en el nombre del producto o categoría
            $query->where(function($q) use ($nombreCategoria) {
                $q->whereRaw('LOWER(categorias.nombre) LIKE ?', ['%' . strtolower($nombreCategoria) . '%'])
                  ->orWhereRaw('LOWER(prendas.nombre) LIKE ?', ['%' . strtolower($nombreCategoria) . '%']);
            });
        }

        // Filtros adicionales
        if ($request->filled('orden')) {
            switch ($request->orden) {
                case 'precio_asc':
                    $query->orderBy('prendas.precio', 'asc');
                    break;
                case 'precio_desc':
                    $query->orderBy('prendas.precio', 'desc');
                    break;
                case 'nuevos':
                    $query->orderBy('prendas.created_at', 'desc');
                    break;
                default:
                    $query->orderBy('prendas.created_at', 'desc');
            }
        } else {
            $query->orderBy('prendas.created_at', 'desc');
        }

        $productos = $query->paginate(12)->withQueryString();
        
        // Obtener todas las categorías para los filtros
        $categorias = Categoria::all();

        // Títulos según la categoría
        $titulos = [
            'mujer' => 'Moda para Mujer',
            'hombre' => 'Moda para Hombre',
            'ninos' => 'Moda para Niños',
            'niños' => 'Moda para Niños',
            'accesorios' => 'Accesorios que marcan estilo'
        ];

        $titulo = $titulos[strtolower($nombreCategoria)] ?? 'Productos';
        
        $descripciones = [
            'mujer' => 'Descubre las últimas tendencias en ropa femenina. Vestidos elegantes, blusas modernas y más con increíbles descuentos.',
            'hombre' => 'Descubre las últimas tendencias en ropa masculina. Camisas elegantes, chaquetas modernas y mucho más con increíbles descuentos.',
            'ninos' => 'Ropa divertida, cómoda y colorida para los más pequeños. Encuentra conjuntos adorables, camisetas, zapatos y más con descuentos especiales.',
            'niños' => 'Ropa divertida, cómoda y colorida para los más pequeños. Encuentra conjuntos adorables, camisetas, zapatos y más con descuentos especiales.',
            'accesorios' => 'Descubre relojes, gafas, bolsos y mucho más. Diseños que complementan tu outfit con elegancia, innovación y detalles únicos.'
        ];

        $descripcion = $descripciones[strtolower($nombreCategoria)] ?? 'Explora nuestra colección de productos.';

        return view('categoria', compact('productos', 'categorias', 'categoria', 'titulo', 'descripcion', 'nombreCategoria'));
    }
}

