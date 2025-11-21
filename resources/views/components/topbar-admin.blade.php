<!-- resources/views/components/topbar-admin.blade.php -->
<header>
    <div class="header-content">
        <a href="{{ url('/homeadmin') }}" class="logo">
            <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe Logo" class="logo-image">
        </a>

        <nav>
            <a href="{{ route('gestion.productos') }}" 
               class="{{ request()->is('gestion-productos*') ? 'active' : '' }}">
                Gestión de Productos
            </a>
            <a href="{{ url('/gestion-clientes') }}" 
               class="{{ request()->is('gestion-clientes*') || request()->is('gestion-usuarios*') ? 'active' : '' }}">
                Gestión de Clientes
            </a>
            <a href="{{ route('admin.categorias.index') }}" 
               class="{{ request()->is('gestion-categorias*') ? 'active' : '' }}">
                Gestión de Categorías
            </a>
            <a href="{{ route('admin.ventas.index') }}" 
               class="{{ request()->is('gestion-ventas*') ? 'active' : '' }}">
                Ventas
            </a>
            <a href="{{ route('admin.envios.index') }}" 
               class="{{ request()->is('gestion-envios*') ? 'active' : '' }}">
                Envíos
            </a>
        </nav>

        <div class="header-actions">
            <a href="{{ route('carrito.index') }}" class="header-link">🛒 Pedidos</a>
            <a href="{{ route('profile') }}" class="header-link">👤 Cuenta</a>
        </div>
    </div>
</header>

