<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Direccion;
use App\Models\Pedido;
use App\Models\Usuario;

class ProfileController extends Controller
{
    /**
     * Mostrar perfil del usuario
     */
    public function show()
    {
        $user = Usuario::find(Auth::id());
        
        // Si es administrador, mostrar perfil de admin
        if ($user->rol_id == 1) {
            return view('Admin.profile-admin');
        }
        
        // Si es cliente, mostrar perfil normal
        return view('profile');
    }

    /**
     * Cambiar vista preferida (admin o usuario)
     */
    public function cambiarVista(Request $request)
    {
        $request->validate([
            'vista' => 'required|in:admin,usuario'
        ]);

        // Guardar preferencia en sesión
        session(['vista_preferida' => $request->vista]);

        // Redirigir según la vista seleccionada
        if ($request->vista === 'admin') {
            return redirect()->route('homeadmin')->with('success', 'Vista de administrador activada');
        } else {
            return redirect()->route('home')->with('success', 'Vista de usuario activada');
        }
    }

    /**
     * Actualizar información del perfil
     */
    public function update(Request $request)
    {
        // Obtener el usuario autenticado como Usuario (no User)
        $user = Usuario::find(Auth::id());

        // Validación
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => [
                'required',
                'email',
                'max:150',
                Rule::unique('usuarios')->ignore($user->id)
            ],
            'telefono' => 'nullable|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'correo.required' => 'El correo electrónico es obligatorio',
            'correo.email' => 'Ingresa un correo electrónico válido',
            'correo.unique' => 'Este correo ya está registrado',
            'fecha_nacimiento.date' => 'Ingresa una fecha válida',
        ]);

        // Actualizar los datos
        $user->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'fecha_nacimiento' => $request->fecha_nacimiento,
        ]);

        return back()->with('success', '¡Perfil actualizado correctamente!');
    }

    /**
     * Mostrar historial de pedidos
     */
    public function pedidos()
    {
        // Obtener el usuario como Usuario, no como User
        $user = Usuario::find(Auth::id());
        
        $pedidos = $user->pedidos()
            ->with(['items.variacion.prenda.imagenes', 'direccion'])
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(10);

        return view('pedidos', compact('pedidos'));
    }

    /**
     * Ver detalle de un pedido específico
     */
    public function pedidoDetalle($id)
    {
        $user = Usuario::find(Auth::id());
        
        $pedido = $user->pedidos()
            ->with(['items.variacion.prenda.imagenes', 'direccion', 'pagos'])
            ->findOrFail($id);

        return view('pedido-detalle', compact('pedido'));
    }

    /**
     * Mostrar direcciones del usuario
     */
    public function direcciones()
    {
        $user = Usuario::find(Auth::id());
        $direcciones = $user->direcciones()->get();
        
        return view('direcciones', compact('direcciones'));
    }

    /**
     * Guardar nueva dirección o actualizar existente
     */
    public function guardarDireccion(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:200',
            'telefono' => 'required|string|max:30',
            'direccion_linea1' => 'required|string|max:255',
            'direccion_linea2' => 'nullable|string|max:255',
            'ciudad' => 'required|string|max:100',
            'departamento' => 'required|string|max:100',
            'codigo_postal' => 'required|string|max:20',
            'pais' => 'required|string|max:100',
            'predeterminada' => 'nullable|boolean',
        ]);

        $user = Usuario::find(Auth::id());

        // Si se marca como predeterminada, quitar la marca de las demás
        if ($request->predeterminada) {
            $user->direcciones()->update(['predeterminada' => 0]);
        }

        // Si existe ID, actualizar; si no, crear nueva
        if ($request->id) {
            $direccion = $user->direcciones()->findOrFail($request->id);
            $direccion->update($request->all());
            $mensaje = 'Dirección actualizada correctamente';
        } else {
            $user->direcciones()->create($request->all());
            $mensaje = 'Dirección agregada correctamente';
        }

        return redirect()->route('direcciones')->with('success', $mensaje);
    }

    /**
     * Eliminar una dirección
     */
    public function eliminarDireccion($id)
    {
        $user = Usuario::find(Auth::id());
        $direccion = $user->direcciones()->findOrFail($id);
        $direccion->delete();

        return back()->with('success', 'Dirección eliminada correctamente');
    }

    /**
     * Establecer dirección como predeterminada
     */
    public function predeterminarDireccion($id)
    {
        $user = Usuario::find(Auth::id());

        // Quitar predeterminada de todas
        $user->direcciones()->update(['predeterminada' => 0]);

        // Establecer la seleccionada como predeterminada
        $direccion = $user->direcciones()->findOrFail($id);
        $direccion->update(['predeterminada' => 1]);

        return back()->with('success', 'Dirección predeterminada actualizada');
    }

    /**
     * Obtener una dirección en formato JSON (para editar en modal)
     */
    public function obtenerDireccion($id)
    {
        $user = Usuario::find(Auth::id());
        $direccion = $user->direcciones()->findOrFail($id);
        
        return response()->json($direccion);
    }

    /**
     * Reordenar pedido (volver a comprar)
     */
    public function reordenarPedido($id)
    {
        $user = Usuario::find(Auth::id());
        $pedido = $user->pedidos()->with('items.variacion')->findOrFail($id);
        
        // Aquí agregarías la lógica para agregar todos los items del pedido al carrito
        // Por ahora solo redirigimos
        
        return redirect()->route('carrito.index')
            ->with('success', 'Los productos han sido añadidos al carrito');
    }
}