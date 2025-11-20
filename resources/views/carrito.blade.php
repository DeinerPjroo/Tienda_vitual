<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <x-topbar />

    <div class="breadcrumb">
        <a href="{{ url('/home') }}">Inicio</a>
        <span>›</span>
        <span>Carrito de Compras</span>
    </div>

    <div class="cart-container">
        <h1 class="cart-title">Mi Carrito ({{ $items->count() }} artículo{{ $items->count() != 1 ? 's' : '' }})</h1>

        @if($items->count() > 0)
            <div class="cart-layout">
                <!-- Items del Carrito -->
                <div class="cart-items-section">
                    @foreach($items as $item)
                        @php
                            $precioFinal = $item->precio * (1 - $item->descuento / 100);
                            $subtotalItem = $precioFinal * $item->cantidad;
                        @endphp
                        <div class="cart-item" data-item-id="{{ $item->id }}">
                            <div class="item-image-container">
                                @if($item->imagen_url)
                                    <img src="{{ $item->imagen_url }}" alt="{{ $item->producto_nombre }}" class="item-image">
                                @else
                                    <div class="no-image-placeholder">👕</div>
                                @endif
                            </div>

                            <div class="item-details">
                                <h3 class="item-name">{{ $item->producto_nombre }}</h3>
                                
                                <div class="item-specs">
                                    @if($item->talla)
                                        <span>Talla: {{ $item->talla }}</span>
                                    @endif
                                    @if($item->color)
                                        <span class="separator">|</span>
                                        <span>Color: {{ $item->color }}</span>
                                    @endif
                                </div>

                                <div class="item-price-section">
                                    @if($item->descuento > 0)
                                        <span class="item-price">${{ number_format($precioFinal, 0) }}</span>
                                        <span class="item-price-original">${{ number_format($item->precio, 0) }}</span>
                                        <span class="item-discount">-{{ $item->descuento }}%</span>
                                    @else
                                        <span class="item-price">${{ number_format($item->precio, 0) }}</span>
                                    @endif
                                </div>

                                <div class="item-actions">
                                    <div class="quantity-controls">
                                        <button class="qty-btn" onclick="updateQuantity({{ $item->id }}, -1)">−</button>
                                        <input type="number" class="quantity-input" value="{{ $item->cantidad }}" min="1" max="{{ $item->stock }}" readonly>
                                        <button class="qty-btn" onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                                    </div>
                                    
                                    <button class="delete-btn" onclick="removeItem({{ $item->id }})">
                                        🗑️ Eliminar
                                    </button>
                                </div>
                            </div>

                            <div class="item-subtotal">
                                <p class="subtotal-label">Subtotal:</p>
                                <p class="subtotal-amount">${{ number_format($subtotalItem, 0) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Resumen del Pedido -->
                <div class="cart-summary">
                    <h2 class="summary-title">Resumen del Pedido</h2>

                    <div class="summary-line">
                        <span>Subtotal ({{ $items->count() }} artículo{{ $items->count() != 1 ? 's' : '' }})</span>
                        <span>${{ number_format($subtotal, 0) }}</span>
                    </div>

                    <div class="summary-line">
                        <span>Envío</span>
                        @if($envio == 0)
                            <span class="free-shipping">¡GRATIS!</span>
                        @else
                            <span>${{ number_format($envio, 0) }}</span>
                        @endif
                    </div>

                    @if($subtotal < 150000 && $subtotal > 0)
                        <div class="free-shipping-info">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ ($subtotal / 150000) * 100 }}%"></div>
                            </div>
                            <p>Te faltan ${{ number_format(150000 - $subtotal, 0) }} para obtener <strong>envío gratis</strong></p>
                        </div>
                    @endif

                    <div class="summary-total">
                        <span>Total</span>
                        <span class="total-amount">${{ number_format($total, 0) }}</span>
                    </div>

                    <p class="tax-included">IVA incluido</p>

                    <button class="checkout-btn" onclick="proceedToCheckout()" href="{{ route('checkout') }}">
                        Finalizar Compra
                    </button>

                    <a href="{{ url('/home') }}" class="continue-shopping">
                        ← Seguir Comprando
                    </a>
                </div>
            </div>
        @else
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Tu carrito está vacío</h2>
                <p>¡Agrega productos y comienza a comprar!</p>
                <a href="{{ url('/home') }}" class="btn-primary">Explorar Productos</a>
            </div>
        @endif
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function updateQuantity(itemId, change) {
            const cartItem = document.querySelector(`[data-item-id="${itemId}"]`);
            const quantityInput = cartItem.querySelector('.quantity-input');
            let currentQty = parseInt(quantityInput.value);
            const maxStock = parseInt(quantityInput.getAttribute('max'));
            
            let newQty = currentQty + change;
            
            if (newQty < 1) newQty = 1;
            if (newQty > maxStock) {
                alert(`Solo hay ${maxStock} unidades disponibles`);
                return;
            }
            
            quantityInput.value = newQty;
            
            // Actualizar en el servidor
            fetch(`/carrito/item/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ cantidad: newQty })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar cantidad');
            });
        }

        function removeItem(itemId) {
            if (!confirm('¿Eliminar este producto del carrito?')) return;
            
            fetch(`/carrito/item/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar producto');
            });
        }

        function proceedToCheckout() {
            
            window.location.href = '/checkout';
        }

        function mostrarModalConfirmacion() {
    const modal = document.getElementById("modalConfirmacion");
    const contadorEl = document.getElementById("contadorRedirect");
    let segundos = 3;

    modal.style.display = "flex";

    const intervalo = setInterval(() => {
        segundos--;
        contadorEl.textContent = segundos;

        if (segundos === 0) {
            clearInterval(intervalo);
            window.location.href = "historial_pedidos.php";
        }
    }, 1000);
}
    </script>

    <!-- Modal de Confirmación -->
<div id="modalConfirmacion" class="modal-confirmacion" style="display:none;">
    <div class="modal-content">
        <h2>¡Pedido Confirmado!</h2>
        <p>Tu pedido ha sido procesado exitosamente.</p>
        <p>Serás redirigido en <span id="contadorRedirect">3</span> segundos...</p>

        <a href="historial_pedidos.php" class="btn btn-ir">Ir ahora</a>
    </div>
</div>

</body>
</html>