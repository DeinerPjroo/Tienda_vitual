<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido #{{ $pedido->id }} - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <x-topbar />

    <div class="breadcrumb">
        <a href="{{ url('/home') }}">Inicio</a>
        <span>›</span>
        <a href="{{ route('pedidos') }}">Historial de Pedidos</a>
        <span>›</span>
        <span>Detalle del Pedido #{{ $pedido->id }}</span>
    </div>

    <div class="cart-container">
        <h1 class="cart-title">Detalle del Pedido #{{ $pedido->id }}</h1>

        <div class="cart-layout">
            <!-- Items del Pedido -->
            <div class="cart-items-section">
                @foreach($pedido->items as $item)
                    @php
                        $precioFinal = $item->precio_unitario; // Ya incluye precio por item
                        $subtotalItem = $precioFinal * $item->cantidad;
                    @endphp
                    <div class="cart-item">
                        <div class="item-image-container">
                            @if($item->variacion->prenda->imagenes->count() > 0)
                                <img src="{{ asset($item->variacion->prenda->imagenes->first()->url) }}" alt="{{ $item->nombre_prenda_snapshot }}" class="item-image">
                            @else
                                <div class="no-image-placeholder">👕</div>
                            @endif
                        </div>

                        <div class="item-details">
                            <h3 class="item-name">{{ $item->nombre_prenda_snapshot }}</h3>
                            
                            <div class="item-specs">
                                <span>Talla: {{ $item->variacion->talla }}</span>
                                <span class="separator">|</span>
                                <span>Color: {{ $item->variacion->color }}</span>
                            </div>

                            <div class="item-price-section">
                                <span class="item-price">${{ number_format($precioFinal, 0) }}</span>
                            </div>

                            <div class="item-quantity">
                                Cantidad: {{ $item->cantidad }}
                            </div>

                            <div class="item-subtotal">
                                <p class="subtotal-label">Subtotal:</p>
                                <p class="subtotal-amount">${{ number_format($subtotalItem, 0) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Resumen del Pedido -->
            <div class="cart-summary">
                <h2 class="summary-title">Resumen del Pedido</h2>

                <div class="summary-line">
                    <span>Subtotal ({{ $pedido->items->count() }} artículo{{ $pedido->items->count() != 1 ? 's' : '' }})</span>
                    <span>${{ number_format($pedido->subtotal, 0) }}</span>
                </div>

                <div class="summary-line">
                    <span>Envío</span>
                    @if($pedido->costo_envio == 0)
                        <span class="free-shipping">¡GRATIS!</span>
                    @else
                        <span>${{ number_format($pedido->costo_envio, 0) }}</span>
                    @endif
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span class="total-amount">${{ number_format($pedido->total, 0) }}</span>
                </div>

                <p class="tax-included">IVA incluido</p>

                

                <div class="order-actions">
    @if(in_array($pedido->estado, ['pendiente', 'pagado']))
        <form method="POST" action="{{ route('pedido.cancelar', $pedido->id) }}" style="display:inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-action btn-cancel">Cancelar Pedido</button>
        </form>
    @endif

    @if($pedido->estado === 'entregado')
        <form method="POST" action="{{ route('pedido.reordenar', $pedido->id) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn-action btn-reorder">Volver a Comprar</button>
        </form>
    @endif

    @if($pedido->estado === 'enviado' || $pedido->estado === 'entregado')
        <a href="{{ route('pedido.rastrear', $pedido->id) }}" class="btn-action btn-track">Rastrear Pedido</a>
    @endif

    <a href="{{ route('pedido.factura', $pedido->id) }}" class="btn-action btn-invoice">Ver Factura</a>

    <a href="{{ route('pedidos') }}" class="checkout-btn">
                    Volver al Historial
                </a>
</div>


            </div>
        </div>
    </div>
</body>
</html>
