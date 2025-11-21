<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeLuxe - Mis Favoritos</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/favoritos.css') }}">
</head>
<body>
    <x-topbar />

    <div class="hero">
        <h1>Mis Favoritos ❤️</h1>
        <p>Explora los productos que más te encantan. Puedes agregarlos al carrito o eliminarlos de tu lista de favoritos.</p>
    </div>

    <div class="content-area">
        <div class="favoritos-header">
            <div class="favoritos-count">
                Tienes <span>{{ $favoritos->total() }}</span> producto{{ $favoritos->total() != 1 ? 's' : '' }} en favoritos
            </div>
            @if($favoritos->count() > 0)
                <button class="clear-all-btn" onclick="limpiarTodosFavoritos()">
                    🗑️ Limpiar Todos
                </button>
            @endif
        </div>

        <div class="products-grid">
            @forelse($favoritos as $favorito)
                <div class="product-card" data-favorito-id="{{ $favorito->favorito_id }}">
                    <div class="product-image">
                        @if($favorito->imagen_url)
                            @if(filter_var($favorito->imagen_url, FILTER_VALIDATE_URL))
                                <img src="{{ $favorito->imagen_url }}" alt="{{ $favorito->nombre }}">
                            @else
                                <img src="{{ asset('storage/' . $favorito->imagen_url) }}" alt="{{ $favorito->nombre }}">
                            @endif
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%); display: flex; align-items: center; justify-content: center; color: #a0aec0; font-size: 3rem;">
                                👕
                            </div>
                        @endif
                        <button class="wishlist-btn active" onclick="eliminarFavorito({{ $favorito->favorito_id }}, {{ $favorito->id }})" title="Eliminar de favoritos">
                            ❤️
                        </button>
                    </div>
                    <div class="product-info">
                        @if($favorito->categoria_nombre)
                            <div class="product-category">{{ $favorito->categoria_nombre }}</div>
                        @endif
                        <div class="product-title">{{ $favorito->nombre }}</div>
                        @if($favorito->descripcion_corta)
                            <p style="font-size: 0.85rem; color: #718096; margin: 8px 0;">
                                {{ Str::limit($favorito->descripcion_corta, 60) }}
                            </p>
                        @endif
                        <div class="product-price">
                            @if($favorito->descuento > 0)
                                <span class="price-current">
                                    ${{ number_format($favorito->precio * (1 - $favorito->descuento / 100), 0) }}
                                </span>
                                <span class="price-original">
                                    ${{ number_format($favorito->precio, 0) }}
                                </span>
                                <span class="discount-badge">
                                    -{{ $favorito->descuento }}%
                                </span>
                            @else
                                <span class="price-current">
                                    ${{ number_format($favorito->precio, 0) }}
                                </span>
                            @endif
                </div>
                        <div class="product-actions">
                            <button class="btn-add-cart" onclick="addToCart({{ $favorito->id }}, '{{ $favorito->nombre }}')">
                                Añadir al Carrito
                            </button>
                            <a href="{{ route('producto.detalle', $favorito->id) }}" class="btn-details">
                                Ver Detalles
                            </a>
                            <button class="btn-remove" onclick="eliminarFavorito({{ $favorito->favorito_id }}, {{ $favorito->id }})">
                                🗑️
                            </button>
            </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-state-icon">💔</div>
                    <h2>No tienes productos en favoritos</h2>
                    <p>Explora nuestra tienda y agrega productos que te gusten a tu lista de favoritos</p>
                    <a href="{{ route('home') }}" class="btn-primary">Explorar Productos</a>
                </div>
            @endforelse
            </div>

        @if($favoritos->hasPages())
            <div class="pagination">
                {{ $favoritos->links() }}
            </div>
        @endif
    </div>

    <script>
        // Obtener token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function eliminarFavorito(favoritoId, prendaId) {
            if (!confirm('¿Estás seguro de que quieres eliminar este producto de tus favoritos?')) {
                return;
            }

            fetch(`/favoritos/${favoritoId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remover el card del DOM
                    const card = document.querySelector(`[data-favorito-id="${favoritoId}"]`);
                    if (card) {
                        card.style.transition = 'opacity 0.3s, transform 0.3s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            // Recargar la página si no quedan productos
                            const remainingCards = document.querySelectorAll('.product-card');
                            if (remainingCards.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }
                } else {
                    alert(data.message || 'Error al eliminar el favorito');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el favorito');
            });
        }

        function limpiarTodosFavoritos() {
            if (!confirm('¿Estás seguro de que quieres eliminar todos los productos de tus favoritos?')) {
                return;
            }

            const favoritos = document.querySelectorAll('[data-favorito-id]');
            const favoritoIds = Array.from(favoritos).map(card => card.getAttribute('data-favorito-id'));

            // Eliminar todos los favoritos
            Promise.all(
                favoritoIds.map(id => 
                    fetch(`/favoritos/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                )
            )
            .then(() => {
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar los favoritos');
            });
        }

        function addToCart(productId, productName) {
            // Redirigir al detalle del producto para seleccionar variaciones
            window.location.href = "{{ url('/producto') }}/" + productId;
        }
    </script>
</body>
</html>
