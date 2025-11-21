<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\AdminUserController;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use Laravel\Socialite\Facades\Socialite;

// ============================================
// RUTAS PÚBLICAS
// ============================================

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// ============================================
// RUTAS DE ADMINISTRADOR (Requieren rol admin)
// ============================================

Route::middleware(['auth', 'admin'])->group(function () {
    // Panel de administración
    Route::get('/homeadmin', [App\Http\Controllers\AdminHomeController::class, 'index'])->name('homeadmin');
    
    // Gestión de clientes
    Route::get('/gestion-clientes', [AdminUserController::class, 'index'])->name('gestion.clientes');
});

// Favoritos
Route::middleware(['auth'])->group(function () {
    Route::get('/favoritos', [App\Http\Controllers\FavoritoController::class, 'index'])->name('favoritos');
    Route::post('/favoritos/{prendaId}/agregar', [App\Http\Controllers\FavoritoController::class, 'agregar'])->name('favoritos.agregar');
    Route::delete('/favoritos/{favoritoId}', [App\Http\Controllers\FavoritoController::class, 'eliminar'])->name('favoritos.eliminar');
    Route::post('/favoritos/{prendaId}/toggle', [App\Http\Controllers\FavoritoController::class, 'toggle'])->name('favoritos.toggle');
    Route::get('/favoritos/{prendaId}/verificar', [App\Http\Controllers\FavoritoController::class, 'verificar'])->name('favoritos.verificar');
});

// Rutas de categorías con productos reales
Route::get('/hombre', function(Request $request) {
    return app(App\Http\Controllers\CategoriaController::class)->mostrarCategoria($request);
})->name('hombre');

Route::get('/ninos', function(Request $request) {
    return app(App\Http\Controllers\CategoriaController::class)->mostrarCategoria($request);
})->name('ninos');

Route::get('/niños', function(Request $request) {
    return app(App\Http\Controllers\CategoriaController::class)->mostrarCategoria($request);
})->name('ninos-alt');

Route::get('/accesorios', function(Request $request) {
    return app(App\Http\Controllers\CategoriaController::class)->mostrarCategoria($request);
})->name('accesorios');

Route::get('/mujer', function(Request $request) {
    return app(App\Http\Controllers\CategoriaController::class)->mostrarCategoria($request);
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

// ============================================
// GOOGLE OAUTH (Login con Google)
// ============================================
Route::get('/login-google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('login.google.callback');

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
    Route::post('/profile/cambiar-vista', [ProfileController::class, 'cambiarVista'])->name('profile.cambiar-vista');
    
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



// gestion de productos 
use App\Http\Controllers\AdminProductController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/gestion-productos', [AdminProductController::class, 'index'])->name('gestion.productos');
    Route::post('/gestion-productos', [AdminProductController::class, 'store'])->name('admin.productos.store');
    Route::get('/gestion-productos/{id}', [AdminProductController::class, 'show'])->name('admin.productos.show');
    Route::put('/gestion-productos/{id}', [AdminProductController::class, 'update'])->name('admin.productos.update');
    Route::delete('/gestion-productos/{id}', [AdminProductController::class, 'destroy'])->name('admin.productos.destroy');
});

// gestion de usuario 

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Rutas de usuarios
    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('admin.usuarios.index');
    Route::post('/usuarios', [AdminUserController::class, 'store'])->name('admin.usuarios.store');
    Route::get('/usuarios/{id}', [AdminUserController::class, 'show'])->name('admin.usuarios.show');
    Route::put('/usuarios/{id}', [AdminUserController::class, 'update'])->name('admin.usuarios.update');
    Route::delete('/usuarios/{id}', [AdminUserController::class, 'destroy'])->name('admin.usuarios.destroy');
    Route::post('/usuarios/{id}/toggle-activo', [AdminUserController::class, 'toggleActivo'])->name('admin.usuarios.toggle');
});

// gestion de categorias
use App\Http\Controllers\AdminCategoriaController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/gestion-categorias', [AdminCategoriaController::class, 'index'])->name('admin.categorias.index');
    Route::post('/gestion-categorias', [AdminCategoriaController::class, 'store'])->name('admin.categorias.store');
    Route::get('/gestion-categorias/{id}', [AdminCategoriaController::class, 'show'])->name('admin.categorias.show');
    Route::put('/gestion-categorias/{id}', [AdminCategoriaController::class, 'update'])->name('admin.categorias.update');
    Route::delete('/gestion-categorias/{id}', [AdminCategoriaController::class, 'destroy'])->name('admin.categorias.destroy');
});

// gestion de ventas
use App\Http\Controllers\AdminVentaController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/gestion-ventas', [AdminVentaController::class, 'index'])->name('admin.ventas.index');
    Route::get('/gestion-ventas/{id}', [AdminVentaController::class, 'show'])->name('admin.ventas.show');
    Route::put('/gestion-ventas/{id}/estado', [AdminVentaController::class, 'updateEstado'])->name('admin.ventas.updateEstado');
});

// gestion de envios
use App\Http\Controllers\AdminEnvioController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/gestion-envios', [AdminEnvioController::class, 'index'])->name('admin.envios.index');
    Route::get('/gestion-envios/{id}', [AdminEnvioController::class, 'show'])->name('admin.envios.show');
    Route::post('/gestion-envios/{id}/marcar-enviado', [AdminEnvioController::class, 'marcarEnviado'])->name('admin.envios.marcarEnviado');
    Route::post('/gestion-envios/{id}/marcar-entregado', [AdminEnvioController::class, 'marcarEntregado'])->name('admin.envios.marcarEntregado');
    Route::put('/gestion-envios/{id}/estado', [AdminEnvioController::class, 'actualizarEstado'])->name('admin.envios.actualizarEstado');
});