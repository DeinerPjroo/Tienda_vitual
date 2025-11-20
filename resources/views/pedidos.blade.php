<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos - BeLuxe</title>
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
   
</head>
<body>
    <!-- Header -->
    <x-topbar />

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="profile-layout">
                <!-- Sidebar -->
                <aside class="sidebar">
                    <div class="profile-card">
                        <div class="profile-image">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nombre . ' ' . auth()->user()->apellido) }}&size=200&background=4b5563&color=fff" alt="{{ auth()->user()->nombre }}">
                            <span class="online-indicator"></span>
                        </div>
                        <h2 class="profile-name">{{ auth()->user()->nombre }} {{ auth()->user()->apellido }}</h2>
                        <p class="profile-email">{{ auth()->user()->correo }}</p>
                    </div>
                    <nav class="sidebar-nav">
                        <a href="{{ route('profile') }}" class="sidebar-link">Información Personal</a>
                        <a href="{{ route('pedidos') }}" class="sidebar-link active">Historial de Pedidos</a>
                        <a href="{{ route('direcciones') }}" class="sidebar-link">Direcciones</a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="sidebar-link logout">Cerrar Sesión</button>
                        </form>
                    </nav>
                </aside>

                <!-- Content Area -->
                <section class="content-area">
                    <h1 class="content-title">Historial de Pedidos</h1>

                    @if($pedidos->count() > 0)
                        <div class="orders-list">
                            @foreach($pedidos as $pedido)
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-info">
                                        <h3 class="order-number">Pedido #{{ $pedido->id }}</h3>
                                        <p class="order-date">{{ $pedido->fecha_pedido->format('d M Y, H:i') }}</p>
                                    </div>
                                    <div class="order-status">
                                        <span class="status-badge status-{{ $pedido->estado }}">
                                            @switch($pedido->estado)
                                                @case('pendiente') Pendiente @break
                                                @case('pagado') Pagado @break
                                                @case('procesando') Procesando @break
                                                @case('enviado') Enviado @break
                                                @case('entregado') Entregado @break
                                                @case('cancelado') Cancelado @break
                                                @case('reembolsado') Reembolsado @break
                                            @endswitch
                                        </span>
                                    </div>
                                </div>

                                <div class="order-items">
                                    @foreach($pedido->items->take(3) as $item)
                                    <div class="order-item">
                                        <div class="item-image">
                                            @if($item->variacion->prenda->imagenes->count() > 0)
                                                <img src="{{ asset($item->variacion->prenda->imagenes->first()->url) }}" alt="{{ $item->nombre_prenda_snapshot }}">
                                            @else
                                                <img src="https://via.placeholder.com/80" alt="{{ $item->nombre_prenda_snapshot }}">
                                            @endif
                                        </div>
                                        <div class="item-details">
                                            <h4>{{ $item->nombre_prenda_snapshot }}</h4>
                                            <p class="item-specs">
                                                Talla: {{ $item->variacion->talla }} | Color: {{ $item->variacion->color }}
                                            </p>
                                            <p class="item-quantity">Cantidad: {{ $item->cantidad }}</p>
                                        </div>
                                        <div class="item-price">
                                            ${{ number_format($item->precio_unitario, 2) }}
                                        </div>
                                    </div>
                                    @endforeach

                                    @if($pedido->items->count() > 3)
                                    <p class="more-items">+ {{ $pedido->items->count() - 3 }} producto(s) más</p>
                                    @endif
                                </div>

                                <div class="order-footer">
                                    <div class="order-total">
                                        <span>Total:</span>
                                        <strong>${{ number_format($pedido->total_final, 2) }}</strong>
                                    </div>
                                    <div class="order-actions">
                                        <a href="{{ route('pedido.detalle', $pedido->id) }}" class="btn-view-order">Ver Detalles</a>
                                        @if($pedido->estado === 'entregado')
                                        <button class="btn-reorder">Volver a Comprar</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>


@if(session('confirmacion'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Pedido realizado con éxito!',
        text: 'Tu pedido #{{ session("pedido_id") }} ha sido registrado correctamente.',
        confirmButtonText: 'Aceptar'
    });
</script>
@endif



                        <!-- Paginación -->
                        <div class="pagination-wrapper">
                            {{ $pedidos->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <h3>No tienes pedidos aún</h3>
                            <p>Cuando realices tu primera compra, aparecerá aquí</p>
                            <a href="{{ route('product') }}" class="btn-start-shopping">Comenzar a Comprar</a>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-links">
                    <a href="#">Información</a>
                    <a href="#">Comprar</a>
                    <a href="#">Legal</a>
                </div>
                <div class="social-links">
                    <a href="#" class="social-icon">📘</a>
                    <a href="#" class="social-icon">🐦</a>
                    <a href="#" class="social-icon">📷</a>
                    <a href="#" class="social-icon">💼</a>
                </div>
            </div>
        </div>
    </footer>


</body>
</html>