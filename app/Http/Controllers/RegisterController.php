<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('register');
    }

    public function register(Request $request)
{
    Log::info('=== INICIO REGISTRO ===');
    Log::info('Datos POST:', $request->all());
    
    try {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:30',
            'fecha_nacimiento' => [
                'required',
                'date',
                'before:today',
                'before_or_equal:' . now()->subYears(16)->format('Y-m-d'), // Máximo hace 16 años
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'correo.required' => 'El correo electrónico es obligatorio',
            'correo.email' => 'El correo electrónico debe ser válido',
            'correo.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.date' => 'Ingresa una fecha válida',
            'fecha_nacimiento.before' => 'Debes haber nacido antes de hoy',
            'fecha_nacimiento.before_or_equal' => 'Debes tener al menos 16 años para registrarte',
        ]);
        
        Log::info('Validación exitosa:', $validated);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Error de validación:', $e->errors());
        return back()->withErrors($e->errors())->withInput();
    }

    try {
        Log::info('Intentando crear usuario...');
        
        $usuario = Usuario::create([
            'rol_id' => 2,
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'correo' => $validated['correo'],
            'password' => Hash::make($validated['password']),
            'telefono' => $validated['telefono'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'activo' => 1,
        ]);

        Log::info('Usuario creado exitosamente', ['id' => $usuario->id]);

        Session::put('usuario_id', $usuario->id);
        Session::put('usuario_nombre', $usuario->nombre);

        return redirect()->route('login')->with('success', 'Usuario registrado correctamente.');

    } catch (\Exception $e) {
        Log::error('ERROR AL CREAR USUARIO:', [
            'mensaje' => $e->getMessage(),
            'archivo' => $e->getFile(),
            'linea' => $e->getLine(),
        ]);
        
        return back()->withErrors(['error' => 'Error al crear usuario: ' . $e->getMessage()])->withInput();
    }
}
}