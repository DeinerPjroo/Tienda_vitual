<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Mujer</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mujer.css') }}">
</head>
<body>
    <x-topbar />

    <div class="hero">
        <h1>Moda para Mujer</h1>
        <p>Descubre las últimas tendencias en ropa femenina. Vestidos elegantes, blusas modernas y más con increíbles descuentos.</p>
        <button class="btn-primary">Explorar Ofertas Ahora</button>
    </div>

    <div class="content-area">
        <div class="filters-section">
            <div class="filters">
                <div class="filter-title">Filtros ⚙️</div>
                <div class="filter-group">
                    <h3>Categoría ▼</h3>
                    <label class="filter-option">
                        <input type="checkbox"> Vestidos
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Blusas
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Pantalones
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Faldas
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
                <div class="product-image" style="background: linear-gradient(135deg, #ffd6e8 0%, #ffe8f0 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Vestidos</div>
                    <div class="product-title">Vestido Rojo Elegante</div>
                    <div class="product-price">
                        <span class="price-current">$59.99</span>
                        <span class="price-original">$89.99</span>
                        <span class="discount-badge">-33%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #d4e4ff 0%, #e8f0ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Blusas</div>
                    <div class="product-title">Blusa Floral de Verano</div>
                    <div class="product-price">
                        <span class="price-current">$34.99</span>
                        <span class="price-original">$54.99</span>
                        <span class="discount-badge">-36%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #fff4d6 0%, #fff9e8 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Pantalones</div>
                    <div class="product-title">Jeans Mom Fit</div>
                    <div class="product-price">
                        <span class="price-current">$49.99</span>
                        <span class="price-original">$79.99</span>
                        <span class="discount-badge">-38%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #e8d4ff 0%, #f0e8ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Faldas</div>
                    <div class="product-title">Falda Midi Plisada</div>
                    <div class="product-price">
                        <span class="price-current">$39.99</span>
                        <span class="price-original">$64.99</span>
                        <span class="discount-badge">-38%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #ffd4d4 0%, #ffe8e8 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Blusas</div>
                    <div class="product-title">Camisa de Seda Romántica</div>
                    <div class="product-price">
                        <span class="price-current">$44.99</span>
                        <span class="price-original">$74.99</span>
                        <span class="discount-badge">-40%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #d4fff4 0%, #e8fffa 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Vestidos</div>
                    <div class="product-title">Vestido Maxi Bohemio</div>
                    <div class="product-price">
                        <span class="price-current">$69.99</span>
                        <span class="price-original">$119.99</span>
                        <span class="discount-badge">-42%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/mujer.js') }}"></script>
</body>
</html>