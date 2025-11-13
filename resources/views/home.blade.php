<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Tienda de Ropa</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <x-topbar />

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1>Ofertas Exclusivas de Temporada</h1>
            <p>Descubre nuestra colección de moda con descuentos increíbles. Renueva tu armario con estilo y al mejor precio, solo por tiempo limitado.</p>
            <button class="btn-primary">Explorar Ofertas Ahora</button>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <!-- Tabs -->
        <div class="tabs">
            <div class="tab">Comprar Mujer</div>
            <div class="tab active">Comprar Hombre</div>
            <div class="tab">Comprar Accesorios</div>
        </div>

        <!-- Products Section -->
        <div class="products-section">
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <div class="filter-header">
                    <h3>Filtros</h3>
                    <span style="color: #667eea; cursor: pointer;">⚙️</span>
                </div>

                <!-- Categoría -->
                <div class="filter-section">
                    <div class="filter-title">
                        <span>Categoría</span>
                        <span>▼</span>
                    </div>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox"> Camisetas y Tops
                        </label>
                        <label class="filter-option">
                            <input type="checkbox"> Pantalones
                        </label>
                        <label class="filter-option">
                            <input type="checkbox"> Vestidos y Faldas
                        </label>
                        <label class="filter-option">
                            <input type="checkbox"> Abrigos y Chaquetas
                        </label>
                        <label class="filter-option">
                            <input type="checkbox"> Calzado
                        </label>
                        <label class="filter-option">
                            <input type="checkbox"> Accesorios
                        </label>
                        <label class="filter-option">
                            <input type="checkbox"> Ropa Deportiva
                        </label>
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



                <!-- Talla -->
                <div class="filter-section">
                    <div class="filter-title">
                        <span>Talla</span>
                        <span>▼</span>
                    </div>
                    <div class="size-grid">
                        <div class="size-option">XS</div>
                        <div class="size-option">S</div>
                        <div class="size-option">M</div>
                        <div class="size-option">L</div>
                        <div class="size-option">XL</div>
                    </div>
                </div>

                <!-- Color -->
                <div class="filter-section">
                    <div class="filter-title">
                        <span>Color</span>
                        <span>▼</span>
                    </div>
                    <div class="color-grid">
                        <div class="color-option" style="background: #000000;"></div>
                        <div class="color-option" style="background: #FFFFFF; border: 1px solid #e2e8f0;"></div>
                        <div class="color-option" style="background: #718096;"></div>
                        <div class="color-option" style="background: #2B6CB0;"></div>
                        <div class="color-option" style="background: #C6F6D5;"></div>
                        <div class="color-option" style="background: #22543D;"></div>
                    </div>
                </div>
            </aside>

            <!-- Products Grid -->
            <div>
                <div class="sort-section">
                    <select class="sort-dropdown">
                        <option>Más Relevante</option>
                        <option>Menor Precio</option>
                        <option>Mayor Precio</option>
                        <option>Más Nuevo</option>
                        <option>Mejor Valorado</option>
                    </select>
                </div>

                <div class="products-grid">
    @forelse($productos as $producto)
        <div class="product-card">
            <div class="product-image">
                @if($producto->imagen_url)
                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}">
                @else
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%); display: flex; align-items: center; justify-content: center; color: #a0aec0; font-size: 3rem;">
                        👕
                    </div>
                @endif
                <button class="wishlist-btn">♡</button>
            </div>
            <div class="product-info">
                @if($producto->categoria_nombre)
                    <div style="font-size: 0.85rem; color: #718096; margin-bottom: 5px; text-transform: uppercase;">
                        {{ $producto->categoria_nombre }}
                    </div>
                @endif
                
                <h3 class="product-name">{{ $producto->nombre }}</h3>
                
                @if($producto->descripcion_corta)
                    <p style="font-size: 0.85rem; color: #718096; margin: 8px 0;">
                        {{ Str::limit($producto->descripcion_corta, 60) }}
                    </p>
                @endif
                
                @if($producto->descuento > 0)
                    <p class="product-price" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span style="font-weight: 700; color: #667eea; font-size: 1.2rem;">
                            ${{ number_format($producto->precio * (1 - $producto->descuento / 100), 0) }}
                        </span>
                        <span style="font-size: 0.9rem; color: #a0aec0; text-decoration: line-through;">
                            ${{ number_format($producto->precio, 0) }}
                        </span>
                        <span style="background: #48bb78; color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                            -{{ $producto->descuento }}%
                        </span>
                    </p>
                @else
                    <p class="product-price" style="font-weight: 700; color: #2d3748; font-size: 1.2rem;">
                        ${{ number_format($producto->precio, 0) }}
                    </p>
                @endif
                
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
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
            <div style="font-size: 4rem; margin-bottom: 20px;">📦</div>
            <h2 style="font-size: 1.5rem; color: #2d3748; margin-bottom: 10px;">
                No hay productos disponibles
            </h2>
            <p style="color: #718096;">
                Agrega productos desde el panel de administración
            </p>
        </div>
    @endforelse
</div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-overlay"></div>

<!-- Cart Modal -->
<div class="cart-modal" id="cart-modal">
    <!-- Cart Header -->
    <div class="cart-header">
        <button class="back-btn" id="close-cart">‹</button>
        <h2>Mi carrito (<span id="cart-items-count">1</span>)</h2>
    </div>

    <!-- Cart Items -->
    <div class="cart-items" id="cart-items-container">
        <div class="cart-item">
            <img src="https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=200" alt="Tenis casuales" class="item-image">
            <div class="item-details">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <h3 class="item-name">Tenis casuales con detalle verde</h3>
                    <button class="delete-btn" onclick="removeItem(this)">🗑️</button>
                </div>
                <p class="item-price">$ 109.900</p>
                <p class="item-specs">Talla 41 | Crema Muy Claro</p>
                <div class="item-actions">
                    <button class="edit-btn">Editar</button>
                    <div class="quantity-controls">
                        <button class="qty-btn" onclick="decreaseQty(this)">−</button>
                        <span class="quantity">1</span>
                        <button class="qty-btn" onclick="increaseQty(this)">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Footer -->
    <div class="cart-footer">
        <div class="summary-row">
            <span class="summary-label">1 artículo</span>
            <span class="summary-value">$ 109.900</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Envío estimado</span>
            <span class="summary-value">$ 7.000</span>
        </div>

        <div class="free-shipping-bar">
            <div class="free-shipping-progress"></div>
        </div>
        <p class="free-shipping-text">
            Faltan <span class="free-shipping-amount">$ 40.000</span> para tu <strong>ENVÍO GRATUITO</strong>
        </p>


        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Total</span>
                <span class="total-amount">$ 116.900</span>
            </div>
            <p class="tax-included">IVA incluido</p>
        </div>

        <button class="checkout-btn">Finalizar pedido</button>
       
    </div>
</div>

    <script>
    // Funcionalidad para tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

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
        // Aquí irá la lógica real del carrito
    }

    // Modal del carrito
    document.addEventListener('DOMContentLoaded', function() {
        const cartLink = document.querySelector('a[href="/carrito"]');
        const cartModal = document.getElementById('cart-modal');
        const modalOverlay = document.getElementById('modal-overlay');
        const closeCart = document.getElementById('close-cart');

        if (cartLink) {
            cartLink.addEventListener('click', (e) => {
                e.preventDefault();
                cartModal.classList.add('active');
                modalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        function closeCartModal() {
            cartModal.classList.remove('active');
            modalOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (closeCart) {
            closeCart.addEventListener('click', closeCartModal);
        }
        if (modalOverlay) {
            modalOverlay.addEventListener('click', closeCartModal);
        }
    });

    // Funciones de cantidad del carrito
    function increaseQty(btn) {
        const qtySpan = btn.previousElementSibling;
        let qty = parseInt(qtySpan.textContent);
        qtySpan.textContent = qty + 1;
        updateCartTotal();
    }

    function decreaseQty(btn) {
        const qtySpan = btn.nextElementSibling;
        let qty = parseInt(qtySpan.textContent);
        if (qty > 1) {
            qtySpan.textContent = qty - 1;
            updateCartTotal();
        }
    }

    function removeItem(btn) {
        if (confirm('¿Eliminar este producto del carrito?')) {
            btn.closest('.cart-item').remove();
            updateCartTotal();
        }
    }

    function updateCartTotal() {
        const items = document.querySelectorAll('.cart-item');
        let totalItems = 0;
        let subtotal = 0;

        items.forEach(item => {
            const qty = parseInt(item.querySelector('.quantity').textContent);
            const priceText = item.querySelector('.item-price').textContent.replace(/[$.]/g, '').trim();
            const price = parseInt(priceText);
            totalItems += qty;
            subtotal += price * qty;
        });

        document.getElementById('cart-items-count').textContent = totalItems;
        
        const shipping = 7000;
        const total = subtotal + shipping;
        
        document.querySelector('.summary-row .summary-value').textContent = `$ ${subtotal.toLocaleString()}`;
        document.querySelector('.total-amount').textContent = `$ ${total.toLocaleString()}`;
    }
</script>

    // Funciones de cantidad
    function increaseQty(btn) {
        const qtySpan = btn.previousElementSibling;
        let qty = parseInt(qtySpan.textContent);
        qtySpan.textContent = qty + 1;
        updateCartTotal();
    }

    function decreaseQty(btn) {
        const qtySpan = btn.nextElementSibling;
        let qty = parseInt(qtySpan.textContent);
        if (qty > 1) {
            qtySpan.textContent = qty - 1;
            updateCartTotal();
        }
    }

    function removeItem(btn) {
        if (confirm('¿Eliminar este producto del carrito?')) {
            btn.closest('.cart-item').remove();
            updateCartTotal();
        }
    }

    function updateCartTotal() {
        const items = document.querySelectorAll('.cart-item');
        let totalItems = 0;
        let subtotal = 0;

        items.forEach(item => {
            const qty = parseInt(item.querySelector('.quantity').textContent);
            const priceText = item.querySelector('.item-price').textContent.replace(/[$.]/g, '').trim();
            const price = parseInt(priceText);
            totalItems += qty;
            subtotal += price * qty;
        });

        document.getElementById('cart-items-count').textContent = totalItems;
        
        const shipping = 7000;
        const total = subtotal + shipping;
        
        document.querySelector('.summary-row .summary-value').textContent = `$ ${subtotal.toLocaleString()}`;
        document.querySelector('.total-amount').textContent = `$ ${total.toLocaleString()}`;
    }
    </script>
    
</body>
</html>