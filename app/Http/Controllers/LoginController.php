<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingresa un correo electrónico válido',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        try {
            $usuario = Usuario::where('correo', $request->email)->first();

            if (!$usuario) {
                Log::warning('Usuario no encontrado:', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Las credenciales no coinciden con nuestros registros.'
                ])->withInput($request->only('email'));
            }

            if (!$usuario->activo) {
                Log::warning('Usuario inactivo:', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada. Contacta al soporte.'
                ])->withInput($request->only('email'));
            }

            if (!Hash::check($request->password, $usuario->password)) {
                Log::warning('Contraseña incorrecta:', ['email' => $request->email]);
                return back()->withErrors([
                    'password' => 'La contraseña es incorrecta.'
                ])->withInput($request->only('email'));
            }

            // 🔥 AQUÍ ESTÁ EL CAMBIO IMPORTANTE
            // Autenticar con Auth de Laravel
            Auth::login($usuario, $request->filled('remember'));
            $request->session()->regenerate();

            Log::info('Login exitoso:', ['user_id' => $usuario->id]);

            // Redirigir según el rol
            if ($usuario->rol_id == 1) {
                return redirect()->route('dashboard')->with('success', '¡Bienvenido de vuelta, ' . $usuario->nombre . '!');
            } else {
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

    public function logout(Request $request)
    {
        Log::info('Usuario cerrando sesión:', ['user_id' => Auth::id()]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'Has cerrado sesión correctamente.');
    }
}