<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Favoritos</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/favoritos.css') }}">
</head>
<body>
    <header>
        <div class="header-content">
            <a class="logo">
                <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe Logo" class="logo-image">
            </a>

            <nav>
                <a href="{{ url('/home') }}">Inicio</a>
                <a href="/mujer">Mujer</a>
                <a href="/hombre">Hombre</a>
                <a href="/ninos">Niños</a>
                <a href="/accesorios">Accesorios</a>
            </nav>

            <div class="header-actions">
                <div class="search-box">
                    <input type="text" placeholder="Buscar productos...">
                    <span class="search-icon">🔍</span>
                </div>
                <a href="/favoritos" class="header-link">♡ Favoritos</a>
                <a href="/carrito" class="header-link">🛒 Carrito</a>
                <a href="/cuenta" class="header-link">👤 Cuenta</a>
            </div>
        </div>
    </header>

    <div class="hero">
        <h1>Mis Favoritos ❤️</h1>
        <p>Explora los productos que más te encantan. Puedes agregarlos al carrito o eliminarlos de tu lista de favoritos.</p>
    </div>

    <div class="content-area">
        <div class="products-grid">

            <div class="product-card">
                <button class="favorite-btn active">❤️</button>
                <div class="product-image" style="background: linear-gradient(135deg, #e8f0ff 0%, #f2f6ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Camisas</div>
                    <div class="product-title">Camisa Blanca Clásica</div>
                    <div class="product-price">
                        <span class="price-current">$39.99</span>
                        <span class="price-original">$64.99</span>
                        <span class="discount-badge">-38%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn active">❤️</button>
                <div class="product-image" style="background: linear-gradient(135deg, #fff4d6 0%, #fff9e8 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Accesorios</div>
                    <div class="product-title">Reloj Clásico Dorado</div>
                    <div class="product-price">
                        <span class="price-current">$89.99</span>
                        <span class="price-original">$129.99</span>
                        <span class="discount-badge">-31%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn active">❤️</button>
                <div class="product-image" style="background: linear-gradient(135deg, #e8ffe8 0%, #f5fff5 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Zapatos</div>
                    <div class="product-title">Zapatillas Urbanas</div>
                    <div class="product-price">
                        <span class="price-current">$74.99</span>
                        <span class="price-original">$99.99</span>
                        <span class="discount-badge">-25%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn active">❤️</button>
                <div class="product-image" style="background: linear-gradient(135deg, #ffe8f3 0%, #fff0f7 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Bolsos</div>
                    <div class="product-title">Bolso de Cuero Premium</div>
                    <div class="product-price">
                        <span class="price-current">$129.99</span>
                        <span class="price-original">$189.99</span>
                        <span class="discount-badge">-32%</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('js/favoritos.js') }}"></script>
</body>
</html>
