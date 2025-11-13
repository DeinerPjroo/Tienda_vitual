<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda: {{ $query }} - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/topbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <x-topbar />

    <div class="container" style="padding: 40px 20px;">
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 2rem; color: #2d3748; margin-bottom: 10px;">
                Resultados de búsqueda
            </h1>
            <p style="color: #718096; font-size: 1.1rem;">
                Encontramos <strong>{{ $total }}</strong> resultado(s) para "<strong>{{ $query }}</strong>"
            </p>
        </div>

        @if($productos->count() > 0)
            <div class="products-grid">
                @foreach($productos as $producto)
                    <div class="product-card">
                        <button class="wishlist-btn">♡</button>
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
                        </div>
                        <div class="product-info">
                            @if($producto->categoria_nombre)
                                <div style="font-size: 0.85rem; color: #718096; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $producto->categoria_nombre }}
                                </div>
                            @endif
                            <div class="product-name">{{ $producto->nombre }}</div>
                            
                            @if($producto->descripcion_corta)
                                <p style="font-size: 0.85rem; color: #718096; margin: 8px 0; line-height: 1.4;">
                                    {{ Str::limit($producto->descripcion_corta, 60) }}
                                </p>
                            @endif

                            @if($producto->descuento > 0)
                                <div class="product-price" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin: 10px 0;">
                                    <span style="font-size: 1.2rem; font-weight: 700; color: #667eea;">
                                        ${{ number_format($producto->precio * (1 - $producto->descuento / 100), 0) }}
                                    </span>
                                    <span style="font-size: 0.9rem; color: #a0aec0; text-decoration: line-through;">
                                        ${{ number_format($producto->precio, 0) }}
                                    </span>
                                    <span style="background: #48bb78; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                        -{{ $producto->descuento }}%
                                    </span>
                                </div>
                            @else
                                <div class="product-price" style="font-size: 1.2rem; font-weight: 700; color: #2d3748; margin: 10px 0;">
                                    ${{ number_format($producto->precio, 0) }}
                                </div>
                            @endif

                            <div class="product-actions">
                                <button class="btn-add-cart" onclick="addToCart({{ $producto->id }}, '{{ $producto->nombre }}')">
                                    Agregar
                                </button>
                                <a href="{{ route('producto.detalle', $producto->id) }}" class="btn-details">
                                    Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div style="margin-top: 40px; display: flex; justify-content: center;">
                {{ $productos->appends(['q' => $query])->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                <h2 style="font-size: 1.5rem; color: #2d3748; margin-bottom: 10px;">
                    No encontramos productos
                </h2>
                <p style="color: #718096; margin-bottom: 30px;">
                    Intenta con otros términos de búsqueda o explora nuestras categorías
                </p>
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <a href="{{ url('/home') }}" class="btn-primary">Inicio</a>
                    <a href="{{ url('/mujer') }}" class="btn-primary">Mujer</a>
                    <a href="{{ url('/hombre') }}" class="btn-primary">Hombre</a>
                    <a href="{{ url('/ninos') }}" class="btn-primary">Niños</a>
                    <a href="{{ url('/accesorios') }}" class="btn-primary">Accesorios</a>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Funcionalidad para botón de favoritos
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                this.innerHTML = this.innerHTML === '♡' ? '❤️' : '♡';
            });
        });

        // Función para agregar al carrito
        function addToCart(productId, productName) {
            alert('Producto "' + productName + '" agregado al carrito\n(Funcionalidad del carrito próximamente)');
        }
    </script>
</body>
</html>