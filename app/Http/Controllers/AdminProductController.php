<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Prenda;
use App\Models\Categoria;
use App\Models\Variacion;
use App\Models\ImagenPrenda;

class AdminProductController extends Controller
{
    public function index(Request $request)
{
    // Obtener productos con sus relaciones
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
        );

    // Filtro de búsqueda
    if ($request->filled('buscar')) {
        $search = $request->buscar;
        $query->where(function($q) use ($search) {
            $q->where('prendas.nombre', 'LIKE', "%{$search}%")
              ->orWhere('prendas.sku', 'LIKE', "%{$search}%")
              ->orWhere('prendas.descripcion', 'LIKE', "%{$search}%");
        });
    }

    // Filtro de categoría
    if ($request->filled('categoria')) {
        $query->where('prendas.categoria_id', $request->categoria);
    }

    // Filtro de precio
    if ($request->filled('precio')) {
        $precio = $request->precio;
        
        if ($precio == '0-50000') {
            $query->whereBetween('prendas.precio', [0, 50000]);
        } elseif ($precio == '50000-100000') {
            $query->whereBetween('prendas.precio', [50000, 100000]);
        } elseif ($precio == '100000-200000') {
            $query->whereBetween('prendas.precio', [100000, 200000]);
        } elseif ($precio == '200000+') {
            $query->where('prendas.precio', '>=', 200000);
        }
    }

    // Filtro de stock
    if ($request->filled('stock')) {
        $stock = $request->stock;
        
        if ($stock == 'disponible') {
            $query->havingRaw('stock_total > 10');
        } elseif ($stock == 'bajo') {
            $query->havingRaw('stock_total BETWEEN 1 AND 10');
        } elseif ($stock == 'agotado') {
            $query->havingRaw('stock_total = 0 OR stock_total IS NULL');
        }
    }

    $query->orderBy('prendas.created_at', 'desc');

    $productos = $query->paginate(15)->withQueryString(); // Importante: withQueryString() mantiene los filtros en la paginación
    $categorias = Categoria::all();

    return view('Admin.GestionDeProductos', compact('productos', 'categorias'));
}

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:200',
        'descripcion_corta' => 'nullable|string|max:255',
        'descripcion' => 'nullable|string',
        'precio' => 'required|numeric|min:0',
        'descuento' => 'nullable|numeric|min:0|max:100',
        'categoria_id' => 'required|exists:categorias,id',
        'sku' => 'nullable|string|unique:prendas,sku|max:100',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        'stock' => 'nullable|numeric|min:0',
        'color' => 'nullable|string|max:50',
        'talla' => 'nullable|string|max:20',
    ]);

    DB::beginTransaction();

    try {
        // Generar SKU automático si no se proporciona
        $sku = $request->sku ?? 'PRD-' . strtoupper(uniqid());

        // Crear la prenda
        $prenda = Prenda::create([
            'categoria_id' => $request->categoria_id,
            'sku' => $sku,
            'nombre' => $request->nombre,
            'descripcion_corta' => $request->descripcion_corta,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'descuento' => $request->descuento ?? 0,
            'activo' => 1
        ]);

        // Guardar imagen si existe
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            
            ImagenPrenda::create([
                'prenda_id' => $prenda->id,
                'url' => $path,
                'texto_alternativo' => $request->nombre,
                'posicion' => 1
            ]);
        }

        // Crear variación básica
        Variacion::create([
            'prenda_id' => $prenda->id,
            'sku' => $sku . '-' . ($request->talla ?? 'UNICA'),
            'color' => $request->color ?? 'Sin especificar',
            'talla' => $request->talla ?? 'Única',
            'stock' => $request->stock ?? 0,
            'precio_adicional' => 0
        ]);

        DB::commit();

        return redirect()->route('gestion.productos')
            ->with('success', 'Producto creado exitosamente');

    } catch (\Exception $e) {
        DB::rollBack();
        
        \Log::error('Error al crear producto: ' . $e->getMessage());
        
        return back()->withErrors(['error' => 'Error al crear producto: ' . $e->getMessage()])
            ->withInput();
    }
}

    public function show($id)
{
    $producto = Prenda::with(['categoria', 'variaciones', 'imagenes'])
        ->findOrFail($id);

    return response()->json([
        'success' => true,
        'producto' => $producto
    ]);
}

public function update(Request $request, $id)
{
    $prenda = Prenda::findOrFail($id);

    $request->validate([
        'nombre' => 'required|string|max:200',
        'descripcion_corta' => 'nullable|string|max:255',
        'descripcion' => 'nullable|string',
        'precio' => 'required|numeric|min:0',
        'descuento' => 'nullable|numeric|min:0|max:100',
        'categoria_id' => 'required|exists:categorias,id',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    DB::beginTransaction();

    try {
        $prenda->update([
            'nombre' => $request->nombre,
            'descripcion_corta' => $request->descripcion_corta,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'descuento' => $request->descuento ?? 0,
            'categoria_id' => $request->categoria_id,
        ]);

        // Actualizar imagen si se subió una nueva
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            $imagenAnterior = ImagenPrenda::where('prenda_id', $prenda->id)
                ->where('posicion', 1)
                ->first();
            
            if ($imagenAnterior && Storage::disk('public')->exists($imagenAnterior->url)) {
                Storage::disk('public')->delete($imagenAnterior->url);
            }
            
            $path = $request->file('imagen')->store('productos', 'public');
            
            ImagenPrenda::updateOrCreate(
                ['prenda_id' => $prenda->id, 'posicion' => 1],
                [
                    'url' => $path,
                    'texto_alternativo' => $request->nombre
                ]
            );
        }

        // Actualizar variación si existe
        if ($request->filled('stock') || $request->filled('color') || $request->filled('talla')) {
            $variacion = Variacion::where('prenda_id', $prenda->id)->first();
            
            if ($variacion) {
                $variacion->update([
                    'stock' => $request->stock ?? $variacion->stock,
                    'color' => $request->color ?? $variacion->color,
                    'talla' => $request->talla ?? $variacion->talla,
                ]);
            }
        }

        DB::commit();

        return redirect()->route('gestion.productos')
            ->with('success', 'Producto actualizado exitosamente');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al actualizar: ' . $e->getMessage())
            ->withInput();
    }
}

public function destroy($id)
{
    try {
        $prenda = Prenda::findOrFail($id);
        
        DB::beginTransaction();
        
        // Eliminar imágenes del storage
        $imagenes = ImagenPrenda::where('prenda_id', $id)->get();
        foreach ($imagenes as $imagen) {
            if (Storage::disk('public')->exists($imagen->url)) {
                Storage::disk('public')->delete($imagen->url);
            }
            $imagen->delete();
        }
        
        // Eliminar variaciones
        Variacion::where('prenda_id', $id)->delete();
        
        // Eliminar prenda
        $prenda->delete();
        
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar producto: ' . $e->getMessage()
        ], 500);
    }
}
}
