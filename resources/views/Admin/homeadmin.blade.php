<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Panel de Administración</title>
    <link rel="stylesheet" href="{{ asset('css/homeadmin.blade.css') }}">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <a class="logo">
                <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe Logo" class="logo-image">
            </a>

            <nav>
                <a href="/gestion-productos">Gestión de Productos</a>
                <a href="/gestion-clientes">Gestión de Clientes</a>
                <a href="#">Ventas</a>
                <a href="#">Envíos</a>
            </nav>

            <div class="header-actions">
                <a href="/carrito" class="header-link">🛒 Pedidos</a>
                <a href="/cuenta" class="header-link">👤 Cuenta</a>
            </div>
        </div>
    </header>

    <!-- Contenido principal centrado -->
    <main class="main-home">
        <section class="dynamic-section">
            <div class="welcome-box">
                <h1 id="saludo" class="saludo"></h1>
                <p id="reloj" class="reloj"></p>
                <p id="frase" class="frase"></p>
            </div>
        </section>

        <section class="info-section">
            <h2>Resumen general</h2>
            <div class="cards">
                <div class="card">
                    <h3>💰 Ventas del día</h3>
                    <p>$1,250.000</p>
                </div>
                <div class="card">
                    <h3>🧾 Pedidos activos</h3>
                    <p>8</p>
                </div>
                <div class="card">
                    <h3>👗 Productos en stock</h3>
                    <p>230</p>
                </div>
                <div class="card">
                    <h3>👥 Nuevos clientes</h3>
                    <p>5</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>© 2025 BeLuxe | Panel de Administración</p>
    </footer>

    <!-- Script dinámico -->
    <script>
        function obtenerSaludo() {
            const hora = new Date().getHours();
            if (hora < 12) return "🌅 Buenos días, BeLuxe Te Saluda";
            if (hora < 18) return "🌞 Buenas tardes, BeLuxe Te Saluda";
            return "🌙 Buenas noches, BeLuxe Te Saludae";
        }

        function actualizarReloj() {
            const ahora = new Date();
            const hora = ahora.toLocaleTimeString('es-CO', { hour12: false });
            document.getElementById("reloj").textContent = "🕒 " + hora;
        }

        const frases = [
            "✨ La elegancia no tiene precio, pero sí estilo.",
            "👕 Cada prenda cuenta una historia.",
            "💼 El éxito está en los detalles.",
            "💫 La moda es la mejor forma de expresarte sin hablar.",
            "🛍️ Un cliente feliz vuelve siempre."
        ];

        function fraseAleatoria() {
            const indice = Math.floor(Math.random() * frases.length);
            return frases[indice];
        }

        document.getElementById("saludo").textContent = obtenerSaludo();
        document.getElementById("frase").textContent = fraseAleatoria();
        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    </script>
</body>
</html>
