<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Tienda de Ropa</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
                <a class="logo">
                <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe Logo" class="logo-image">
                </a>

            <nav>
                <a href="{{ url('/home') }}">Inicio</a>
                <a href="/mujer">Mujer</a>
                <a href="{{ url('/hombre') }}">Hombre</a>
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
                <a href="/profile" class="header-link">👤 Cuenta</a>
            </div>
        </div>
    </header>

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
                    <!-- Product 1 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400" alt="Camiseta">
                            <button class="wishlist-btn">♡</button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Camiseta Básica de Algodón</h3>
                            <p class="product-price">$35.00</p>
                            <div class="product-actions">
                                <button class="btn-add-cart">Añadir al Carrito</button>
                                <button class="btn-details">Ver Detalles</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 2 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?w=400" alt="Pantalón">
                            <button class="wishlist-btn">♡</button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Pantalón Vaquero Ajustado</h3>
                            <p class="product-price">$70.00</p>
                            <div class="product-actions">
                                <button class="btn-add-cart">Añadir al Carrito</button>
                                <button class="btn-details">Ver Detalles</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 3 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400" alt="Vestido">
                            <button class="wishlist-btn">♡</button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Vestido Fluido Estampado</h3>
                            <p class="product-price">$85.00</p>
                            <div class="product-actions">
                                <button class="btn-add-cart">Añadir al Carrito</button>
                                <button class="btn-details">Ver Detalles</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 4 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400" alt="Sudadera">
                            <button class="wishlist-btn">♡</button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Sudadera con Capucha Premium</h3>
                            <p class="product-price">$60.00</p>
                            <div class="product-actions">
                                <button class="btn-add-cart">Añadir al Carrito</button>
                                <button class="btn-details">Ver Detalles</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 5 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=400" alt="Blusa">
                            <button class="wishlist-btn">♡</button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Blusa de Lino Transpirable</h3>
                            <p class="product-price">$50.00</p>
                            <div class="product-actions">
                                <button class="btn-add-cart">Añadir al Carrito</button>
                                <button class="btn-details">Ver Detalles</button>
                            </div>
                        </div>
                    </div>

                    <!-- Product 6 -->
                    <div class="product-card">
                        <div class="product-image">
                            <img src="https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400" alt="Chaqueta">
                            <button class="wishlist-btn">♡</button>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name">Chaqueta Impermeable Ligera</h3>
                            <p class="product-price">$110.00</p>
                            <div class="product-actions">
                                <button class="btn-add-cart">Añadir al Carrito</button>
                                <button class="btn-details">Ver Detalles</button>
                            </div>
                        </div>
                    </div>
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

         document.addEventListener('DOMContentLoaded', function() {
        const cartLink = document.querySelector('a[href="/carrito"]');
        const cartModal = document.getElementById('cart-modal');
        const modalOverlay = document.getElementById('modal-overlay');
        const closeCart = document.getElementById('close-cart');

        // Abrir carrito al hacer click en el enlace
        if (cartLink) {
            cartLink.addEventListener('click', (e) => {
                e.preventDefault();
                cartModal.classList.add('active');
                modalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }

        // Cerrar carrito
        function closeCartModal() {
            cartModal.classList.remove('active');
            modalOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        closeCart.addEventListener('click', closeCartModal);
        modalOverlay.addEventListener('click', closeCartModal);
    });

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