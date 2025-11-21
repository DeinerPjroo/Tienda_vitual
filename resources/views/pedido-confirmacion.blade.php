<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Confirmado - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .success-icon {
            text-align: center;
            font-size: 5rem;
            margin-bottom: 20px;
        }
        .confirmation-title {
            text-align: center;
            color: #2d3748;
            margin-bottom: 10px;
        }
        .confirmation-subtitle {
            text-align: center;
            color: #718096;
            margin-bottom: 30px;
        }
        .order-info {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #4a5568;
        }
        .info-value {
            color: #2d3748;
        }
        .order-items {
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .item-details {
            flex: 1;
        }
        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .item-specs {
            color: #718096;
            font-size: 0.9rem;
        }
        .item-price {
            font-weight: 600;
            color: #667eea;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn {
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #2d3748;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .status-pendiente {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <x-topbar />

    <div class="breadcrumb">
        <a href="{{ url('/home') }}">Inicio</a>
        <span>›</span>
        <span>Pedido Confirmado</span>
    </div>

    <div class="confirmation-container">
        <div class="success-icon">✅</div>
        <h1 class="confirmation-title">¡Pedido Confirmado!</h1>
        <p class="confirmation-subtitle">Tu pedido ha sido registrado exitosamente</p>

        <div class="order-info">
            <div class="info-row">
                <span class="info-label">Número de Pedido:</span>
                <span class="info-value"><strong>#{{ str_pad($pedido->id, 6, '0', STR_PAD_LEFT) }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span class="info-value">{{ $pedido->fecha_pedido->format('d/m/Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="info-value">
                    <span class="status-badge status-{{ $pedido->estado }}">{{ ucfirst($pedido->estado) }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Método de Pago:</span>
                <span class="info-value">
                    @if($pedido->pagos->count() > 0)
                        {{ ucfirst($pedido->pagos->first()->metodo) }}
                    @else
                        N/A
                    @endif
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Total:</span>
                <span class="info-value"><strong>${{ number_format($pedido->total + $pedido->costo_envio + $pedido->impuestos, 0) }}</strong></span>
            </div>
        </div>

        @if($pedido->direccion)
        <div class="order-info">
            <h3 style="margin-top: 0; margin-bottom: 15px;">Dirección de Envío</h3>
            <p style="margin: 5px 0;"><strong>{{ $pedido->direccion->nombre_completo }}</strong></p>
            <p style="margin: 5px 0;">{{ $pedido->direccion->direccion_linea1 }}</p>
            @if($pedido->direccion->direccion_linea2)
                <p style="margin: 5px 0;">{{ $pedido->direccion->direccion_linea2 }}</p>
            @endif
            <p style="margin: 5px 0;">{{ $pedido->direccion->ciudad }}, {{ $pedido->direccion->departamento }}</p>
            <p style="margin: 5px 0;">{{ $pedido->direccion->codigo_postal }}, {{ $pedido->direccion->pais }}</p>
            <p style="margin: 5px 0;">📱 {{ $pedido->direccion->telefono }}</p>
        </div>
        @endif

        <div class="order-items">
            <h3 style="margin-bottom: 15px;">Productos del Pedido</h3>
            @foreach($pedido->items as $item)
            <div class="order-item">
                <div class="item-details">
                    <div class="item-name">{{ $item->nombre_prenda_snapshot }}</div>
                    <div class="item-specs">
                        Cantidad: {{ $item->cantidad }} | 
                        Precio unitario: ${{ number_format($item->precio_unitario, 0) }}
                    </div>
                </div>
                <div class="item-price">
                    ${{ number_format($item->precio_unitario * $item->cantidad, 0) }}
                </div>
            </div>
            @endforeach
        </div>

        <div class="order-info">
            <div class="info-row">
                <span class="info-label">Subtotal:</span>
                <span class="info-value">${{ number_format($pedido->total, 0) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Envío:</span>
                <span class="info-value">
                    @if($pedido->costo_envio == 0)
                        <span style="color: #48bb78; font-weight: 600;">GRATIS</span>
                    @else
                        ${{ number_format($pedido->costo_envio, 0) }}
                    @endif
                </span>
            </div>
            <div class="info-row" style="border-top: 2px solid #cbd5e0; margin-top: 10px; padding-top: 15px;">
                <span class="info-label" style="font-size: 1.1rem;">Total:</span>
                <span class="info-value" style="font-size: 1.2rem; font-weight: bold; color: #667eea;">
                    ${{ number_format($pedido->total + $pedido->costo_envio + $pedido->impuestos, 0) }}
                </span>
            </div>
        </div>

        <div style="background: #e6fffa; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #38b2ac;">
            <p style="margin: 0; color: #2d3748;">
                <strong>📧</strong> Recibirás un correo de confirmación con los detalles de tu pedido.<br>
                <strong>📦</strong> Te notificaremos cuando tu pedido sea enviado.
            </p>
        </div>

        <div class="action-buttons">
            <a href="{{ route('pedido.detalle', $pedido->id) }}" class="btn btn-primary">Ver Detalles del Pedido</a>
            <a href="{{ route('home') }}" class="btn btn-secondary">Seguir Comprando</a>
        </div>
    </div>
</body>
</html>

