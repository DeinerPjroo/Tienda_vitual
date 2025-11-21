<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

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

            // Verificar si es un usuario que se registró con Google
            // Los usuarios de Google tienen un password especial que nunca coincidirá
            if ($this->esUsuarioGoogle($usuario)) {
                Log::warning('Usuario intenta login normal pero se registró con Google:', ['email' => $request->email]);
                return back()->withErrors([
                    'email' => 'Esta cuenta se registró con Google. Por favor, usa el botón "Iniciar sesión con Google".'
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

            // Redirigir según el rol y preferencia de vista
            if ($usuario->rol_id == 1) {
                // Administrador → Verificar preferencia de vista
                $vistaPreferida = session('vista_preferida', 'admin');
                if ($vistaPreferida === 'usuario') {
                    return redirect()->route('home')->with('success', '¡Bienvenido de vuelta, ' . $usuario->nombre . '!');
                }
                return redirect()->route('homeadmin')->with('success', '¡Bienvenido de vuelta, ' . $usuario->nombre . '!');
            } else {
                // Cliente → Home normal
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

    /**
     * Redirige al usuario a Google para autenticación OAuth
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Maneja el callback de Google después de la autenticación
     * 
     * Proceso:
     * 1. Obtiene los datos del usuario de Google
     * 2. Busca si existe un usuario con ese correo
     * 3. Si no existe, crea uno nuevo (rol Cliente por defecto)
     * 4. Si existe, verifica que sea un usuario de Google (no manual)
     * 5. Autentica al usuario y redirige según su rol
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            // Obtener datos del usuario de Google
            $googleUser = Socialite::driver('google')->user();
            
            Log::info('Callback de Google recibido:', ['email' => $googleUser->getEmail()]);

            // Buscar usuario existente por correo
            $usuario = Usuario::where('correo', $googleUser->getEmail())->first();

            if (!$usuario) {
                // Crear nuevo usuario desde Google
                // Separar nombre y apellido del nombre completo
                $nombreCompleto = $googleUser->getName() ?? $googleUser->getNickname() ?? 'Usuario';
                $partesNombre = explode(' ', $nombreCompleto, 2);
                
                $usuario = Usuario::create([
                    'rol_id' => 2, // Cliente por defecto
                    'nombre' => $partesNombre[0],
                    'apellido' => $partesNombre[1] ?? null,
                    'correo' => $googleUser->getEmail(),
                    // Password especial que identifica usuarios de Google
                    // Usamos el email como identificador para poder verificar después
                    // Este password nunca coincidirá con un login normal
                    'password' => Hash::make('GOOGLE_AUTH_ONLY_' . $googleUser->getEmail()),
                    'telefono' => null,
                    'fecha_nacimiento' => now()->subYears(18), // Fecha por defecto
                    'activo' => true,
                ]);

                Log::info('Usuario creado desde Google:', ['user_id' => $usuario->id]);
            } else {
                // Usuario ya existe - verificar que no sea un usuario manual
                if (!$this->esUsuarioGoogle($usuario)) {
                    Log::warning('Usuario manual intenta login con Google:', ['email' => $googleUser->getEmail()]);
                    return redirect()->route('login')->withErrors([
                        'email' => 'Esta cuenta se registró con email y contraseña. Por favor, usa el formulario de inicio de sesión normal.'
                    ]);
                }

                // Actualizar password si es necesario (usamos el email como identificador único)
                $usuario->password = Hash::make('GOOGLE_AUTH_ONLY_' . $usuario->correo);
                $usuario->save();

                Log::info('Usuario de Google autenticado:', ['user_id' => $usuario->id]);
            }

            // Verificar que el usuario esté activo
            if (!$usuario->activo) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada. Contacta al soporte.'
                ]);
            }

            // Autenticar al usuario
            Auth::login($usuario, true);
            request()->session()->regenerate();

            Log::info('Login con Google exitoso:', ['user_id' => $usuario->id]);

            // Redirigir según el rol (igual que el login normal)
            if ($usuario->rol_id == 1) {
                // Administrador
                $vistaPreferida = session('vista_preferida', 'admin');
                if ($vistaPreferida === 'usuario') {
                    return redirect()->route('home')->with('success', '¡Bienvenido, ' . $usuario->nombre . '!');
                }
                return redirect()->route('homeadmin')->with('success', '¡Bienvenido, ' . $usuario->nombre . '!');
            } else {
                // Cliente
                return redirect()->route('home')->with('success', '¡Bienvenido, ' . $usuario->nombre . '!');
            }

        } catch (\Exception $e) {
            Log::error('Error en callback de Google:', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
            ]);

            return redirect()->route('login')->withErrors([
                'error' => 'Ocurrió un error al autenticar con Google. Por favor intenta de nuevo.'
            ]);
        }
    }

    /**
     * Verifica si un usuario se registró con Google
     * 
     * Los usuarios de Google tienen un password que es un hash de 'GOOGLE_AUTH_ONLY_*'
     * Para detectarlo, verificamos si el password coincide con el patrón conocido
     * 
     * @param Usuario $usuario
     * @return bool
     */
    private function esUsuarioGoogle(Usuario $usuario): bool
    {
        // Verificar si el password coincide con el patrón de usuarios de Google
        // Los usuarios de Google tienen un password que es hash de 'GOOGLE_AUTH_ONLY_' + algo
        // Verificamos con un password de prueba que sabemos que es de Google
        // Si coincide, es usuario de Google
        
        // Usamos un password de prueba que sabemos que es de Google
        // Si el hash del usuario coincide con este patrón, es usuario de Google
        $passwordPruebaGoogle = 'GOOGLE_AUTH_ONLY_VERIFY';
        
        // Si el password del usuario es un hash de cualquier string que empieza con 'GOOGLE_AUTH_ONLY_',
        // entonces es usuario de Google. Verificamos con el password de prueba
        // Nota: Esto no es perfecto, pero funciona porque los usuarios de Google siempre
        // tendrán un password que es hash de 'GOOGLE_AUTH_ONLY_' + ID de Google
        
        // Mejor solución: verificar si el password hash tiene un formato que indica Google
        // Como los hashes bcrypt son únicos cada vez, necesitamos otra estrategia
        
        // Solución práctica: verificar si el password NO puede ser un password normal
        // Intentamos verificar con el password de prueba de Google
        // Si el password del usuario fue creado con el patrón de Google, lo detectaremos
        
        // Estrategia final: como no podemos verificar directamente sin el ID de Google,
        // usamos una marca especial. Guardamos el password como hash de 'GOOGLE_AUTH_ONLY_' + ID
        // Para verificar, intentamos con el password de prueba
        // Si no coincide, intentamos verificar si es un password normal (que nunca coincidirá)
        
        // La mejor forma: verificar si el password hash tiene características especiales
        // Pero como no podemos hacerlo directamente, usamos esta solución:
        // Intentamos verificar con el password de prueba de Google
        // Si el password del usuario fue creado con el patrón, lo detectaremos
        
        try {
            // Verificar si el password coincide con el patrón de Google
            // Como no tenemos el ID de Google original, usamos una verificación indirecta:
            // Si el password NO coincide con ningún password normal conocido,
            // y tiene el formato de un hash de Google, entonces es usuario de Google
            
            // Solución más simple: verificar si el password hash tiene un formato específico
            // Los hashes de bcrypt siempre tienen 60 caracteres y empiezan con $2y$
            // Pero no podemos distinguir entre un hash de Google y uno normal
            
            // Solución práctica: usar una constante para usuarios de Google
            // Guardamos el password como hash de una constante + ID de Google
            // Para verificar, intentamos con la constante base
            
            // Verificar con el password base de Google (sin ID específico)
            // Si el password del usuario fue creado con 'GOOGLE_AUTH_ONLY_' + ID,
            // no coincidirá exactamente, pero podemos usar otra estrategia
            
            // Mejor solución: verificar si el password es un hash de un string que empieza con 'GOOGLE_AUTH_ONLY_'
            // Como no podemos hacerlo directamente, usamos una marca especial en el password
            
            // Solución final más práctica: 
            // Guardar el password como hash de 'GOOGLE_AUTH_ONLY_' + email del usuario
            // Así podemos verificar fácilmente
            $passwordGoogleVerificacion = 'GOOGLE_AUTH_ONLY_' . $usuario->correo;
            
            // Si el password coincide con este patrón, es usuario de Google
            return Hash::check($passwordGoogleVerificacion, $usuario->password);
            
        } catch (\Exception $e) {
            Log::error('Error verificando si es usuario de Google:', [
                'usuario_id' => $usuario->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}