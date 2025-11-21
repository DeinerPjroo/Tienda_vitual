<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeLuxe - {{ $titulo }}</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @php
        // Definir colores según la categoría
        $categoriaLower = strtolower($nombreCategoria);
        if ($categoriaLower === 'mujer') {
            $heroGradient = 'linear-gradient(135deg, #ff6b9d 0%, #c44569 100%)';
            $primaryColor = '#ff6b9d';
            $primaryHover = '#e55a8a';
            $accentColor = '#ffd6e8';
            $categoryColor = '#ff6b9d';
        } elseif ($categoriaLower === 'hombre') {
            $heroGradient = 'linear-gradient(135deg, #4a90e2 0%, #2c5aa0 100%)';
            $primaryColor = '#4a90e2';
            $primaryHover = '#357abd';
            $accentColor = '#cfe3ff';
            $categoryColor = '#4a90e2';
        } elseif ($categoriaLower === 'ninos' || $categoriaLower === 'niños') {
            $heroGradient = 'linear-gradient(135deg, #52c5ff 0%, #2b8fd4 100%)';
            $primaryColor = '#52c5ff';
            $primaryHover = '#3ab0e6';
            $accentColor = '#d6f3ff';
            $categoryColor = '#52c5ff';
        } elseif ($categoriaLower === 'accesorios') {
            $heroGradient = 'linear-gradient(135deg, #d4a574 0%, #b8865a 100%)';
            $primaryColor = '#d4a574';
            $primaryHover = '#c49564';
            $accentColor = '#f4ede4';
            $categoryColor = '#d4a574';
        } else {
            $heroGradient = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            $primaryColor = '#667eea';
            $primaryHover = '#5568d3';
            $accentColor = '#e8e8ff';
            $categoryColor = '#667eea';
        }
    @endphp
    <link rel="stylesheet" href="{{ asset('css/categoria.css') }}">
    <style>
        :root {
            --hero-gradient: {{ $heroGradient }};
            --primary-color: {{ $primaryColor }};
            --primary-hover: {{ $primaryHover }};
            --accent-color: {{ $accentColor }};
            --category-color: {{ $categoryColor }};
        }
    </style>
</head>
<body>
    <x-topbar />

    <div class="hero">
        <h1>{{ $titulo }}</h1>
        <p>{{ $descripcion }}</p>
    </div>

    <div class="content-area">
        <!-- Products Section con layout como home -->
        <div class="products-section">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <div class="filter-header">
                    <h3>Filtros</h3>
                    <span style="color: {{ $primaryColor }}; cursor: pointer; font-size: 1.2rem;">⚙️</span>
                </div>

                <!-- Categoría -->
                <div class="filter-section">
                    <div class="filter-title">
                        <span>Categoría</span>
                        <span>▼</span>
                    </div>
                    <div class="filter-options">
                        @foreach($categorias as $cat)
                            <label class="filter-option">
                                <input type="checkbox" name="categoria" value="{{ $cat->id }}" 
                                    {{ $categoria && $categoria->id == $cat->id ? 'checked' : '' }}>
                                {{ $cat->nombre }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Precio -->
                <div class="filter-section">
                    <div class="filter-title">
                        <span>Precio</span>
                        <span>▼</span>
                    </div>
                    <div class="price-range">
                        <div class="price-inputs">
                            <span>$50</span>
                            <span>$200</span>
                        </div>
                        <div class="price-slider">
                            <div class="price-progress"></div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div>
                <div class="sort-section">
                    <form method="GET" action="{{ url('/' . $nombreCategoria) }}">
                        <select name="orden" class="sort-dropdown" onchange="this.form.submit()">
                            <option value="">Más Relevante</option>
                            <option value="precio_asc" {{ request('orden') == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                            <option value="precio_desc" {{ request('orden') == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                            <option value="nuevos" {{ request('orden') == 'nuevos' ? 'selected' : '' }}>Nuevos</option>
                        </select>
                    </form>
                </div>

                <div class="products-grid">
            @forelse($productos as $producto)
                <div class="product-card">
                    <div class="product-image">
                        @if($producto->imagen_url)
                            @if(filter_var($producto->imagen_url, FILTER_VALIDATE_URL))
                                <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">
                            @else
                                <img src="{{ asset('storage/' . $producto->imagen_url) }}" alt="{{ $producto->nombre }}">
                            @endif
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%); display: flex; align-items: center; justify-content: center; color: #a0aec0; font-size: 3rem;">
                                👕
                            </div>
                        @endif
                        <button class="wishlist-btn" id="favorite-btn-{{ $producto->id }}" onclick="toggleFavorite({{ $producto->id }})" title="Agregar a favoritos">♡</button>
                    </div>
                    <div class="product-info">
                        @if($producto->categoria_nombre)
                            <div class="product-category">{{ $producto->categoria_nombre }}</div>
                        @endif
                        <div class="product-title">{{ $producto->nombre }}</div>
                        @if($producto->descripcion_corta)
                            <p style="font-size: 0.85rem; color: #718096; margin: 8px 0;">
                                {{ Str::limit($producto->descripcion_corta, 60) }}
                            </p>
                        @endif
                        <div class="product-price">
                            @if($producto->descuento > 0)
                                <span class="price-current">
                                    ${{ number_format($producto->precio * (1 - $producto->descuento / 100), 0) }}
                                </span>
                                <span class="price-original">
                                    ${{ number_format($producto->precio, 0) }}
                                </span>
                                <span class="discount-badge">
                                    -{{ $producto->descuento }}%
                                </span>
                            @else
                                <span class="price-current">
                                    ${{ number_format($producto->precio, 0) }}
                                </span>
                            @endif
                        </div>
                        <div class="product-actions">
                            <button class="btn-add-cart" onclick="addToCart({{ $producto->id }}, '{{ $producto->nombre }}')">
                                Añadir al Carrito
                            </button>
                            <a href="{{ route('producto.detalle', $producto->id) }}" class="btn-details">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h2 style="font-size: 1.5rem; color: #2d3748; margin-bottom: 10px;">
                        No hay productos disponibles
                    </h2>
                    <p style="color: #718096;">
                        No se encontraron productos en esta categoría
                    </p>
                </div>
                @endforelse
                </div>

                @if($productos->hasPages())
                    <div class="pagination">
                        {{ $productos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Verificar estado de favoritos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            @auth
                const productIds = Array.from(document.querySelectorAll('[id^="favorite-btn-"]'))
                    .map(btn => btn.id.replace('favorite-btn-', ''));
                
                productIds.forEach(productId => {
                    fetch(`/favoritos/${productId}/verificar`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const btn = document.getElementById(`favorite-btn-${productId}`);
                        if (btn && data.is_favorite) {
                            btn.classList.add('active');
                            btn.innerHTML = '❤️';
                            btn.title = 'Eliminar de favoritos';
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            @endauth
        });

        function toggleFavorite(productId) {
            @guest
                alert('Debes iniciar sesión para agregar productos a favoritos');
                window.location.href = "{{ route('login') }}";
                return;
            @endguest

            const btn = document.getElementById(`favorite-btn-${productId}`);
            
            fetch(`/favoritos/${productId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_favorite) {
                        btn.classList.add('active');
                        btn.innerHTML = '❤️';
                        btn.title = 'Eliminar de favoritos';
                    } else {
                        btn.classList.remove('active');
                        btn.innerHTML = '♡';
                        btn.title = 'Agregar a favoritos';
                    }
                } else {
                    alert(data.message || 'Error al actualizar favoritos');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar favoritos');
            });
        }

        function addToCart(productId, productName) {
            window.location.href = "{{ url('/producto') }}/" + productId;
        }
    </script>
</body>
</html>

