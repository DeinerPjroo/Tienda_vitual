<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile');
    }

    public function update(Request $request)
    {

    

        $user = Auth::user();

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
}