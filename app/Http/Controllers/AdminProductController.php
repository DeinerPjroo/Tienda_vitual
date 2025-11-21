<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Prenda;
use App\Models\Categoria;
use App\Models\Variacion;
use App\Models\ImagenPrenda;

/**
 * Controlador para la gestión de productos en el panel de administración
 * 
 * Este controlador maneja todas las operaciones CRUD (Crear, Leer, Actualizar, Eliminar)
 * relacionadas con los productos de la tienda virtual.
 * 
 * @package App\Http\Controllers
 */
class AdminProductController extends Controller
{
    /**
     * Muestra la lista de productos con filtros y paginación
     * 
     * Permite filtrar productos por:
     * - Búsqueda de texto (nombre, SKU, descripción)
     * - Categoría
     * - Rango de precio
     * - Estado de stock (disponible, bajo, agotado)
     * 
     * @param Request $request - Contiene los parámetros de filtrado
     * @return \Illuminate\View\View - Vista con la lista de productos paginada
     */
    public function index(Request $request)
{
    // Construir la consulta base con joins para obtener:
    // - Datos del producto (prendas)
    // - Nombre de la categoría
    // - Primera imagen del producto (la de menor posición)
    // - Stock total sumando todas las variaciones
    $query = DB::table('prendas')
        ->leftJoin('categorias', 'prendas.categoria_id', '=', 'categorias.id')
        ->leftJoin('imagenes_prenda', function($join) {
            // Solo obtener la primera imagen (menor posición) de cada producto
            $join->on('prendas.id', '=', 'imagenes_prenda.prenda_id')
                 ->whereRaw('imagenes_prenda.posicion = (SELECT MIN(posicion) FROM imagenes_prenda WHERE prenda_id = prendas.id)');
        })
        ->select(
            'prendas.*',
            'categorias.nombre as categoria_nombre',
            'imagenes_prenda.url as imagen_url',
            // Calcular el stock total sumando el stock de todas las variaciones del producto
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

    // Ordenar por fecha de creación (más recientes primero)
    $query->orderBy('prendas.created_at', 'desc');

    // Paginar resultados (15 productos por página)
    // withQueryString() es importante: mantiene los parámetros de filtro en la URL al cambiar de página
    $productos = $query->paginate(15)->withQueryString();
    
    // Obtener todas las categorías para el selector de filtros
    $categorias = Categoria::all();

    return view('Admin.GestionDeProductos', compact('productos', 'categorias'));
}

    /**
     * Crea un nuevo producto en la base de datos
     * 
     * Proceso:
     * 1. Valida los datos del formulario
     * 2. Genera SKU automático si no se proporciona
     * 3. Crea el producto (prenda)
     * 4. Guarda la imagen si se subió una
     * 5. Crea una variación básica con stock, color y talla
     * 
     * Usa transacciones para asegurar que si algo falla, se reviertan todos los cambios.
     * 
     * @param Request $request - Datos del formulario (nombre, precio, imagen, etc.)
     * @return \Illuminate\Http\RedirectResponse - Redirige a la lista con mensaje de éxito/error
     */
    public function store(Request $request)
{
    // Validar los datos del formulario antes de procesarlos
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

    // Iniciar transacción: si algo falla, se revierten todos los cambios
    DB::beginTransaction();

    try {
        // Generar SKU automático si no se proporciona
        // Formato: PRD-XXXXXXXXXXXX (donde X es un ID único)
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

        // Guardar imagen si se subió una
        // Las imágenes se almacenan en: storage/app/public/productos/
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('productos', 'public');
            
            // Guardar la referencia de la imagen en la base de datos
            ImagenPrenda::create([
                'prenda_id' => $prenda->id,
                'url' => $path, // Ruta relativa: productos/nombre-archivo.jpg
                'texto_alternativo' => $request->nombre, // Para accesibilidad (alt text)
                'posicion' => 1 // Primera imagen del producto
            ]);
        }

        // Crear una variación básica del producto
        // Cada producto debe tener al menos una variación (color, talla, stock)
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

    /**
     * Obtiene los detalles completos de un producto
     * 
     * Este método se usa principalmente para:
     * - Mostrar el modal de detalles del producto
     * - Cargar datos al editar un producto
     * 
     * Retorna un JSON con el producto y todas sus relaciones:
     * - Categoría
     * - Variaciones (colores, tallas, stock)
     * - Imágenes
     * 
     * @param int $id - ID del producto
     * @return \Illuminate\Http\JsonResponse - JSON con los datos del producto
     */
    public function show($id)
{
    // Cargar el producto con todas sus relaciones usando eager loading
    $producto = Prenda::with(['categoria', 'variaciones', 'imagenes'])
        ->findOrFail($id);

    return response()->json([
        'success' => true,
        'producto' => $producto
    ]);
}

    /**
     * Actualiza un producto existente
     * 
     * Proceso:
     * 1. Valida los datos
     * 2. Actualiza los datos del producto
     * 3. Si se sube una nueva imagen, elimina la anterior y guarda la nueva
     * 4. Actualiza la variación si se proporcionan datos
     * 
     * @param Request $request - Datos actualizados del formulario
     * @param int $id - ID del producto a actualizar
     * @return \Illuminate\Http\RedirectResponse - Redirige a la lista con mensaje
     */
public function update(Request $request, $id)
{
    // Buscar el producto o lanzar error 404 si no existe
    $prenda = Prenda::findOrFail($id);

    // Validar los datos antes de actualizar
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
            // Buscar la imagen anterior (posición 1 = imagen principal)
            $imagenAnterior = ImagenPrenda::where('prenda_id', $prenda->id)
                ->where('posicion', 1)
                ->first();
            
            // Eliminar el archivo físico del storage si existe
            if ($imagenAnterior && Storage::disk('public')->exists($imagenAnterior->url)) {
                Storage::disk('public')->delete($imagenAnterior->url);
            }
            
            // Guardar la nueva imagen
            $path = $request->file('imagen')->store('productos', 'public');
            
            // Actualizar o crear el registro de la imagen en la BD
            ImagenPrenda::updateOrCreate(
                ['prenda_id' => $prenda->id, 'posicion' => 1],
                [
                    'url' => $path,
                    'texto_alternativo' => $request->nombre
                ]
            );
        }

        // Actualizar la variación básica si se proporcionaron datos
        // Solo actualiza si se enviaron campos relacionados con variaciones
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

    /**
     * Elimina un producto y todos sus datos relacionados
     * 
     * Proceso de eliminación:
     * 1. Elimina las imágenes físicas del storage
     * 2. Elimina los registros de imágenes de la BD
     * 3. Elimina las variaciones del producto
     * 4. Elimina el producto (prenda)
     * 
     * IMPORTANTE: Esta acción es irreversible. Se eliminan todos los datos relacionados.
     * 
     * @param int $id - ID del producto a eliminar
     * @return \Illuminate\Http\JsonResponse - JSON con el resultado de la operación
     */
public function destroy($id)
{
    try {
        $prenda = Prenda::findOrFail($id);
        
        // Iniciar transacción para asegurar integridad de datos
        DB::beginTransaction();
        
        // Eliminar todas las imágenes del producto
        // Primero eliminar los archivos físicos del storage
        $imagenes = ImagenPrenda::where('prenda_id', $id)->get();
        foreach ($imagenes as $imagen) {
            // Verificar que el archivo existe antes de intentar eliminarlo
            if (Storage::disk('public')->exists($imagen->url)) {
                Storage::disk('public')->delete($imagen->url);
            }
            // Eliminar el registro de la base de datos
            $imagen->delete();
        }
        
        // Eliminar todas las variaciones del producto
        // Las variaciones se eliminan en cascada, pero es mejor hacerlo explícitamente
        Variacion::where('prenda_id', $id)->delete();
        
        // Finalmente, eliminar el producto
        $prenda->delete();
        
        // Confirmar todos los cambios
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
