<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeLuxe - Gestión de Ventas</title>
    <link rel="stylesheet" href="{{ asset('css/gestion-usuarios.css') }}">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .stat-card .value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 0;
        }
        .estado-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }
        .estado-pendiente { background: #fff3cd; color: #856404; }
        .estado-pagado { background: #d1ecf1; color: #0c5460; }
        .estado-procesando { background: #d4edda; color: #155724; }
        .estado-enviado { background: #cce5ff; color: #004085; }
        .estado-entregado { background: #d4edda; color: #155724; }
        .estado-cancelado { background: #f8d7da; color: #721c24; }
        .estado-reembolsado { background: #e2e3e5; color: #383d41; }
    </style>
</head>

<body>
    <x-topbar-admin />

    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <div>
                <h1>💰 Gestión de Ventas</h1>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>📊 Total Ventas</h3>
                <p class="value">{{ number_format($estadisticas['total_ventas'] ?? 0, 0) }}</p>
            </div>
            <div class="stat-card success">
                <h3>💰 Ingresos Totales</h3>
                <p class="value">${{ number_format($estadisticas['ingresos_totales'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card info">
                <h3>📅 Ventas Hoy</h3>
                <p class="value">{{ $estadisticas['ventas_hoy'] ?? 0 }}</p>
            </div>
            <div class="stat-card info">
                <h3>💵 Ingresos Hoy</h3>
                <p class="value">${{ number_format($estadisticas['ingresos_hoy'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card">
                <h3>📆 Ventas del Mes</h3>
                <p class="value">{{ $estadisticas['ventas_mes'] ?? 0 }}</p>
            </div>
            <div class="stat-card success">
                <h3>💸 Ingresos del Mes</h3>
                <p class="value">${{ number_format($estadisticas['ingresos_mes'] ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="stat-card warning">
                <h3>⏳ Pendientes</h3>
                <p class="value">{{ $estadisticas['pendientes'] ?? 0 }}</p>
            </div>
            <div class="stat-card">
                <h3>🔄 Procesando</h3>
                <p class="value">{{ $estadisticas['procesando'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                🔍 Filtros de Búsqueda
            </div>
            <form method="GET" action="{{ route('admin.ventas.index') }}">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>📊 Estado</label>
                        <select name="estado" id="filterEstado">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            <option value="procesando" {{ request('estado') == 'procesando' ? 'selected' : '' }}>Procesando</option>
                            <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                            <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                            <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            <option value="reembolsado" {{ request('estado') == 'reembolsado' ? 'selected' : '' }}>Reembolsado</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>📅 Fecha Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>

                    <div class="filter-group">
                        <label>📅 Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    </div>

                    <div class="filter-group">
                        <label>👤 Cliente</label>
                        <input type="text" name="cliente" placeholder="Nombre o email..." value="{{ request('cliente') }}">
                    </div>

                    <div class="filter-group" style="grid-column: 1 / -1;">
                        <label>🔎 Buscar</label>
                        <input type="text" name="buscar" placeholder="ID de pedido, nombre o email..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('admin.ventas.index') }}" class="btn-secondary">🔄 Limpiar</a>
                    <button type="submit" class="btn-primary">✨ Aplicar Filtros</button>
                </div>
            </form>
        </div>

        <!-- Ventas Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>📋 Lista de Ventas</h2>
            </div>

            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('error') }}
                </div>
            @endif

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas ?? [] as $venta)
                        <tr>
                            <td><strong>#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $venta->usuario->nombre }} {{ $venta->usuario->apellido }}</strong><br>
                                    <small style="color: #666;">{{ $venta->usuario->correo }}</small>
                                </div>
                            </td>
                            <td>{{ $venta->fecha_pedido->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta->items->sum('cantidad') }} items</td>
                            <td><strong>${{ number_format($venta->total + $venta->costo_envio + $venta->impuestos, 0, ',', '.') }}</strong></td>
                            <td>
                                <span class="estado-badge estado-{{ $venta->estado }}">
                                    {{ ucfirst($venta->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-icon btn-view" onclick="viewVenta({{ $venta->id }})"
                                        title="Ver Detalles">👁️</button>
                                    <button class="btn-icon btn-edit" onclick="openEstadoModal({{ $venta->id }}, '{{ $venta->estado }}')"
                                        title="Cambiar Estado">✏️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-state-icon">💰</div>
                                <h3>No hay ventas disponibles</h3>
                                <p>Las ventas aparecerán aquí cuando se realicen pedidos</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if(isset($ventas) && $ventas->hasPages())
                <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $ventas->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Ver Detalles de la Venta -->
    <div class="modal" id="viewVentaModal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2>💰 Detalles de la Venta</h2>
                <button class="close-modal" onclick="closeViewModal()">×</button>
            </div>

            <div class="user-details" id="ventaDetailsContent">
                <!-- Los detalles se cargarán aquí dinámicamente -->
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeViewModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Cambiar Estado -->
    <div class="modal" id="estadoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✏️ Cambiar Estado de la Venta</h2>
                <button class="close-modal" onclick="closeEstadoModal()">×</button>
            </div>

            <form id="estadoForm" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>📊 Nuevo Estado</label>
                    <select id="nuevoEstado" name="estado" required>
                        <option value="pendiente">Pendiente</option>
                        <option value="pagado">Pagado</option>
                        <option value="procesando">Procesando</option>
                        <option value="enviado">Enviado</option>
                        <option value="entregado">Entregado</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="reembolsado">Reembolsado</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEstadoModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">💾 Actualizar Estado</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function viewVenta(id) {
            const modal = document.getElementById('viewVentaModal');
            const content = document.getElementById('ventaDetailsContent');

            fetch(`{{ url('gestion-ventas') }}/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const venta = data.venta;
                        let itemsHtml = '';
                        venta.items.forEach(item => {
                            itemsHtml += `
                                <tr>
                                    <td>${item.nombre_prenda_snapshot || 'Producto'}</td>
                                    <td>${item.cantidad}</td>
                                    <td>$${parseFloat(item.precio_unitario).toLocaleString('es-CO')}</td>
                                    <td>$${(parseFloat(item.precio_unitario) * item.cantidad).toLocaleString('es-CO')}</td>
                                </tr>
                            `;
                        });

                        let pagosHtml = '';
                        if (venta.pagos && venta.pagos.length > 0) {
                            venta.pagos.forEach(pago => {
                                pagosHtml += `
                                    <p><strong>Método:</strong> ${pago.metodo || 'N/A'}</p>
                                    <p><strong>Monto:</strong> $${parseFloat(pago.monto || 0).toLocaleString('es-CO')}</p>
                                    <p><strong>Estado:</strong> ${pago.estado || 'N/A'}</p>
                                `;
                            });
                        } else {
                            pagosHtml = '<p>No hay información de pago disponible</p>';
                        }

                        content.innerHTML = `
                            <div style="padding: 20px;">
                                <h3>Información del Pedido</h3>
                                <p><strong>ID:</strong> #${String(venta.id).padStart(6, '0')}</p>
                                <p><strong>Fecha:</strong> ${new Date(venta.fecha_pedido).toLocaleString('es-ES')}</p>
                                <p><strong>Estado:</strong> <span class="estado-badge estado-${venta.estado}">${venta.estado}</span></p>
                                
                                <h3 style="margin-top: 20px;">Cliente</h3>
                                <p><strong>Nombre:</strong> ${venta.usuario.nombre} ${venta.usuario.apellido || ''}</p>
                                <p><strong>Email:</strong> ${venta.usuario.correo}</p>
                                <p><strong>Teléfono:</strong> ${venta.usuario.telefono || 'N/A'}</p>
                                
                                ${venta.direccion ? `
                                <h3 style="margin-top: 20px;">Dirección de Envío</h3>
                                <p>${venta.direccion.direccion || ''}</p>
                                <p>${venta.direccion.ciudad || ''}, ${venta.direccion.departamento || ''}</p>
                                ` : ''}
                                
                                <h3 style="margin-top: 20px;">Productos</h3>
                                <table style="width: 100%; margin-top: 10px;">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unit.</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${itemsHtml}
                                    </tbody>
                                </table>
                                
                                <h3 style="margin-top: 20px;">Resumen</h3>
                                <p><strong>Subtotal:</strong> $${parseFloat(venta.total || 0).toLocaleString('es-CO')}</p>
                                <p><strong>Envío:</strong> $${parseFloat(venta.costo_envio || 0).toLocaleString('es-CO')}</p>
                                <p><strong>Impuestos:</strong> $${parseFloat(venta.impuestos || 0).toLocaleString('es-CO')}</p>
                                <p><strong style="font-size: 1.2em;">Total:</strong> $${(parseFloat(venta.total || 0) + parseFloat(venta.costo_envio || 0) + parseFloat(venta.impuestos || 0)).toLocaleString('es-CO')}</p>
                                
                                <h3 style="margin-top: 20px;">Pago</h3>
                                ${pagosHtml}
                            </div>
                        `;
                        modal.style.display = 'flex';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = '<p>Error al cargar los detalles</p>';
                });
        }

        function closeViewModal() {
            document.getElementById('viewVentaModal').style.display = 'none';
        }

        function openEstadoModal(id, estadoActual) {
            const modal = document.getElementById('estadoModal');
            const form = document.getElementById('estadoForm');
            const select = document.getElementById('nuevoEstado');

            form.action = `{{ url('gestion-ventas') }}/${id}/estado`;
            select.value = estadoActual;
            modal.style.display = 'flex';
        }

        function closeEstadoModal() {
            document.getElementById('estadoModal').style.display = 'none';
        }

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            const viewModal = document.getElementById('viewVentaModal');
            const estadoModal = document.getElementById('estadoModal');
            if (event.target === viewModal) {
                closeViewModal();
            }
            if (event.target === estadoModal) {
                closeEstadoModal();
            }
        }
    </script>
</body>

</html>

