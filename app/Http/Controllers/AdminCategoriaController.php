<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;

class AdminCategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query();

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $categorias = $query->paginate(15);
        
        // Estadísticas
        $totalCategorias = Categoria::count();
        $categoriasActivas = Categoria::count(); // Todas están activas si no hay campo activo

        return view('Admin.GestionCategorias', compact('categorias', 'totalCategorias', 'categoriasActivas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Esta categoría ya existe',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
        ]);

        try {
            Categoria::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion
            ]);

            return redirect()->back()
                ->with('success', 'Categoría creada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear categoría: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);

            return response()->json([
                'success' => true,
                'categoria' => $categoria
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Categoría no encontrada'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $id,
            'descripcion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.unique' => 'Esta categoría ya existe',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
        ]);

        try {
            $categoria->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion
            ]);

            return redirect()->back()
                ->with('success', 'Categoría actualizada exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar categoría: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            
            // Verificar si tiene productos asociados
            if ($categoria->prendas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene productos asociados'
                ], 400);
            }

            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar categoría'
            ], 500);
        }
    }
}

