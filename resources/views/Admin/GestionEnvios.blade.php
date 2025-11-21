<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeLuxe - Gestión de Envíos</title>
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
        .stat-card.urgent {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
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
        .estado-pagado { background: #d1ecf1; color: #0c5460; }
        .estado-procesando { background: #d4edda; color: #155724; }
        .estado-enviado { background: #cce5ff; color: #004085; }
        .estado-entregado { background: #d4edda; color: #155724; }
        .accion-rapida {
            display: inline-flex;
            gap: 5px;
            margin-left: 5px;
        }
        .btn-small {
            padding: 5px 10px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <x-topbar-admin />

    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <div>
                <h1>📦 Gestión de Envíos</h1>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card info">
                <h3>📋 Listos para Enviar</h3>
                <p class="value">{{ $estadisticas['listos_para_enviar'] ?? 0 }}</p>
            </div>
            <div class="stat-card">
                <h3>🔄 En Proceso</h3>
                <p class="value">{{ $estadisticas['en_proceso'] ?? 0 }}</p>
            </div>
            <div class="stat-card success">
                <h3>🚚 Enviados</h3>
                <p class="value">{{ $estadisticas['enviados'] ?? 0 }}</p>
            </div>
            <div class="stat-card success">
                <h3>✅ Entregados (Mes)</h3>
                <p class="value">{{ $estadisticas['entregados_mes'] ?? 0 }}</p>
            </div>
            <div class="stat-card urgent">
                <h3>⚠️ Urgentes</h3>
                <p class="value">{{ $estadisticas['pendientes_urgentes'] ?? 0 }}</p>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                🔍 Filtros de Búsqueda
            </div>
            <form method="GET" action="{{ route('admin.envios.index') }}">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>📊 Estado</label>
                        <select name="estado" id="filterEstado">
                            <option value="">Todos los estados</option>
                            <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado (Listo para enviar)</option>
                            <option value="procesando" {{ request('estado') == 'procesando' ? 'selected' : '' }}>Procesando</option>
                            <option value="enviado" {{ request('estado') == 'enviado' ? 'selected' : '' }}>Enviado</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>🏙️ Ciudad</label>
                        <input type="text" name="ciudad" placeholder="Ciudad de destino..." value="{{ request('ciudad') }}">
                    </div>

                    <div class="filter-group">
                        <label>📅 Fecha Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>

                    <div class="filter-group">
                        <label>📅 Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    </div>

                    <div class="filter-group" style="grid-column: 1 / -1;">
                        <label>🔎 Buscar</label>
                        <input type="text" name="buscar" placeholder="ID de pedido, cliente o dirección..." value="{{ request('buscar') }}">
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('admin.envios.index') }}" class="btn-secondary">🔄 Limpiar</a>
                    <button type="submit" class="btn-primary">✨ Aplicar Filtros</button>
                </div>
            </form>
        </div>

        <!-- Envíos Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>📋 Lista de Envíos</h2>
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
                        <th>Dirección de Envío</th>
                        <th>Fecha Pedido</th>
                        <th>Items</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($envios ?? [] as $envio)
                        <tr>
                            <td><strong>#{{ str_pad($envio->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>
                                <div>
                                    <strong>{{ $envio->usuario->nombre }} {{ $envio->usuario->apellido }}</strong><br>
                                    <small style="color: #666;">{{ $envio->usuario->correo }}</small><br>
                                    <small style="color: #666;">📞 {{ $envio->usuario->telefono ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                @if($envio->direccion)
                                    <div style="font-size: 0.9rem;">
                                        <strong>{{ $envio->direccion->nombre_completo ?? 'N/A' }}</strong><br>
                                        {{ $envio->direccion->direccion_linea1 ?? '' }}<br>
                                        @if($envio->direccion->direccion_linea2)
                                            {{ $envio->direccion->direccion_linea2 }}<br>
                                        @endif
                                        {{ $envio->direccion->ciudad ?? '' }}, {{ $envio->direccion->departamento ?? '' }}<br>
                                        <small>📮 {{ $envio->direccion->codigo_postal ?? 'N/A' }}</small>
                                    </div>
                                @else
                                    <span style="color: #dc3545;">Sin dirección</span>
                                @endif
                            </td>
                            <td>{{ $envio->fecha_pedido->format('d/m/Y') }}<br><small>{{ $envio->fecha_pedido->diffForHumans() }}</small></td>
                            <td>{{ $envio->items->sum('cantidad') }} items</td>
                            <td>
                                <span class="estado-badge estado-{{ $envio->estado }}">
                                    {{ ucfirst($envio->estado) }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-icon btn-view" onclick="viewEnvio({{ $envio->id }})"
                                        title="Ver Detalles">👁️</button>
                                    @if($envio->estado == 'pagado' || $envio->estado == 'procesando')
                                        <button class="btn-icon btn-edit" onclick="openEnviarModal({{ $envio->id }})"
                                            title="Marcar como Enviado">📦</button>
                                    @endif
                                    @if($envio->estado == 'enviado')
                                        <button class="btn-icon btn-edit" onclick="marcarEntregado({{ $envio->id }})"
                                            title="Marcar como Entregado">✅</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <h3>No hay envíos disponibles</h3>
                                <p>Los envíos aparecerán aquí cuando haya pedidos listos para enviar</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if(isset($envios) && $envios->hasPages())
                <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $envios->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Ver Detalles del Envío -->
    <div class="modal" id="viewEnvioModal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h2>📦 Detalles del Envío</h2>
                <button class="close-modal" onclick="closeViewModal()">×</button>
            </div>

            <div class="user-details" id="envioDetailsContent">
                <!-- Los detalles se cargarán aquí dinámicamente -->
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeViewModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Marcar como Enviado -->
    <div class="modal" id="enviarModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>📦 Marcar como Enviado</h2>
                <button class="close-modal" onclick="closeEnviarModal()">×</button>
            </div>

            <form id="enviarForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>📋 Guía de Envío</label>
                    <input type="text" id="guia_envio" name="guia_envio" placeholder="Número de guía...">
                </div>

                <div class="form-group">
                    <label>🚚 Transportadora</label>
                    <input type="text" id="transportadora" name="transportadora" placeholder="Nombre de la transportadora...">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEnviarModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">📦 Marcar como Enviado</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function viewEnvio(id) {
            const modal = document.getElementById('viewEnvioModal');
            const content = document.getElementById('envioDetailsContent');

            fetch(`{{ url('gestion-envios') }}/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const envio = data.envio;
                        let itemsHtml = '';
                        envio.items.forEach(item => {
                            itemsHtml += `
                                <tr>
                                    <td>${item.nombre_prenda_snapshot || 'Producto'}</td>
                                    <td>${item.cantidad}</td>
                                    <td>$${parseFloat(item.precio_unitario).toLocaleString('es-CO')}</td>
                                </tr>
                            `;
                        });

                        const direccion = envio.direccion || {};
                        content.innerHTML = `
                            <div style="padding: 20px;">
                                <h3>Información del Pedido</h3>
                                <p><strong>ID:</strong> #${String(envio.id).padStart(6, '0')}</p>
                                <p><strong>Fecha:</strong> ${new Date(envio.fecha_pedido).toLocaleString('es-ES')}</p>
                                <p><strong>Estado:</strong> <span class="estado-badge estado-${envio.estado}">${envio.estado}</span></p>
                                
                                <h3 style="margin-top: 20px;">Cliente</h3>
                                <p><strong>Nombre:</strong> ${envio.usuario.nombre} ${envio.usuario.apellido || ''}</p>
                                <p><strong>Email:</strong> ${envio.usuario.correo}</p>
                                <p><strong>Teléfono:</strong> ${envio.usuario.telefono || 'N/A'}</p>
                                
                                <h3 style="margin-top: 20px;">Dirección de Envío</h3>
                                <p><strong>Nombre:</strong> ${direccion.nombre_completo || 'N/A'}</p>
                                <p><strong>Dirección:</strong> ${direccion.direccion_linea1 || ''} ${direccion.direccion_linea2 || ''}</p>
                                <p><strong>Ciudad:</strong> ${direccion.ciudad || 'N/A'}, ${direccion.departamento || 'N/A'}</p>
                                <p><strong>Código Postal:</strong> ${direccion.codigo_postal || 'N/A'}</p>
                                <p><strong>País:</strong> ${direccion.pais || 'N/A'}</p>
                                
                                <h3 style="margin-top: 20px;">Productos</h3>
                                <table style="width: 100%; margin-top: 10px;">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unit.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${itemsHtml}
                                    </tbody>
                                </table>
                                
                                <h3 style="margin-top: 20px;">Resumen</h3>
                                <p><strong>Subtotal:</strong> $${parseFloat(envio.total || 0).toLocaleString('es-CO')}</p>
                                <p><strong>Envío:</strong> $${parseFloat(envio.costo_envio || 0).toLocaleString('es-CO')}</p>
                                <p><strong>Total:</strong> $${(parseFloat(envio.total || 0) + parseFloat(envio.costo_envio || 0) + parseFloat(envio.impuestos || 0)).toLocaleString('es-CO')}</p>
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
            document.getElementById('viewEnvioModal').style.display = 'none';
        }

        function openEnviarModal(id) {
            const modal = document.getElementById('enviarModal');
            const form = document.getElementById('enviarForm');

            form.action = `{{ url('gestion-envios') }}/${id}/marcar-enviado`;
            document.getElementById('guia_envio').value = '';
            document.getElementById('transportadora').value = '';
            modal.style.display = 'flex';
        }

        function closeEnviarModal() {
            document.getElementById('enviarModal').style.display = 'none';
        }

        function marcarEntregado(id) {
            if (!confirm('¿Estás seguro de que deseas marcar este pedido como entregado?')) {
                return;
            }

            fetch(`{{ url('gestion-envios') }}/${id}/marcar-entregado`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || response.ok) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al marcar como entregado');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                location.reload(); // Recargar de todas formas
            });
        }

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            const viewModal = document.getElementById('viewEnvioModal');
            const enviarModal = document.getElementById('enviarModal');
            if (event.target === viewModal) {
                closeViewModal();
            }
            if (event.target === enviarModal) {
                closeEnviarModal();
            }
        }
    </script>
</body>

</html>

