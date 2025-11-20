<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeLuxe - Gestión de Usuarios</title>
    <link rel="stylesheet" href="{{ asset('css/gestion-usuarios.css') }}">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <a class="logo" href="/homeadmin">
                <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe Logo" class="logo-image">
            </a>

            <nav>
                <a href="/gestion-productos">Gestión de Productos</a>
                <a href="/gestion-usuarios" class="active">Gestión de Usuarios</a>
                <a href="#">Ventas</a>
                <a href="#">Envíos</a>
            </nav>

            <div class="header-actions">
                <a href="/carrito" class="header-link">🛒 Pedidos</a>
                <a href="/cuenta" class="header-link">👤 Cuenta</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Gestión de Usuarios</h1>
                <div class="stats-mini">
                    <div class="stat-item">👥 {{ $totalUsuarios ?? 0 }} Usuarios</div>
                    <div class="stat-item">👨‍💼 {{ $totalAdmins ?? 0 }} Administradores</div>
                </div>
            </div>
            <button class="btn-primary" onclick="openModal('create')">➕ Agregar Usuario</button>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                🔍 Filtros de Búsqueda
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label>👤 Rol</label>
                    <select id="filterRol">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="cliente">Cliente</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>📅 Fecha de Registro</label>
                    <select id="filterFecha">
                        <option value="">Todas las fechas</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
                        <option value="año">Este año</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>📧 Estado de Email</label>
                    <select id="filterEmail">
                        <option value="">Todos</option>
                        <option value="verificado">Verificado</option>
                        <option value="no_verificado">No Verificado</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>🔒 Estado de Cuenta</label>
                    <select id="filterEstado">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                        <option value="bloqueado">Bloqueado</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button class="btn-secondary" onclick="limpiarFiltros()">🔄 Limpiar Filtros</button>
                <button class="btn-primary" onclick="aplicarFiltros()">✨ Aplicar Filtros</button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>📋 Lista de Usuarios</h2>
                <div class="table-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Buscar por nombre o email..." id="searchInput">
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Fecha Nacimiento</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @forelse($usuarios ?? [] as $usuario)
                    <tr>
                        <td><strong>#{{ str_pad($usuario->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="user-name">{{ $usuario->nombre }}</div>
                                    @if($usuario->email_verified_at)
                                        <span class="verified-badge">✓ Verificado</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="email-cell">{{ $usuario->email }}</td>
                        <td>
                            @if($usuario->rol && $usuario->rol->nombre === 'admin')
                                <span class="role-badge role-admin">👨‍💼 Administrador</span>
                            @else
                                <span class="role-badge role-client">👤 Cliente</span>
                            @endif
                        </td>
                        <td>{{ $usuario->telefono ?? 'N/A' }}</td>
                        <td>
                            <div class="address-cell">{{ Str::limit($usuario->direccion ?? 'No especificada', 30) }}</div>
                        </td>
                        <td>{{ $usuario->fecha_nacimiento ? \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}</td>
                        <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon btn-view" onclick="viewUser({{ $usuario->id }})" title="Ver Detalles">👁️</button>
                                <button class="btn-icon btn-edit" onclick="openModal('edit', {{ $usuario->id }})" title="Editar">✏️</button>
                                <button class="btn-icon btn-delete" onclick="deleteUser({{ $usuario->id }})" title="Eliminar">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="empty-state">
                            <div class="empty-state-icon">👥</div>
                            <h3>No hay usuarios disponibles</h3>
                            <p>Agrega usuarios desde el panel de administración</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Crear/Editar Usuario -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Agregar Nuevo Usuario</h2>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>

            <form id="userForm"  method="POST">
                @csrf
                <input type="hidden" id="userId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="POST">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>👤 Nombre Completo *</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Juan Pérez García">
                    </div>

                    <div class="form-group">
                        <label>📧 Email *</label>
                        <input type="email" id="email" name="email" required placeholder="correo@ejemplo.com">
                    </div>

                    <div class="form-group">
                        <label>🔒 Contraseña *</label>
                        <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres">
                    </div>

                    <div class="form-group">
                        <label>👨‍💼 Rol *</label>
                        <select id="rol_id" name="rol_id" required>
                            <option value="">Seleccionar rol</option>
                            @foreach($roles ?? [] as $rol)
                                <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>📱 Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="+57 300 123 4567">
                    </div>

                    <div class="form-group full-width">
                        <label>📍 Dirección</label>
                        <input type="text" id="direccion" name="direccion" placeholder="Calle 123 #45-67, Ciudad">
                    </div>

                    <div class="form-group">
                        <label>🎂 Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                    </div>

                    <div class="form-group">
                        <label>📊 Estado de Cuenta</label>
                        <select id="estado" name="estado">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="bloqueado">Bloqueado</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">💾 Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Ver Detalles del Usuario -->
    <div class="modal" id="viewUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>👤 Detalles del Usuario</h2>
                <button class="close-modal" onclick="closeViewModal()">×</button>
            </div>

            <div class="user-details" id="userDetailsContent">
                <!-- Los detalles se cargarán aquí dinámicamente -->
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeViewModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/gestion-usuarios.js') }}"></script>
</body>
</html>