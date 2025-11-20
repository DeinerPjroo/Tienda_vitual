<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Laravel\Socialite\Facades\Socialite;

// ============================================
// RUTAS PÚBLICAS
// ============================================

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/gestion-clientes', function () {
    return view('Admin.GestionClientes');
})->name('gestion.clientes');

Route::get('/homeadmin', function () {
    return view('admin.homeadmin');
})->name('homeadmin');

Route::get('/gestion-productos', function () {
    return view('Admin.GestionDeProductos');
})->name('gestion.productos');

Route::get('/favoritos', function () {
    return view('favoritos');
})->name('favoritos');

Route::get('/hombre', function () {
    return view('hombre');
})->name('hombre');

Route::get('/ninos', function () {
    return view('ninos');
})->name('ninos');

Route::get('/accesorios', function () {
    return view('accesorios');
})->name('accesorios');

Route::get('/mujer', function () {
    return view('mujer');
})->name('mujer');

Route::get('/product', function () {
    return view('product');
})->name('product');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');

Route::get('/home', [HomeController::class, 'index'])->name('home');

// ============================================
// RUTAS DE AUTENTICACIÓN
// ============================================

// Login
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registro
Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

// Google OAuth
Route::get('/login-google', function () {
    return Socialite::driver('google')->redirect();
})->name('login.google');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->user();

    $user = \App\Models\User::where('email', $googleUser->getEmail())->first();

    if (!$user) {
        $user = \App\Models\User::create([
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Usuario',
            'email' => $googleUser->getEmail(),
            'password' => \Illuminate\Support\Str::random(24),
        ]);
    }

    \Illuminate\Support\Facades\Auth::login($user, true);

    return redirect()->intended(route('dashboard'));
})->name('login.google.callback');

// ============================================
// RUTAS PROTEGIDAS (Requieren Autenticación)
// ============================================

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::view('dashboard', 'dashboard')->name('dashboard');

    // ============================================
    // PERFIL DE USUARIO
    // ============================================
    
    // Ver y actualizar perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Alias adicional para /perfil (español)
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil');

    // ============================================
    // HISTORIAL DE PEDIDOS
    // ============================================
    
    // Lista de pedidos del usuario
    Route::get('/mis-pedidos', [ProfileController::class, 'pedidos'])->name('pedidos');
    
    // Detalle de un pedido específico
    Route::get('/pedido/{id}', [ProfileController::class, 'pedidoDetalle'])->name('pedido.detalle');
    
    // Volver a comprar (duplicar pedido)
    Route::post('/pedido/{id}/reordenar', [ProfileController::class, 'reordenarPedido'])->name('pedido.reordenar');

    // ============================================
    // DIRECCIONES
    // ============================================
    
    // Lista de direcciones del usuario
    Route::get('/direcciones', [ProfileController::class, 'direcciones'])->name('direcciones');
    
    // Guardar nueva dirección o actualizar existente
    Route::post('/direccion/guardar', [ProfileController::class, 'guardarDireccion'])->name('direccion.guardar');
    
    // Eliminar dirección
    Route::delete('/direccion/{id}', [ProfileController::class, 'eliminarDireccion'])->name('direccion.eliminar');
    
    // Establecer dirección como predeterminada
    Route::post('/direccion/{id}/predeterminar', [ProfileController::class, 'predeterminarDireccion'])->name('direccion.predeterminar');
    
    // Obtener dirección por ID (para editar en modal - AJAX)
    Route::get('/direccion/{id}/json', [ProfileController::class, 'obtenerDireccion'])->name('direccion.obtener');

    // ============================================
    // CARRITO DE COMPRAS
    // ============================================
    
    Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
    Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::put('/carrito/item/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');
    Route::delete('/carrito/item/{id}', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');

    // ============================================
    // CONFIGURACIONES ADICIONALES
    // ============================================
    
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

// ============================================
// RUTAS DE PRODUCTOS (Públicas)
// ============================================

// Búsqueda de productos
Route::get('/buscar', [ProductController::class, 'buscar'])->name('productos.buscar');

// Detalle de producto
Route::get('/producto/{id}', [ProductController::class, 'detalle'])->name('producto.detalle');


// Rutas de Checkout y Pedidos
Route::middleware(['auth'])->group(function () {
    // Checkout
    Route::get('/checkout', [PedidoController::class, 'checkout'])->name('checkout');
    Route::post('/pedido/crear', [PedidoController::class, 'crear'])->name('pedido.crear');
    
    // Pedidos
    Route::get('/pedido/confirmacion/{id}', [PedidoController::class, 'confirmacion'])->name('pedido.confirmacion');
    Route::get('/pedido/{id}', [PedidoController::class, 'detalle'])->name('pedido.detalle');
    Route::post('/pedido/{id}/cancelar', [PedidoController::class, 'cancelar'])->name('pedido.cancelar');
    Route::get('/pedido/{id}/rastrear', [PedidoController::class, 'rastrear'])->name('pedido.rastrear');
});
// Detalles de pedidos
Route::get('/pedido/{id}/detalle', [PedidoController::class, 'detalle'])->name('pedido.detalle');
Route::patch('/pedido/{id}/cancelar', [PedidoController::class, 'cancelar'])->name('pedido.cancelar');
Route::post('/pedido/{id}/reordenar', [PedidoController::class, 'reordenar'])->name('pedido.reordenar');
Route::post('/pedido/{id}/devolucion', [PedidoController::class, 'devolucion'])->name('pedido.devolucion');
Route::get('/pedido/{id}/rastrear', [PedidoController::class, 'rastrear'])->name('pedido.rastrear');
Route::get('/pedido/{id}/factura', [PedidoController::class, 'factura'])->name('pedido.factura');

