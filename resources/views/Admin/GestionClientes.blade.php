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
    <x-topbar-admin />

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
                    <select name="rol" id="filterRol">
                        <option value="">Todos los roles</option>
                        <option value="admin" {{ request('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="cliente" {{ request('rol') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>📅 Fecha de Registro</label>
                    <select name="fecha" id="filterFecha">
                        <option value="">Todas las fechas</option>
                        <option value="hoy" {{ request('fecha') == 'hoy' ? 'selected' : '' }}>Hoy</option>
                        <option value="semana" {{ request('fecha') == 'semana' ? 'selected' : '' }}>Esta semana</option>
                        <option value="mes" {{ request('fecha') == 'mes' ? 'selected' : '' }}>Este mes</option>
                        <option value="año" {{ request('fecha') == 'año' ? 'selected' : '' }}>Este año</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>🔒 Estado de Cuenta</label>
                    <select name="estado" id="filterEstado">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo
                        </option>
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
                        <th>Apellido</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Teléfono</th>
                        <th>Fecha Nacimiento</th>
                        <th>Estado</th>
                        <th>Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @forelse($usuarios ?? [] as $usuario)
                        <tr>
                            <td><strong>#{{ str_pad($usuario->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                            <td>{{ $usuario->nombre }}</td>
                            <td>{{ $usuario->apellido }}</td>
                            <td class="email-cell">{{ $usuario->correo }}</td>
                           <td>
    @if ($usuario->rol && ($usuario->rol->nombre === 'Administrador' || $usuario->rol_id == 1))
        <span class="role-badge role-admin">👨‍💼 Administrador</span>
    @else
        <span class="role-badge role-client">👤 Cliente</span>
    @endif
</td>
                            <td>{{ $usuario->telefono ?? 'N/A' }}</td>
                            <td>{{ $usuario->fecha_nacimiento ? \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td>
                                @if ($usuario->activo)
                                    <span style="color: #28a745; font-weight: 600;">✓ Activo</span>
                                @else
                                    <span style="color: #dc3545; font-weight: 600;">✗ Inactivo</span>
                                @endif
                            </td>
                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="actions">
                                    <button class="btn-icon btn-view" onclick="viewUser({{ $usuario->id }})"
                                        title="Ver Detalles">👁️</button>
                                    <button class="btn-icon btn-edit" onclick="openModal('edit', {{ $usuario->id }})"
                                        title="Editar">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deleteUser({{ $usuario->id }})"
                                        title="Eliminar">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="empty-state">
                                <div class="empty-state-icon">👥</div>
                                <h3>No hay usuarios disponibles</h3>
                                <p>Agrega usuarios desde el panel de administración</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if(isset($usuarios) && $usuarios->hasPages())
                <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Crear/Editar Usuario -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Agregar Nuevo Usuario</h2>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>

           <form id="userForm" method="POST" action="{{ route('admin.usuarios.store') }}">
    @csrf
    <input type="hidden" id="userId" name="id">
    <input type="hidden" id="formMethod" name="_method" value="POST">

    <div class="form-grid">
        <div class="form-group">
            <label>👤 Nombre *</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ej: Juan">
        </div>

        <div class="form-group">
            <label>👤 Apellido *</label>
            <input type="text" id="apellido" name="apellido" placeholder="Ej: Pérez García">
        </div>

        <div class="form-group">
            <label>📧 Email *</label>
            <input type="email" id="correo" name="correo" required placeholder="correo@ejemplo.com">
        </div>

        <div class="form-group">
            <label>🔒 Contraseña <span id="passwordRequired">*</span></label>
            <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres">
            <small id="passwordHint" style="color: #666; font-size: 0.85rem;"></small>
        </div>

        <div class="form-group">
            <label>👨‍💼 Rol *</label>
            <select id="rol_id" name="rol_id" required>
                <option value="">Seleccionar rol</option>
                @foreach ($roles ?? [] as $rol)
                    <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>📱 Teléfono</label>
            <input type="tel" id="telefono" name="telefono" placeholder="+57 300 123 4567">
        </div>

        <div class="form-group">
            <label>🎂 Fecha de Nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
        </div>

        <div class="form-group">
            <label>📊 Estado</label>
            <select id="activo" name="activo">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
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
