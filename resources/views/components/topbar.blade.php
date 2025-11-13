<!-- resources/views/components/topbar.blade.php -->
<header>
    <div class="header-content">
        <a href="{{ url('/home') }}" class="logo">
            <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe Logo" class="logo-image">
        </a>

        <nav>
            <a href="{{ url('/home') }}" class="{{ request()->is('home') ? 'active' : '' }}">Inicio</a>
            <a href="{{ url('/mujer') }}" class="{{ request()->is('mujer') ? 'active' : '' }}">Mujer</a>
            <a href="{{ url('/hombre') }}" class="{{ request()->is('hombre') ? 'active' : '' }}">Hombre</a>
            <a href="{{ url('/ninos') }}" class="{{ request()->is('ninos') ? 'active' : '' }}">Niños</a>
            <a href="{{ url('/accesorios') }}" class="{{ request()->is('accesorios') ? 'active' : '' }}">Accesorios</a>
        </nav>

        <div class="header-actions">
            <form action="{{ route('productos.buscar') }}" method="GET" class="search-form">
                <div class="search-box">
                    <input type="text" name="q" placeholder="Buscar productos..." value="{{ request('q') }}" required>
                    <button type="submit" class="search-icon">🔍</button>
                </div>
            </form>
            <a href="{{ url('/favoritos') }}" class="header-link">♡ Favoritos</a>
            <a href="{{ url('/carrito') }}" class="header-link">🛒 Carrito</a>
            
            @auth
                <a href="{{ route('profile') }}" class="header-link">
                    👤 {{ auth()->user()->nombre }}
                </a>
            @else
                <a href="{{ route('login') }}" class="header-link">👤 Cuenta</a>
            @endauth
        </div>
    </div>
</header>