<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener productos con sus imágenes y categorías
        $productos = DB::table('prendas')
            ->leftJoin('categorias', 'prendas.categoria_id', '=', 'categorias.id')
            ->leftJoin('imagenes_prenda', function($join) {
                $join->on('prendas.id', '=', 'imagenes_prenda.prenda_id')
                     ->where('imagenes_prenda.posicion', '=', 1); // Primera imagen
            })
            ->select(
                'prendas.*',
                'categorias.nombre as categoria_nombre',
                'imagenes_prenda.url as imagen_url'
            )
            ->where('prendas.activo', 1)
            ->orderBy('prendas.created_at', 'desc')
            ->get();

        return view('home', compact('productos'));
    }
}