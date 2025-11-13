<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Hombre</title>
    <link rel="stylesheet" href="{{ asset('css/hombre.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
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
                <a href="/hombre" class="active">Hombre</a>
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
        <h1>Moda para Hombre</h1>
        <p>Descubre las últimas tendencias en ropa masculina. Camisas elegantes, chaquetas modernas y mucho más con increíbles descuentos.</p>
        <button class="btn-primary">Explorar Ofertas para Él</button>
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
                <div class="product-image" style="background: linear-gradient(135deg, #d4e8ff 0%, #e8f0ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Camisas</div>
                    <div class="product-title">Camisa Azul Casual</div>
                    <div class="product-price">
                        <span class="price-current">$49.99</span>
                        <span class="price-original">$79.99</span>
                        <span class="discount-badge">-37%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #d4ffd8 0%, #e8fff0 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Pantalones</div>
                    <div class="product-title">Pantalón Chino Beige</div>
                    <div class="product-price">
                        <span class="price-current">$59.99</span>
                        <span class="price-original">$94.99</span>
                        <span class="discount-badge">-36%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #fff4d6 0%, #fff9e8 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Chaquetas</div>
                    <div class="product-title">Chaqueta de Cuero Negra</div>
                    <div class="product-price">
                        <span class="price-current">$129.99</span>
                        <span class="price-original">$189.99</span>
                        <span class="discount-badge">-32%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #e8e4ff 0%, #f0e8ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Suéteres</div>
                    <div class="product-title">Suéter de Lana Gris</div>
                    <div class="product-price">
                        <span class="price-current">$54.99</span>
                        <span class="price-original">$79.99</span>
                        <span class="discount-badge">-31%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #d4f0ff 0%, #e8faff 100%);"></div>
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
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #d4fff4 0%, #e8fffa 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Pantalones</div>
                    <div class="product-title">Joggers Deportivos Negros</div>
                    <div class="product-price">
                        <span class="price-current">$44.99</span>
                        <span class="price-original">$69.99</span>
                        <span class="discount-badge">-36%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/hombre.js') }}"></script>
</body>
</html>
