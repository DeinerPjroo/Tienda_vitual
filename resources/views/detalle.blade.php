<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $producto->nombre }} - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/detalle.css') }}">
</head>

<body>
    <x-topbar />

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ url('/home') }}">Inicio</a>
        <span>›</span>
        @if ($producto->categoria_nombre)
            <a href="{{ url('/' . strtolower($producto->categoria_nombre)) }}">{{ $producto->categoria_nombre }}</a>
            <span>›</span>
        @endif
        <span>{{ $producto->nombre }}</span>
    </div>

    <div class="product-detail-container">
        <!-- Galería de Imágenes -->
        <div class="product-gallery">
            <div class="main-image">
                @if ($imagenes->count() > 0)
                    {{-- Verificar si la URL es externa o local --}}
                    @php
                        $primeraImagen = $imagenes->first()->url;
                        $imageUrl =
                            str_starts_with($primeraImagen, 'http://') || str_starts_with($primeraImagen, 'https://')
                                ? $primeraImagen
                                : asset('storage/' . $primeraImagen);
                    @endphp
                    <img id="mainImage" src="{{ $imageUrl }}" alt="{{ $producto->nombre }}"
                        onerror="this.parentElement.innerHTML='<div class=\'no-image\'><span style=\'font-size: 5rem;\'>👕</span><p>Imagen no disponible</p></div>'">
                @else
                    <div class="no-image">
                        <span style="font-size: 5rem;">👕</span>
                        <p>Sin imagen disponible</p>
                    </div>
                @endif
                <button class="wishlist-btn-large">♡</button>
            </div>

            @if ($imagenes->count() > 1)
                <div class="thumbnail-gallery">
                    @foreach ($imagenes as $imagen)
                        @php
                            $thumbUrl =
                                str_starts_with($imagen->url, 'http://') || str_starts_with($imagen->url, 'https://')
                                    ? $imagen->url
                                    : asset('storage/' . $imagen->url);
                        @endphp
                        <img src="{{ $thumbUrl }}" alt="{{ $producto->nombre }}"
                            class="thumbnail {{ $loop->first ? 'active' : '' }}"
                            onclick="changeImage('{{ $thumbUrl }}', this)">
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Información del Producto -->
        <div class="product-info-section">
            @if ($producto->categoria_nombre)
                <div class="product-category">{{ $producto->categoria_nombre }}</div>
            @endif

            <h1 class="product-title">{{ $producto->nombre }}</h1>

            @if ($producto->descripcion_corta)
                <p class="product-subtitle">{{ $producto->descripcion_corta }}</p>
            @endif

            <!-- Precio -->
            <div class="price-section">
                @if ($producto->descuento > 0)
                    <div class="price-with-discount">
                        <span
                            class="current-price">${{ number_format($producto->precio * (1 - $producto->descuento / 100), 0) }}</span>
                        <span class="original-price">${{ number_format($producto->precio, 0) }}</span>
                        <span class="discount-badge">-{{ $producto->descuento }}% OFF</span>
                    </div>
                @else
                    <div class="current-price">${{ number_format($producto->precio, 0) }}</div>
                @endif
                <p class="tax-info">Precio incluye IVA</p>
            </div>

            <!-- Selección de Color -->
            @if ($coloresDisponibles->count() > 0)
                <div class="option-section">
                    <label class="option-label">Color: <span id="selected-color-name"></span></label>
                    <div class="color-options">
                        @foreach ($coloresDisponibles as $color)
                            @php
                                // Mapeo de colores en español a códigos hexadecimales
                                $colorMap = [
                                    'Negro' => '#000000',
                                    'Blanco' => '#FFFFFF',
                                    'Rojo' => '#DC143C',
                                    'Azul' => '#0000FF',
                                    'Verde' => '#008000',
                                    'Amarillo' => '#FFD700',
                                    'Rosa' => '#FFC0CB',
                                    'Gris' => '#808080',
                                    'Café' => '#8B4513',
                                    'Beige' => '#F5F5DC',
                                ];

                                $colorHex = $colorMap[$color->color] ?? '#CCCCCC';
                            @endphp
                            <div class="color-option"
                                style="background-color: {{ $colorHex }}; {{ $color->color == 'Blanco' ? 'border: 2px solid #e2e8f0;' : '' }}"
                                data-color-name="{{ $color->color }}" title="{{ $color->color }}"
                                onclick="selectColor('{{ $color->color }}', this)">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Selección de Talla -->
            @if ($tallasDisponibles->count() > 0)
                <div class="option-section">
                    <label class="option-label">Talla: <span id="selected-size-name"></span></label>
                    <div class="size-options">
                        @foreach ($tallasDisponibles as $talla)
                            <button class="size-option" onclick="selectSize('{{ $talla->talla }}', this)">
                                {{ $talla->talla }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Cantidad -->
            <div class="option-section">
                <label class="option-label">Cantidad</label>
                <div class="quantity-selector">
                    <button class="qty-btn" onclick="decreaseQuantity()">-</button>
                    <input type="number" id="quantity" value="1" min="1" max="10" readonly>
                    <button class="qty-btn" onclick="increaseQuantity()">+</button>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="action-buttons">
                <button class="btn-add-to-cart" onclick="addToCart()">
                    🛒 Agregar al Carrito
                </button>
                <button class="btn-buy-now" onclick="buyNow()">
                    Comprar Ahora
                </button>
            </div>

            <!-- Información Adicional -->
            <div class="additional-info">
                <div class="info-item">
                    <span class="info-icon">🚚</span>
                    <div>
                        <strong>Envío gratis</strong>
                        <p>En compras superiores a $150.000</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">↩️</span>
                    <div>
                        <strong>Devolución gratis</strong>
                        <p>Tienes 30 días para devolver tu producto</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="info-icon">🔒</span>
                    <div>
                        <strong>Compra segura</strong>
                        <p>Tus datos están protegidos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Descripción del Producto -->
    @if ($producto->descripcion)
        <div class="product-description-section">
            <h2>Descripción del Producto</h2>
            <div class="description-content">
                {{ $producto->descripcion }}
            </div>
        </div>
    @endif

    <!-- Stock y SKU -->
    @if ($producto->sku)
        <div class="product-meta">
            <p><strong>SKU:</strong> {{ $producto->sku }}</p>
            @if ($variaciones->sum('stock') > 0)
                <p><strong>Disponibilidad:</strong> <span class="in-stock">En Stock ({{ $variaciones->sum('stock') }}
                        unidades)</span></p>
            @else
                <p><strong>Disponibilidad:</strong> <span class="out-stock">Agotado</span></p>
            @endif
        </div>
    @endif

    <script>
    let selectedColor = null;
    let selectedSize = null;
    const productId = {{ $producto->id }};
    const variaciones = @json($variaciones);

    function changeImage(imageUrl, element) {
        document.getElementById('mainImage').src = imageUrl;
        document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
        element.classList.add('active');
    }

    function selectColor(color, element) {
        selectedColor = color;
        document.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('selected-color-name').textContent = color;
        updateAvailableSizes();
    }

    function selectSize(size, element) {
        selectedSize = size;
        document.querySelectorAll('.size-option').forEach(s => s.classList.remove('selected'));
        element.classList.add('selected');
        document.getElementById('selected-size-name').textContent = size;
        updateAvailableColors();
    }

    function updateAvailableSizes() {
        if (!selectedColor) return;
        
        document.querySelectorAll('.size-option').forEach(btn => {
            const size = btn.textContent.trim();
            const hasStock = variaciones.some(v => 
                v.color === selectedColor && 
                v.talla === size && 
                v.stock > 0
            );
            
            btn.disabled = !hasStock;
            btn.style.opacity = hasStock ? '1' : '0.3';
        });
    }

    function updateAvailableColors() {
        if (!selectedSize) return;
        
        document.querySelectorAll('.color-option').forEach(colorBtn => {
            const color = colorBtn.title;
            const hasStock = variaciones.some(v => 
                v.color === color && 
                v.talla === selectedSize && 
                v.stock > 0
            );
            
            colorBtn.style.opacity = hasStock ? '1' : '0.3';
            colorBtn.style.cursor = hasStock ? 'pointer' : 'not-allowed';
        });
    }

    function decreaseQuantity() {
        const input = document.getElementById('quantity');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function increaseQuantity() {
        const input = document.getElementById('quantity');
        const maxStock = getMaxStock();
        
        if (parseInt(input.value) < maxStock) {
            input.value = parseInt(input.value) + 1;
        } else {
            alert(`Solo hay ${maxStock} unidades disponibles`);
        }
    }

    function getMaxStock() {
        if (!selectedColor || !selectedSize) return 10;
        
        const variacion = variaciones.find(v => 
            v.color === selectedColor && 
            v.talla === selectedSize
        );
        
        return variacion ? variacion.stock : 0;
    }

    function getVariacionId() {
        console.log('getVariacionId llamado');
        console.log('selectedColor:', selectedColor);
        console.log('selectedSize:', selectedSize);
        
        // Si no hay colores ni tallas disponibles, retornar la primera variación
        @if($coloresDisponibles->count() == 0 && $tallasDisponibles->count() == 0)
            if (variaciones.length > 0) {
                return variaciones[0].id;
            }
            return null;
        @endif
        
        // Si solo hay colores pero no tallas
        @if($coloresDisponibles->count() > 0 && $tallasDisponibles->count() == 0)
            if (!selectedColor) return null;
            const variacion = variaciones.find(v => v.color === selectedColor);
            return variacion ? variacion.id : null;
        @endif
        
        // Si solo hay tallas pero no colores
        @if($tallasDisponibles->count() > 0 && $coloresDisponibles->count() == 0)
            if (!selectedSize) return null;
            const variacion = variaciones.find(v => v.talla === selectedSize);
            return variacion ? variacion.id : null;
        @endif
        
        // Si hay ambos, buscar la combinación exacta
        if (!selectedColor || !selectedSize) return null;
        
        const variacion = variaciones.find(v => 
            v.color === selectedColor && 
            v.talla === selectedSize
        );
        
        console.log('Variación encontrada:', variacion);
        return variacion ? variacion.id : null;
    }

    function addToCart() {
        console.log('addToCart llamado');
        console.log('selectedColor:', selectedColor);
        console.log('selectedSize:', selectedSize);
        console.log('variaciones:', variaciones);
        
        // Validar que se haya seleccionado color y talla solo si existen opciones
        @if($coloresDisponibles->count() > 0)
            if (!selectedColor) {
                alert('⚠️ Por favor selecciona un color');
                return;
            }
        @endif

        @if($tallasDisponibles->count() > 0)
            if (!selectedSize) {
                alert('⚠️ Por favor selecciona una talla');
                return;
            }
        @endif

        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        let variacionId = getVariacionId();
        
        console.log('variacionId obtenido:', variacionId);
        console.log('quantity:', quantity);

        // Si hay colores o tallas disponibles, debe haber una variación seleccionada
        @if($coloresDisponibles->count() > 0 || $tallasDisponibles->count() > 0)
            if (!variacionId) {
                alert('⚠️ La combinación seleccionada no está disponible');
                return;
            }
        @else
            // Si no hay variaciones, usar la primera variación disponible o null
            const primeraVariacion = variaciones.length > 0 ? variaciones[0].id : null;
            if (!primeraVariacion) {
                alert('⚠️ Este producto no tiene variaciones disponibles');
                return;
            }
            variacionId = primeraVariacion;
        @endif

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('❌ Error: Token CSRF no encontrado. Por favor recarga la página.');
            return;
        }

        // Enviar al servidor
        fetch('/carrito/agregar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                prenda_id: productId,
                variacion_id: variacionId,
                cantidad: quantity
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error en la petición');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                const mensaje = `✅ ${quantity} unidad(es) agregada(s) al carrito` + 
                    (selectedColor ? `\nColor: ${selectedColor}` : '') +
                    (selectedSize ? `\nTalla: ${selectedSize}` : '');
                alert(mensaje);
                // Opcional: redirigir al carrito
                // window.location.href = '/carrito';
            } else {
                alert('❌ ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert('❌ Error al agregar al carrito: ' + error.message);
        });
    }

    function buyNow() {
        console.log('buyNow llamado');
        
        // Validar selecciones
        @if($coloresDisponibles->count() > 0)
            if (!selectedColor) {
                alert('⚠️ Por favor selecciona un color');
                return;
            }
        @endif

        @if($tallasDisponibles->count() > 0)
            if (!selectedSize) {
                alert('⚠️ Por favor selecciona una talla');
                return;
            }
        @endif

        const quantity = parseInt(document.getElementById('quantity').value) || 1;
        let variacionId = getVariacionId();
        
        @if($coloresDisponibles->count() > 0 || $tallasDisponibles->count() > 0)
            if (!variacionId) {
                alert('⚠️ La combinación seleccionada no está disponible');
                return;
            }
        @else
            const primeraVariacion = variaciones.length > 0 ? variaciones[0].id : null;
            if (!primeraVariacion) {
                alert('⚠️ Este producto no tiene variaciones disponibles');
                return;
            }
            variacionId = primeraVariacion;
        @endif

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            alert('❌ Error: Token CSRF no encontrado. Por favor recarga la página.');
            return;
        }

        // Agregar al carrito y luego redirigir
        fetch('/carrito/agregar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                prenda_id: productId,
                variacion_id: variacionId,
                cantidad: quantity
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error en la petición');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                window.location.href = '/carrito';
            } else {
                alert('❌ ' + (data.message || 'Error al agregar al carrito'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error al agregar al carrito: ' + error.message);
        });
    }
</script>
</body>

</html>
