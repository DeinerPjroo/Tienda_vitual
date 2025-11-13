<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');




Route::get('/home', function () {
    return view('home');
})->name('home');


// RUTAS DE LOGIN
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// RUTAS DE REGISTRO
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// Dashboard (solo para usuarios autenticados)
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
});

// Google OAuth
Route::get('/login-google', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    $user = \App\Models\User::where('email', $googleUser->getEmail())->first();

    if (! $user) {
        $user = \App\Models\User::create([
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Usuario',
            'email' => $googleUser->getEmail(),
            'password' => \Illuminate\Support\Str::random(24),
        ]);
    }

    \Illuminate\Support\Facades\Auth::login($user, true);

    return redirect()->intended(route('dashboard'));
})->name('login.google.callback');