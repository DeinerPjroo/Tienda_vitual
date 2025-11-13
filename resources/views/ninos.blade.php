<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Niños</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ninos.css') }}">
</head>
<body>
    <x-topbar />

    <div class="hero">
        <h1>Moda para Niños</h1>
        <p>Ropa divertida, cómoda y colorida para los más pequeños. Encuentra conjuntos adorables, camisetas, zapatos y más con descuentos especiales.</p>
        <button class="btn-primary">Explorar Ofertas Infantiles</button>
    </div>

    <div class="content-area">
        <div class="filters-section">
            <div class="filters">
                <div class="filter-title">Filtros ⚙️</div>
                <div class="filter-group">
                    <h3>Categoría ▼</h3>
                    <label class="filter-option">
                        <input type="checkbox"> Camisetas
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Pantalones
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Conjuntos
                    </label>
                    <label class="filter-option">
                        <input type="checkbox"> Calzado
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
                <div class="product-image" style="background: linear-gradient(135deg, #ffe8a3 0%, #fff6d4 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Camisetas</div>
                    <div class="product-title">Camiseta Dinosaurio</div>
                    <div class="product-price">
                        <span class="price-current">$19.99</span>
                        <span class="price-original">$29.99</span>
                        <span class="discount-badge">-33%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #a3e4ff 0%, #d4f3ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Conjuntos</div>
                    <div class="product-title">Conjunto Deportivo Azul</div>
                    <div class="product-price">
                        <span class="price-current">$34.99</span>
                        <span class="price-original">$54.99</span>
                        <span class="discount-badge">-36%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #ffd4f3 0%, #ffe8fa 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Pantalones</div>
                    <div class="product-title">Pantalón de Algodón Verde</div>
                    <div class="product-price">
                        <span class="price-current">$24.99</span>
                        <span class="price-original">$39.99</span>
                        <span class="discount-badge">-37%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #caffd6 0%, #e8ffe8 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Calzado</div>
                    <div class="product-title">Zapatillas Multicolor</div>
                    <div class="product-price">
                        <span class="price-current">$39.99</span>
                        <span class="price-original">$59.99</span>
                        <span class="discount-badge">-33%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #e8f0ff 0%, #f0f8ff 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Camisetas</div>
                    <div class="product-title">Camiseta Astronauta</div>
                    <div class="product-price">
                        <span class="price-current">$22.99</span>
                        <span class="price-original">$34.99</span>
                        <span class="discount-badge">-34%</span>
                    </div>
                </div>
            </div>

            <div class="product-card">
                <button class="favorite-btn">♡</button>
                <div class="product-image" style="background: linear-gradient(135deg, #fff4d6 0%, #fff9e8 100%);"></div>
                <div class="product-info">
                    <div class="product-category">Conjuntos</div>
                    <div class="product-title">Conjunto Safari</div>
                    <div class="product-price">
                        <span class="price-current">$32.99</span>
                        <span class="price-original">$49.99</span>
                        <span class="discount-badge">-34%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/ninos.js') }}"></script>
</body>
</html>
