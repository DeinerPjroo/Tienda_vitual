<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeLuxe - Gestión de Categorías</title>
    <link rel="stylesheet" href="{{ asset('css/gestion-usuarios.css') }}">
</head>

<body>
    <x-topbar-admin />

    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Gestión de Categorías</h1>
                <div class="stats-mini">
                    <div class="stat-item">📁 {{ $totalCategorias ?? 0 }} Categorías</div>
                </div>
            </div>
            <button class="btn-primary" onclick="openModal('create')">➕ Agregar Categoría</button>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                🔍 Búsqueda
            </div>
            <div class="filters-grid">
                <div class="filter-group" style="grid-column: 1 / -1;">
                    <label>🔎 Buscar Categoría</label>
                    <input type="text" id="searchInput" placeholder="Buscar por nombre o descripción..." 
                           value="{{ request('buscar') }}" onkeypress="if(event.key === 'Enter') aplicarFiltros()">
                </div>
            </div>

            <div class="filter-actions">
                <button class="btn-secondary" onclick="limpiarFiltros()">🔄 Limpiar</button>
                <button class="btn-primary" onclick="aplicarFiltros()">✨ Buscar</button>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>📋 Lista de Categorías</h2>
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
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Productos</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody">
                    @forelse($categorias ?? [] as $categoria)
                        <tr>
                            <td><strong>#{{ str_pad($categoria->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                            <td><strong>{{ $categoria->nombre }}</strong></td>
                            <td>{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
                            <td>{{ $categoria->prendas()->count() ?? 0 }}</td>
                            <td>{{ $categoria->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="actions">
                                    <button class="btn-icon btn-view" onclick="viewCategory({{ $categoria->id }})"
                                        title="Ver Detalles">👁️</button>
                                    <button class="btn-icon btn-edit" onclick="openModal('edit', {{ $categoria->id }})"
                                        title="Editar">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deleteCategory({{ $categoria->id }})"
                                        title="Eliminar">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">
                                <div class="empty-state-icon">📁</div>
                                <h3>No hay categorías disponibles</h3>
                                <p>Agrega categorías desde el panel de administración</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if(isset($categorias) && $categorias->hasPages())
                <div class="pagination-container" style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $categorias->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Crear/Editar Categoría -->
    <div class="modal" id="categoryModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Agregar Nueva Categoría</h2>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>

           <form id="categoryForm" method="POST" action="{{ route('admin.categorias.store') }}">
    @csrf
    <input type="hidden" id="categoryId" name="id">
    <input type="hidden" id="formMethod" name="_method" value="POST">

    <div class="form-grid">
        <div class="form-group" style="grid-column: 1 / -1;">
            <label>📁 Nombre *</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Ej: Ropa de Mujer">
        </div>

        <div class="form-group" style="grid-column: 1 / -1;">
            <label>📝 Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4" placeholder="Descripción de la categoría..."></textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
        <button type="submit" class="btn-primary">💾 Guardar Categoría</button>
    </div>
</form>
        </div>
    </div>

    <!-- Modal para Ver Detalles de la Categoría -->
    <div class="modal" id="viewCategoryModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>📁 Detalles de la Categoría</h2>
                <button class="close-modal" onclick="closeViewModal()">×</button>
            </div>

            <div class="user-details" id="categoryDetailsContent">
                <!-- Los detalles se cargarán aquí dinámicamente -->
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeViewModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Funciones del modal
        function openModal(action, id = null) {
            const modal = document.getElementById('categoryModal');
            const form = document.getElementById('categoryForm');
            const modalTitle = document.getElementById('modalTitle');
            const categoryId = document.getElementById('categoryId');
            const formMethod = document.getElementById('formMethod');

            if (action === 'create') {
                modalTitle.textContent = 'Agregar Nueva Categoría';
                form.reset();
                form.action = '{{ route("admin.categorias.store") }}';
                formMethod.value = 'POST';
                categoryId.value = '';
            } else if (action === 'edit' && id) {
                modalTitle.textContent = 'Editar Categoría';
                form.action = `{{ url('gestion-categorias') }}/${id}`;
                formMethod.value = 'PUT';
                categoryId.value = id;
                
                // Cargar datos de la categoría
                fetch(`{{ url('gestion-categorias') }}/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const cat = data.categoria;
                            document.getElementById('nombre').value = cat.nombre || '';
                            document.getElementById('descripcion').value = cat.descripcion || '';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            modal.style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }

        function viewCategory(id) {
            const modal = document.getElementById('viewCategoryModal');
            const content = document.getElementById('categoryDetailsContent');

            fetch(`{{ url('gestion-categorias') }}/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cat = data.categoria;
                        content.innerHTML = `
                            <div style="padding: 20px;">
                                <p><strong>ID:</strong> #${String(cat.id).padStart(4, '0')}</p>
                                <p><strong>Nombre:</strong> ${cat.nombre}</p>
                                <p><strong>Descripción:</strong> ${cat.descripcion || 'Sin descripción'}</p>
                                <p><strong>Fecha de Creación:</strong> ${new Date(cat.created_at).toLocaleDateString('es-ES')}</p>
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
            document.getElementById('viewCategoryModal').style.display = 'none';
        }

        function deleteCategory(id) {
            if (!confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
                return;
            }

            fetch(`{{ url('gestion-categorias') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message || 'Error al eliminar la categoría');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar la categoría');
            });
        }

        function aplicarFiltros() {
            const buscar = document.getElementById('searchInput').value;
            const url = new URL(window.location.href);
            
            if (buscar) {
                url.searchParams.set('buscar', buscar);
            } else {
                url.searchParams.delete('buscar');
            }
            
            window.location.href = url.toString();
        }

        function limpiarFiltros() {
            window.location.href = '{{ route("admin.categorias.index") }}';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const categoryModal = document.getElementById('categoryModal');
            const viewModal = document.getElementById('viewCategoryModal');
            if (event.target === categoryModal) {
                closeModal();
            }
            if (event.target === viewModal) {
                closeViewModal();
            }
        }
    </script>
</body>

</html>

