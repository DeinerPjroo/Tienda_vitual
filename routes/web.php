<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register');
})->name('register');


Route::get('/home', function () {
    return view('home');
})->name('home');

// Ruta para la vista de Mujer
Route::get('/mujer', function () {
    return view('mujer');
})->name('mujer');


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
}




);

// Redirección a Google para iniciar sesión
Route::get('/login-google', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

// Callback de Google: obtener información del usuario, crear/obtener usuario local y autenticación
Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    // Buscar por email
    $user = \App\Models\User::where('email', $googleUser->getEmail())->first();

    if (! $user) {
        $user = \App\Models\User::create([
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Usuario',
            'email' => $googleUser->getEmail(),
            // contraseña aleatoria (se almacenará hasheada gracias al cast en el modelo)
            'password' => \Illuminate\Support\Str::random(24),
        ]);
    }

    \Illuminate\Support\Facades\Auth::login($user, true);

    return redirect()->intended(route('dashboard'));
})->name('login.google.callback');



use App\Http\Controllers\RegisterController;

Route::get('/register', [RegisterController::class, 'showForm'])->name('register.form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
