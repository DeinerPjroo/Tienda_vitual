<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Accesorios</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/accesorios.css') }}">
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
                <a href="/accesorios" class="active">Accesorios</a>
            </nav>

            <div class="header-actions">
                <div class="search-box">
                    <input type="text" placeholder="Buscar accesorios...">
                    <span class="search-icon">🔍</span>
                </div>
                <a href="/favoritos" class="header-link">♡ Favoritos</a>
                <a href="/carrito" class="header-link">🛒 Carrito</a>
                <a href="/cuenta" class="header-link">👤 Cuenta</a>
            </div>
        </div>
    </header>

    <div class="hero">
        <h1>Accesorios que marcan estilo</h1>
        <p>Descubre relojes, gafas, bolsos y mucho más. Diseños que complementan tu outfit con elegancia, innovación y detalles únicos.</p>
        <button class="btn-primary">Explorar Accesorios</button>
    </div>

 <div class="content-area">
        <div class="filters-section">
            <div class="filters">
                <div class="filter-title">Filtros ⚙️</div>
                <div class="filter-group">
                    <h3>Categoría ▼</h3>
                    <label class="filter-option">
                        <input type="checkbox"> Camisas
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Pantalones
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Chaquetas
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Suéteres
                    </label>
                </div>
            </div>
            <select class="sort-dropdown">
                <option>Más Relevante</option>
                <option>Precio: Menor a Mayor</option>
                <option>Precio: Mayor a Menor</option>
                <option>Nuevos</option>
            </select>
        </div>

        <div class="products-grid">
            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #dce9ff 0%, #edf4ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Relojes</div>
                    <div class="product-title">Reloj Smart Steel</div>
                    <div class="product-price">
                        <span class="price-current">$249.900</span>
                        <span class="price-original">$329.900</span>
                        <span class="discount-badge">-25%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #ffe8cf 0%, #fff4e0 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Gafas</div>
                    <div class="product-title">Gafas Urban Vision</div>
                    <div class="product-price">
                        <span class="price-current">$159.900</span>
                        <span class="price-original">$199.900</span>
                        <span class="discount-badge">-20%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #e6f9f2 0%, #f2fffa 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Bolsos</div>
                    <div class="product-title">Bolso Minimalista Beige</div>
                    <div class="product-price">
                        <span class="price-current">$189.900</span>
                        <span class="price-original">$239.900</span>
                        <span class="discount-badge">-21%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #f7e6ff 0%, #fdf1ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Carteras</div>
                    <div class="product-title">Cartera Premium Negra</div>
                    <div class="product-price">
                        <span class="price-current">$99.900</span>
                        <span class="price-original">$139.900</span>
                        <span class="discount-badge">-29%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #e0f7ff 0%, #f3fbff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Gorras</div>
                    <div class="product-title">Gorra SportFlex Blanca</div>
                    <div class="product-price">
                        <span class="price-current">$69.900</span>
                        <span class="price-original">$99.900</span>
                        <span class="discount-badge">-30%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #fff6e5 0%, #fffaf1 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Relojes</div>
                    <div class="product-title">Reloj Clásico Cuero</div>
                    <div class="product-price">
                        <span class="price-current">$219.900</span>
                        <span class="price-original">$299.900</span>
                        <span class="discount-badge">-27%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/accesorios.js') }}"></script>
</body>
</html>
