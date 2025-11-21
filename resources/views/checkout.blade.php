<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <x-topbar />

    <div class="breadcrumb">
        <a href="{{ url('/home') }}">Inicio</a>
        <span>›</span>
        <a href="{{ route('carrito.index') }}">Carrito</a>
        <span>›</span>
        <span>Checkout</span>
    </div>

    <div class="checkout-container">
        <h1 class="checkout-title">Finalizar Compra</h1>

        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('pedido.crear') }}" method="POST" id="checkoutForm">
            @csrf
            
            <div class="checkout-layout">
                <!-- Sección Principal -->
                <div class="checkout-main">
                    
                    <!-- Paso 1: Dirección de Envío -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="step-number">1</div>
                            <h2>Dirección de Envío</h2>
                        </div>

                        <div class="section-content">
                            @if($direcciones->count() > 0)
                                <div class="addresses-list">
                                    @foreach($direcciones as $direccion)
                                    <label class="address-option {{ $direccion->predeterminada ? 'selected' : '' }}">
                                        <input type="radio" 
                                               name="direccion_id" 
                                               value="{{ $direccion->id }}" 
                                               {{ $direccion->predeterminada ? 'checked' : '' }}
                                               required>
                                        <div class="address-card-content">
                                            <div class="address-header">
                                                <strong>{{ $direccion->nombre_completo }}</strong>
                                                @if($direccion->predeterminada)
                                                    <span class="badge-default">Predeterminada</span>
                                                @endif
                                            </div>
                                            <p class="address-text">
                                                {{ $direccion->direccion_linea1 }}<br>
                                                @if($direccion->direccion_linea2)
                                                    {{ $direccion->direccion_linea2 }}<br>
                                                @endif
                                                {{ $direccion->ciudad }}, {{ $direccion->departamento }}<br>
                                                {{ $direccion->codigo_postal }}, {{ $direccion->pais }}
                                            </p>
                                            <p class="address-phone">📱 {{ $direccion->telefono }}</p>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>

                                <button type="button" class="btn-add-new" onclick="window.location.href='{{ route('direcciones') }}'">
                                    + Agregar Nueva Dirección
                                </button>
                            @else
                                <div class="empty-addresses">
                                    <p>No tienes direcciones guardadas</p>
                                    <a href="{{ route('direcciones') }}" class="btn-primary">Agregar Dirección</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Paso 2: Método de Pago -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="step-number">2</div>
                            <h2>Método de Pago</h2>
                        </div>

                        <div class="section-content">
                            <div class="payment-methods">
                                <label class="payment-option selected">
                                    <input type="radio" name="metodo_pago" value="contraentrega" checked required>
                                    <div class="payment-content">
                                        <div class="payment-icon">💵</div>
                                        <div class="payment-details">
                                            <strong>Pago Contraentrega</strong>
                                            <p>Paga en efectivo cuando recibas tu pedido</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="metodo_pago" value="tarjeta" required>
                                    <div class="payment-content">
                                        <div class="payment-icon">💳</div>
                                        <div class="payment-details">
                                            <strong>Tarjeta de Crédito/Débito</strong>
                                            <p>Pago seguro con tarjeta</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="metodo_pago" value="transferencia" required>
                                    <div class="payment-content">
                                        <div class="payment-icon">🏦</div>
                                        <div class="payment-details">
                                            <strong>Transferencia Bancaria</strong>
                                            <p>Transfiere directamente a nuestra cuenta</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="payment-option">
                                    <input type="radio" name="metodo_pago" value="pse" required>
                                    <div class="payment-content">
                                        <div class="payment-icon">🔒</div>
                                        <div class="payment-details">
                                            <strong>PSE</strong>
                                            <p>Débito desde tu cuenta bancaria</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 3: Notas del Pedido (Opcional) -->
                    <div class="checkout-section">
                        <div class="section-header">
                            <div class="step-number">3</div>
                            <h2>Notas del Pedido (Opcional)</h2>
                        </div>

                        <div class="section-content">
                            <textarea name="nota" 
                                      class="order-notes" 
                                      placeholder="¿Alguna indicación especial para tu pedido? (Ej: Tocar timbre, dejar con el portero, etc.)"
                                      rows="4"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Resumen del Pedido -->
                <div class="order-summary-sidebar">
                    <div class="summary-sticky">
                        <h2 class="summary-title">Resumen del Pedido</h2>

                        <div class="summary-items">
                            @foreach($items as $item)
                            <div class="summary-item">
                                <div class="summary-item-image">
                                    @if($item->variacion->prenda->imagenes->count() > 0)
                                        <img src="{{ asset($item->variacion->prenda->imagenes->first()->url) }}" 
                                             alt="{{ $item->variacion->prenda->nombre }}">
                                    @else
                                        <div class="no-image">👕</div>
                                    @endif
                                </div>
                                <div class="summary-item-details">
                                    <p class="item-name">{{ $item->variacion->prenda->nombre }}</p>
                                    <p class="item-specs">
                                        {{ $item->variacion->color }} | {{ $item->variacion->talla }} | Cant: {{ $item->cantidad }}
                                    </p>
                                    <p class="item-price">${{ number_format($item->precio_unitario * $item->cantidad, 0) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 0) }}</span>
                        </div>

                        <div class="summary-line">
                            <span>Envío</span>
                            @if($envio == 0)
                                <span class="free">¡GRATIS!</span>
                            @else
                                <span>${{ number_format($envio, 0) }}</span>
                            @endif
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-total">
                            <span>Total a Pagar</span>
                            <span class="total-amount">${{ number_format($total, 0) }}</span>
                        </div>

                        <button type="button" class="btn-place-order" id="btnPlaceOrder" onclick="mostrarConfirmacion()">
                            Confirmar Pedido
                        </button>

                        <div class="security-badges">
                            <div class="badge">🔒 Compra Segura</div>
                            <div class="badge">✓ Garantía de Calidad</div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal de Confirmación -->
    <div id="confirmModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px; margin: 10% auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <h2 style="margin-top: 0; color: #2d3748;">⚠️ Confirmar Pedido</h2>
            <div id="confirmContent" style="margin: 20px 0;">
                <!-- El contenido se llenará dinámicamente -->
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" onclick="cerrarConfirmacion()" style="padding: 10px 20px; background: #e2e8f0; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                    Cancelar
                </button>
                <button type="button" onclick="confirmarPedido()" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                    Sí, Confirmar Pedido
                </button>
            </div>
        </div>
    </div>

    <style>
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .confirm-item {
            padding: 10px;
            background: #f7fafc;
            border-radius: 5px;
            margin: 5px 0;
        }
    </style>

    <script>
        // Manejar selección de dirección
        document.querySelectorAll('.address-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.address-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        // Manejar selección de método de pago
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
            });
        });

        function mostrarConfirmacion() {
            const direccion = document.querySelector('input[name="direccion_id"]:checked');
            const metodoPago = document.querySelector('input[name="metodo_pago"]:checked');
            
            if (!direccion) {
                alert('⚠️ Por favor selecciona una dirección de envío');
                return;
            }
            
            if (!metodoPago) {
                alert('⚠️ Por favor selecciona un método de pago');
                return;
            }

            // Obtener información de la dirección seleccionada
            const direccionCard = direccion.closest('.address-option');
            const direccionNombre = direccionCard.querySelector('strong').textContent;
            const direccionTexto = direccionCard.querySelector('.address-text').textContent.trim();
            
            // Obtener información del método de pago
            const metodoPagoCard = metodoPago.closest('.payment-option');
            const metodoPagoNombre = metodoPagoCard.querySelector('strong').textContent;
            
            // Obtener total
            const total = document.querySelector('.total-amount').textContent;
            
            // Construir contenido del modal
            let contenido = `
                <p style="margin-bottom: 15px; font-size: 1.1rem;"><strong>¿Estás seguro de que deseas confirmar este pedido?</strong></p>
                
                <div style="background: #f7fafc; padding: 15px; border-radius: 5px; margin: 15px 0;">
                    <p style="margin: 5px 0;"><strong>📦 Dirección de Envío:</strong></p>
                    <p style="margin: 5px 0; color: #4a5568;">${direccionNombre}</p>
                    <p style="margin: 5px 0; color: #4a5568; font-size: 0.9rem;">${direccionTexto.replace(/\n/g, ', ')}</p>
                </div>
                
                <div style="background: #f7fafc; padding: 15px; border-radius: 5px; margin: 15px 0;">
                    <p style="margin: 5px 0;"><strong>💳 Método de Pago:</strong></p>
                    <p style="margin: 5px 0; color: #4a5568;">${metodoPagoNombre}</p>
                </div>
                
                <div style="background: #e6fffa; padding: 15px; border-radius: 5px; margin: 15px 0; border: 2px solid #38b2ac;">
                    <p style="margin: 5px 0;"><strong>💰 Total a Pagar:</strong></p>
                    <p style="margin: 5px 0; font-size: 1.3rem; color: #2d3748; font-weight: bold;">${total}</p>
                </div>
                
                <p style="margin-top: 15px; color: #718096; font-size: 0.9rem;">
                    Al confirmar, tu pedido será procesado y recibirás un correo de confirmación.
                </p>
            `;
            
            document.getElementById('confirmContent').innerHTML = contenido;
            document.getElementById('confirmModal').style.display = 'flex';
        }

        function cerrarConfirmacion() {
            document.getElementById('confirmModal').style.display = 'none';
        }

        function confirmarPedido() {
            const btn = document.getElementById('btnPlaceOrder');
            btn.disabled = true;
            btn.textContent = 'Procesando...';
            
            // Cerrar modal
            cerrarConfirmacion();
            
            // Enviar formulario
            document.getElementById('checkoutForm').submit();
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarConfirmacion();
            }
        });
    </script>
</body>
</html>