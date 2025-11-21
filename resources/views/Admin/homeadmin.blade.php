<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Panel de Administración</title>
    <link rel="stylesheet" href="{{ asset('css/homeadmin.blade.css') }}">
    
</head>
<body>
    <x-topbar-admin />

    <!-- Mensajes de éxito/error -->
    @if (session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 8px; border-left: 4px solid #28a745; text-align: center;">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 8px; border-left: 4px solid #dc3545; text-align: center;">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="dashboard-container">
        <!-- Welcome Header -->
        <div class="welcome-header">
            <h1 id="saludo">🌅 Buenos días, {{ $usuario->nombre }}!</h1>
            <p id="reloj">🕒 {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <!-- Estadísticas Principales -->
        <div class="stats-grid">
            <div class="stat-card ventas">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Ventas del Día</p>
                        <p class="stat-card-value">${{ number_format($estadisticas['ventas_hoy'], 0, ',', '.') }}</p>
                    </div>
                    <div class="stat-card-icon">💰</div>
                </div>
                <p class="stat-card-change">Este mes: ${{ number_format($estadisticas['ventas_mes'], 0, ',', '.') }}</p>
            </div>

            <div class="stat-card pedidos">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Pedidos Pendientes</p>
                        <p class="stat-card-value">{{ $estadisticas['pedidos_pendientes'] }}</p>
                    </div>
                    <div class="stat-card-icon">📦</div>
                </div>
                <p class="stat-card-change">Hoy: {{ $estadisticas['pedidos_hoy'] }} pedidos</p>
            </div>

            <div class="stat-card productos">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Productos Activos</p>
                        <p class="stat-card-value">{{ $estadisticas['productos_activos'] }}</p>
                    </div>
                    <div class="stat-card-icon">👗</div>
                </div>
                <p class="stat-card-change">Total: {{ $estadisticas['total_productos'] }} productos</p>
            </div>

            <div class="stat-card clientes">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Total Clientes</p>
                        <p class="stat-card-value">{{ $estadisticas['total_clientes'] }}</p>
                    </div>
                    <div class="stat-card-icon">👥</div>
                </div>
                <p class="stat-card-change">Nuevos este mes: {{ $estadisticas['nuevos_clientes_mes'] }}</p>
            </div>
        </div>

        <!-- Gráfico de Ventas -->
        <div class="chart-container">
            <h3 style="color: var(--beluxe-purple); margin: 0 0 20px 0;">📈 Ventas Últimos 7 Días</h3>
            <div class="chart-bars">
                @foreach($ventas_ultimos_7_dias as $dia)
                    @php
                        $maxVenta = max(array_column($ventas_ultimos_7_dias, 'ventas'));
                        $altura = $maxVenta > 0 ? ($dia['ventas'] / $maxVenta) * 100 : 0;
                    @endphp
                    <div class="chart-bar" style="height: {{ max($altura, 5) }}%;">
                        <span class="chart-value">${{ number_format($dia['ventas'] / 1000, 0) }}k</span>
                        <span class="chart-label">{{ $dia['fecha'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Secciones de Información -->
        <div class="sections-grid">
            <!-- Pedidos Recientes -->
            <div class="section-card">
                <h3>📋 Pedidos Recientes</h3>
                @if($pedidos_recientes->count() > 0)
                    <div>
                        @foreach($pedidos_recientes as $pedido)
                            <div class="pedido-item">
                                <div class="pedido-info">
                                    <div class="pedido-cliente">
                                        {{ $pedido->usuario->nombre }} {{ $pedido->usuario->apellido }}
                                    </div>
                                    <div class="pedido-fecha">
                                        {{ $pedido->fecha_pedido->format('d/m/Y H:i') }}
                                    </div>
                                </div>
                                <div style="text-align: right; margin-right: 15px;">
                                    <div class="pedido-total">${{ number_format($pedido->total, 0, ',', '.') }}</div>
                                    <span class="pedido-estado estado-{{ $pedido->estado }}">
                                        {{ ucfirst($pedido->estado) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div style="margin-top: 15px; text-align: center;">
                        <a href="{{ route('admin.ventas.index') }}" style="color: var(--beluxe-purple); text-decoration: none; font-weight: 600;">
                            Ver todos los pedidos →
                        </a>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        <p>No hay pedidos recientes</p>
                    </div>
                @endif
            </div>

            <!-- Productos Más Vendidos -->
            <div class="section-card">
                <h3>🏆 Productos Más Vendidos (30 días)</h3>
                @if($productos_mas_vendidos->count() > 0)
                    <div>
                        @foreach($productos_mas_vendidos as $producto)
                            <div class="producto-item">
                                <div class="producto-nombre">{{ $producto->nombre }}</div>
                                <div class="producto-stats">
                                    <div class="producto-cantidad">{{ $producto->total_vendido }} unidades</div>
                                    <div class="producto-ventas">${{ number_format($producto->total_ingresos, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">📦</div>
                        <p>No hay ventas registradas</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Estadísticas Adicionales -->
        <div class="stats-grid" style="margin-top: 20px;">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Stock Total</p>
                        <p class="stat-card-value">{{ number_format($estadisticas['stock_total'], 0, ',', '.') }}</p>
                    </div>
                    <div class="stat-card-icon">📊</div>
                </div>
                <p class="stat-card-change" style="color: #ef4444;">
                    ⚠️ {{ $estadisticas['productos_bajo_stock'] }} productos con stock bajo
                </p>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Categorías</p>
                        <p class="stat-card-value">{{ $estadisticas['total_categorias'] }}</p>
                    </div>
                    <div class="stat-card-icon">📁</div>
                </div>
                <p class="stat-card-change">{{ $estadisticas['total_variaciones'] }} variaciones</p>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Pedidos del Mes</p>
                        <p class="stat-card-value">{{ $estadisticas['pedidos_mes'] }}</p>
                    </div>
                    <div class="stat-card-icon">📈</div>
                </div>
                <p class="stat-card-change">
                    Procesando: {{ $estadisticas['pedidos_procesando'] }} | 
                    Enviados: {{ $estadisticas['pedidos_enviados'] }}
                </p>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <p class="stat-card-title">Ventas del Año</p>
                        <p class="stat-card-value">${{ number_format($estadisticas['ventas_anio'] / 1000000, 1, ',', '.') }}M</p>
                    </div>
                    <div class="stat-card-icon">💎</div>
                </div>
                <p class="stat-card-change">Total acumulado</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 BeLuxe | Panel de Administración</p>
    </footer>

    <!-- Script para actualizar reloj -->
    <script>
        function actualizarReloj() {
            const ahora = new Date();
            const fecha = ahora.toLocaleDateString('es-CO', { 
                day: '2-digit', 
                month: '2-digit', 
                year: 'numeric' 
            });
            const hora = ahora.toLocaleTimeString('es-CO', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit' 
            });
            document.getElementById("reloj").textContent = `🕒 ${fecha} ${hora}`;
        }

        function obtenerSaludo() {
            const hora = new Date().getHours();
            let saludo = "🌅 Buenos días";
            if (hora >= 12 && hora < 18) {
                saludo = "🌞 Buenas tardes";
            } else if (hora >= 18) {
                saludo = "🌙 Buenas noches";
            }
            document.getElementById("saludo").textContent = `${saludo}, {{ $usuario->nombre }}!`;
        }

        obtenerSaludo();
        actualizarReloj();
        setInterval(actualizarReloj, 1000);
    </script>
</body>
</html>
