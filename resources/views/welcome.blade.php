<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BeLuxe - Lujo sin exceso</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|cormorant:400,500,600,700" rel="stylesheet" />

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    </head>
    <body>
        <header>
            @if (Route::has('login'))
                <nav>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-nav">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav btn-login">
                            Iniciar Sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-nav btn-register">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main>
            <div class="hero-container">
                <div class="logo-container">
                    <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe - Lujo sin exceso">
                </div>

                <div class="hero-content">
                    <h1 class="hero-title">Bienvenido a BeLuxe</h1>
                    <p class="hero-subtitle">Lujo sin exceso</p>
                    <p class="hero-description">
                        Descubre una experiencia única donde la elegancia se encuentra con la sofisticación. 
                        En BeLuxe, redefinimos el concepto de lujo con productos y servicios excepcionales 
                        que celebran la calidad sin ostentación.
                    </p>
                </div>

                <div class="features">
                    <div class="feature-card">
                        <div class="feature-icon">✨</div>
                        <h3 class="feature-title">Calidad Premium</h3>
                        <p class="feature-text">
                            Productos seleccionados cuidadosamente para garantizar la máxima calidad y durabilidad.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <h3 class="feature-title">Diseño Exclusivo</h3>
                        <p class="feature-text">
                            Cada pieza es única, combinando elegancia atemporal con tendencias contemporáneas.
                        </p>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">🤝</div>
                        <h3 class="feature-title">Servicio Excepcional</h3>
                        <p class="feature-text">
                            Atención personalizada que supera las expectativas en cada interacción.
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>