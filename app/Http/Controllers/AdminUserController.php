<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('rol');

        // Filtros
        if ($request->filled('rol')) {
            if ($request->rol == 'admin') {
                $query->where('rol_id', 1);
            } elseif ($request->rol == 'cliente') {
                $query->where('rol_id', 2);
            }
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado == 'activo' ? 1 : 0);
        }

        if ($request->filled('fecha')) {
            switch ($request->fecha) {
                case 'hoy':
                    $query->whereDate('created_at', today());
                    break;
                case 'semana':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'mes':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'año':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellido', 'LIKE', "%{$search}%")
                  ->orWhere('correo', 'LIKE', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $usuarios = $query->paginate(15);
        
        // Estadísticas
        $totalUsuarios = Usuario::count();
        $totalAdmins = Usuario::where('rol_id', 1)->count();
        
        // Roles para el formulario
        $roles = DB::table('roles')->get();

        return view('Admin.GestionClientes', compact('usuarios', 'totalUsuarios', 'totalAdmins', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo|max:150',
            'password' => 'required|string|min:8',
            'rol_id' => 'required|exists:roles,id',
            'telefono' => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'activo' => 'required|boolean'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'correo.required' => 'El correo es obligatorio',
            'correo.email' => 'El correo debe ser válido',
            'correo.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'rol_id.required' => 'El rol es obligatorio'
        ]);

        try {
            $data = [
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'rol_id' => $request->rol_id,
                'telefono' => $request->telefono,
                'activo' => $request->activo
            ];

            // Solo agregar fecha_nacimiento si tiene valor
            if ($request->filled('fecha_nacimiento')) {
                $data['fecha_nacimiento'] = $request->fecha_nacimiento;
            }

            Usuario::create($data);

            return redirect()->back()
                ->with('success', 'Usuario creado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear usuario: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $usuario = Usuario::with('rol')->findOrFail($id);

            return response()->json([
                'success' => true,
                'usuario' => $usuario
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'nullable|string|max:100',
            'correo' => 'required|email|max:150|unique:usuarios,correo,' . $id,
            'password' => 'nullable|string|min:8',
            'rol_id' => 'required|exists:roles,id',
            'telefono' => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'activo' => 'required|boolean'
        ]);

        try {
            $data = [
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'rol_id' => $request->rol_id,
                'telefono' => $request->telefono,
                'activo' => $request->activo
            ];

            // Solo actualizar contraseña si se proporciona
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Solo actualizar fecha_nacimiento si tiene valor
            if ($request->filled('fecha_nacimiento')) {
                $data['fecha_nacimiento'] = $request->fecha_nacimiento;
            } else {
                // Si está vacío, establecer como null
                $data['fecha_nacimiento'] = null;
            }

            $usuario->update($data);

            return redirect()->back()
                ->with('success', 'Usuario actualizado exitosamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar usuario: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            
            // No permitir eliminar el usuario actual
            if ($usuario->id == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ], 403);
            }

            $usuario->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario'
            ], 500);
        }
    }

    public function toggleActivo($id)
    {
        try {
            $usuario = Usuario::findOrFail($id);
            
            // No permitir desactivar el usuario actual
            if ($usuario->id == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes desactivar tu propio usuario'
                ], 403);
            }

            $usuario->update(['activo' => !$usuario->activo]);

            return response()->json([
                'success' => true,
                'activo' => $usuario->activo,
                'message' => 'Estado actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado'
            ], 500);
        }
    }
}