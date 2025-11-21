<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Administrador - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeadmin.blade.css') }}">
    <style>
        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 10px;
            text-transform: uppercase;
        }
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid var(--beluxe-purple);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: var(--beluxe-purple);
            font-size: 0.9rem;
            text-transform: uppercase;
            font-weight: 600;
        }
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #1a202c;
            margin: 0;
        }
        .admin-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        .admin-link-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 2px solid transparent;
        }
        .admin-link-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-color: var(--beluxe-purple);
        }
        .admin-link-card h4 {
            margin: 0 0 8px 0;
            color: var(--beluxe-purple);
            font-size: 1.1rem;
        }
        .admin-link-card p {
            margin: 0;
            color: #718096;
            font-size: 0.9rem;
        }
        .admin-link-card .icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
     <x-topbar-admin />

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="profile-layout">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="profile-card">
                        <div class="profile-image">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nombre . ' ' . auth()->user()->apellido) }}&size=200&background=667eea&color=fff" alt="{{ auth()->user()->nombre }}">
                            <span class="online-indicator"></span>
                        </div>
                        <h2 class="profile-name">
                            {{ auth()->user()->nombre }} {{ auth()->user()->apellido }}
                            <span class="admin-badge">Admin</span>
                        </h2>
                        <p class="profile-email">{{ auth()->user()->correo }}</p>
                    </div>
                    <nav class="sidebar-nav">
                        <!-- Link a Información Personal -->
                        <a href="{{ route('profile') }}" class="sidebar-link active">Información Personal</a>
                        
                        <!-- Link a Panel de Administración -->
                        <a href="{{ route('homeadmin') }}" class="sidebar-link">Panel de Administración</a>
                        
                        <!-- Formulario de Logout -->
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="sidebar-link logout">Cerrar Sesión</button>
                        </form>
                    </nav>
                </aside>

                <!-- Content Area -->
                <section class="content-area">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul style="margin: 0; padding-left: 1.5rem;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h1 class="content-title">Perfil de Administrador</h1>
                    
                    <!-- Estadísticas Rápidas -->
                    <div class="admin-stats">
                        <div class="stat-card">
                            <h3>Total Usuarios</h3>
                            <p class="stat-value">{{ \App\Models\Usuario::count() }}</p>
                        </div>
                        <div class="stat-card">
                            <h3>Total Productos</h3>
                            <p class="stat-value">{{ \App\Models\Prenda::count() }}</p>
                        </div>
                        <div class="stat-card">
                            <h3>Pedidos Pendientes</h3>
                            <p class="stat-value">{{ \App\Models\Pedido::where('estado', 'pendiente')->count() }}</p>
                        </div>
                        <div class="stat-card">
                            <h3>Ventas del Mes</h3>
                            <p class="stat-value">${{ number_format(\App\Models\Pedido::whereMonth('fecha_pedido', now()->month)->whereIn('estado', ['pagado', 'procesando', 'enviado', 'entregado'])->sum('total'), 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Información Personal -->
                    <div style="margin-top: 40px;">
                        <h2 style="margin-bottom: 20px; color: #1a202c;">Información Personal</h2>
                        <form class="profile-form" method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre', auth()->user()->nombre) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="apellido">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" value="{{ old('apellido', auth()->user()->apellido) }}" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Correo Electrónico</label>
                                    <input type="email" id="email" name="correo" value="{{ old('correo', auth()->user()->correo) }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="telefono">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono" value="{{ old('telefono', auth()->user()->telefono) }}">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', auth()->user()->fecha_nacimiento) }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Rol</label>
                                    <input type="text" value="Administrador" readonly style="background: #f7fafc; cursor: not-allowed;">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn-submit">Guardar Cambios</button>
                        </form>
                    </div>

                    <!-- Selector de Vista -->
                    <div style="margin-top: 40px; margin-bottom: 30px;">
                        <h2 style="margin-bottom: 20px; color: #1a202c;">⚙️ Preferencias de Vista</h2>
                        <div style="background: white; border-radius: 12px; padding: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 0.95rem;">
                                Como administrador, puedes elegir entre ver la vista de administrador o la vista de usuario/cliente.
                            </p>
                            <form method="POST" action="{{ route('profile.cambiar-vista') }}" id="vista-form">
                                @csrf
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                    <label style="cursor: pointer; border: 2px solid {{ session('vista_preferida', 'admin') === 'admin' ? 'var(--beluxe-purple)' : '#e5e7eb' }}; border-radius: 12px; padding: 20px; transition: all 0.3s; background: {{ session('vista_preferida', 'admin') === 'admin' ? '#f3f4f6' : 'white' }};">
                                        <input type="radio" name="vista" value="admin" {{ session('vista_preferida', 'admin') === 'admin' ? 'checked' : '' }} onchange="document.getElementById('vista-form').submit();" style="display: none;">
                                        <div style="text-align: center;">
                                            <div style="font-size: 2.5rem; margin-bottom: 10px;">👨‍💼</div>
                                            <div style="font-weight: 600; color: var(--beluxe-dark); margin-bottom: 5px;">Vista Administrador</div>
                                            <div style="font-size: 0.85rem; color: #6b7280;">Panel de control y gestión</div>
                                            @if(session('vista_preferida', 'admin') === 'admin')
                                                <div style="margin-top: 10px; color: var(--beluxe-purple); font-weight: 600; font-size: 0.9rem;">✓ Activa</div>
                                            @endif
                                        </div>
                                    </label>
                                    <label style="cursor: pointer; border: 2px solid {{ session('vista_preferida', 'admin') === 'usuario' ? 'var(--beluxe-purple)' : '#e5e7eb' }}; border-radius: 12px; padding: 20px; transition: all 0.3s; background: {{ session('vista_preferida', 'admin') === 'usuario' ? '#f3f4f6' : 'white' }};">
                                        <input type="radio" name="vista" value="usuario" {{ session('vista_preferida', 'admin') === 'usuario' ? 'checked' : '' }} onchange="document.getElementById('vista-form').submit();" style="display: none;">
                                        <div style="text-align: center;">
                                            <div style="font-size: 2.5rem; margin-bottom: 10px;">🛍️</div>
                                            <div style="font-weight: 600; color: var(--beluxe-dark); margin-bottom: 5px;">Vista Usuario</div>
                                            <div style="font-size: 0.85rem; color: #6b7280;">Tienda y compras</div>
                                            @if(session('vista_preferida', 'admin') === 'usuario')
                                                <div style="margin-top: 10px; color: var(--beluxe-purple); font-weight: 600; font-size: 0.9rem;">✓ Activa</div>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            </form>
                            <div style="margin-top: 15px; padding: 12px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                                <p style="margin: 0; font-size: 0.85rem; color: #92400e;">
                                    <strong>💡 Nota:</strong> Esta preferencia se aplicará la próxima vez que inicies sesión. 
                                    Puedes cambiar entre vistas en cualquier momento desde aquí.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Accesos Rápidos -->
                    <div style="margin-top: 40px;">
                        <h2 style="margin-bottom: 20px; color: #1a202c;">Accesos Rápidos</h2>
                        <div class="admin-links">
                            <a href="{{ route('gestion.productos') }}" class="admin-link-card">
                                <div class="icon">📦</div>
                                <h4>Gestión de Productos</h4>
                                <p>Administrar catálogo de productos</p>
                            </a>
                            <a href="{{ route('gestion.clientes') }}" class="admin-link-card">
                                <div class="icon">👥</div>
                                <h4>Gestión de Clientes</h4>
                                <p>Ver y administrar usuarios</p>
                            </a>
                            <a href="{{ route('admin.categorias.index') }}" class="admin-link-card">
                                <div class="icon">📁</div>
                                <h4>Gestión de Categorías</h4>
                                <p>Administrar categorías de productos</p>
                            </a>
                            <a href="{{ route('admin.ventas.index') }}" class="admin-link-card">
                                <div class="icon">💰</div>
                                <h4>Ventas</h4>
                                <p>Ver y gestionar pedidos</p>
                            </a>
                            <a href="{{ route('admin.envios.index') }}" class="admin-link-card">
                                <div class="icon">🚚</div>
                                <h4>Envíos</h4>
                                <p>Gestionar envíos y entregas</p>
                            </a>
                            <a href="{{ route('homeadmin') }}" class="admin-link-card">
                                <div class="icon">🏠</div>
                                <h4>Panel Principal</h4>
                                <p>Volver al panel de administración</p>
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#">Información</a>
                    <a href="#">Comprar</a>
                    <a href="#">Legal</a>
                </div>
                <div class="social-links">
                    <a href="#" class="social-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <a href="#" class="social-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Resaltar link activo según la URL actual
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const links = document.querySelectorAll('.sidebar-link');
            
            links.forEach(link => {
                // Remover clase active de todos
                link.classList.remove('active');
                
                // Agregar active al link que coincida con la URL actual
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>

