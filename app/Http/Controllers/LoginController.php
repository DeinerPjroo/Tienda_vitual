<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        Log::info('=== INICIO LOGIN ===');
        Log::info('Datos POST:', $request->only('email'));

        // Validación
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingresa un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        try {
            // Buscar usuario por correo
            $usuario = Usuario::where('correo', $request->email)->first();

            // Verificar si existe el usuario
            if (!$usuario) {
                Log::warning('Usuario no encontrado:', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Las credenciales no coinciden con nuestros registros.'
                ])->withInput($request->only('email'));
            }

            // Verificar si el usuario está activo
            if (!$usuario->activo) {
                Log::warning('Usuario inactivo:', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada. Contacta al soporte.'
                ])->withInput($request->only('email'));
            }

            // Verificar contraseña
            if (!Hash::check($request->password, $usuario->password)) {
                Log::warning('Contraseña incorrecta:', ['email' => $request->email]);
                return back()->withErrors([
                    'password' => 'La contraseña es incorrecta.'
                ])->withInput($request->only('email'));
            }

            // Login exitoso
            Log::info('Login exitoso:', ['user_id' => $usuario->id]);

            // Guardar en sesión
            Session::put('usuario_id', $usuario->id);
            Session::put('usuario_nombre', $usuario->nombre);
            Session::put('usuario_rol', $usuario->rol_id);
            Session::put('usuario_correo', $usuario->correo);

            // Manejar "Recuérdame" (opcional)
            if ($request->has('remember')) {
                // Aquí podrías implementar cookies de larga duración
                // Por ahora, Laravel maneja esto automáticamente con la sesión
            }

            // Redirigir según el rol
            if ($usuario->rol_id == 1) {
                // Admin
                return redirect()->route('dashboard')->with('success', '¡Bienvenido de vuelta, ' . $usuario->nombre . '!');
            } else {
                // Cliente normal
                return redirect()->route('home')->with('success', '¡Bienvenido de vuelta, ' . $usuario->nombre . '!');
            }

        } catch (\Exception $e) {
            Log::error('Error en login:', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
            ]);

            return back()->withErrors([
                'error' => 'Ocurrió un error al iniciar sesión. Por favor intenta de nuevo.'
            ])->withInput($request->only('email'));
        }
    }

    public function logout()
    {
        Log::info('Usuario cerrando sesión:', ['user_id' => Session::get('usuario_id')]);
        
        Session::flush(); // Elimina toda la sesión
        
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}