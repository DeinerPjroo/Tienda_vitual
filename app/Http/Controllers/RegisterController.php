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
        return view('register'); // tu vista register.blade.php
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuarios,correo',
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:30',
            'fecha_nacimiento' => 'required|date',
        ]);

        try {
            $usuario = Usuario::create([
                'rol_id' => 2, // usuario normal
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'correo' => $request->correo,
                'password' => Hash::make($request->password),
                'telefono' => $request->telefono,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'activo' => 1,
            ]);

            // Guardar datos en sesión si quieres
            Session::put('usuario_id', $usuario->id);
            Session::put('usuario_nombre', $usuario->nombre);

            return redirect()->route('home')->with('success', 'Usuario registrado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al registrar usuario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar el usuario.'])->withInput();
        }
    }
}
