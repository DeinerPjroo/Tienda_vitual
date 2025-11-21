<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Gestión de Productos</title>
    <link rel="stylesheet" href="{{ asset('css/gestion-productos.css') }}">
</head>

<body>
    <x-topbar-admin />

    <!-- Mensajes de éxito/error -->
        @if (session('success'))
            <div class="alert alert-success"
                style="background: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 8px; border-left: 4px solid #28a745;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger"
                style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 8px; border-left: 4px solid #dc3545;">
                ❌ {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger"
                style="background: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 8px; border-left: 4px solid #dc3545;">
                <strong>Errores de validación:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <!-- Main Content -->
    <div class="container">
        <div class="page-header">
            <h1>Gestión de Productos</h1>
            <button class="btn-primary" onclick="openModal('create')">➕ Agregar Producto</button>
        </div>

        <!-- Filters Section -->
        <form method="GET" action="{{ route('gestion.productos') }}" id="filtersForm">
            <div class="filters-section">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label>Buscar</label>
                        <input type="text" name="buscar" id="searchInput" placeholder="Buscar productos..."
                            value="{{ request('buscar') }}">
                    </div>

                    <div class="filter-group">
                        <label>Categoría</label>
                        <select name="categoria" id="filterCategoria">
                            <option value="">Todas las categorías</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Stock</label>
                        <select name="stock" id="filterStock">
                            <option value="">Todos</option>
                            <option value="disponible" {{ request('stock') == 'disponible' ? 'selected' : '' }}>
                                Disponible (>10)</option>
                            <option value="bajo" {{ request('stock') == 'bajo' ? 'selected' : '' }}>Stock Bajo (1-10)
                            </option>
                            <option value="agotado" {{ request('stock') == 'agotado' ? 'selected' : '' }}>Agotado (0)
                            </option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Precio</label>
                        <select name="precio" id="filterPrecio">
                            <option value="">Todos los precios</option>
                            <option value="0-50000" {{ request('precio') == '0-50000' ? 'selected' : '' }}>$0 - $50.000
                            </option>
                            <option value="50000-100000" {{ request('precio') == '50000-100000' ? 'selected' : '' }}>
                                $50.000 - $100.000</option>
                            <option value="100000-200000" {{ request('precio') == '100000-200000' ? 'selected' : '' }}>
                                $100.000 - $200.000</option>
                            <option value="200000+" {{ request('precio') == '200000+' ? 'selected' : '' }}>$200.000+
                            </option>
                        </select>
                    </div>
                </div>

                <div class="filter-actions">
                    <a href="{{ route('gestion.productos') }}" class="btn-secondary">Limpiar Filtros</a>
                    <button type="submit" class="btn-primary">Aplicar Filtros</button>
                </div>
            </div>
        </form>

        <!-- Products Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>Lista de Productos</h2>
                <div class="table-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Buscar productos..." id="searchInput">
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vista</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>SKU</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @forelse($productos as $producto)
                        <tr>
                            <td>#{{ $producto->id }}</td>
                            <td>
                                @if ($producto->imagen_url)
                                    <img src="{{ asset($producto->imagen_url) }}" alt="{{ $producto->nombre }}"
                                        class="product-image">
                                @else
                                    <div
                                        style="width: 60px; height: 60px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                        👕
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="product-name">{{ $producto->nombre }}</div>
                                <div class="product-description">{{ Str::limit($producto->descripcion_corta, 50) }}
                                </div>
                            </td>
                            <td><span class="category-badge">{{ $producto->categoria_nombre }}</span></td>
                            <td>{{ $producto->sku }}</td>
                            <td><span class="price">${{ number_format($producto->precio, 0) }}</span></td>
                            <td>
                                @php
                                    $stock = $producto->stock_total ?? 0;
                                @endphp
                                @if ($stock > 10)
                                    <span class="stock-badge stock-high">En Stock ({{ $stock }})</span>
                                @elseif($stock > 0)
                                    <span class="stock-badge stock-medium">Stock Bajo ({{ $stock }})</span>
                                @else
                                    <span class="stock-badge stock-low">Agotado (0)</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <button class="btn-icon btn-view" title="Ver"
                                        onclick="viewProduct({{ $producto->id }})">👁️</button>
                                    <button class="btn-icon btn-edit" onclick="openModal('edit', {{ $producto->id }})"
                                        title="Editar">✏️</button>
                                    <button class="btn-icon btn-delete" onclick="deleteProduct({{ $producto->id }})"
                                        title="Eliminar">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <div class="empty-state-icon">📦</div>
                                <h3>No hay productos disponibles</h3>
                                <p>Los productos se mostrarán aquí</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <button disabled>← Anterior</button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button>Siguiente →</button>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar Producto -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Agregar Nuevo Producto</h2>
                <button class="close-modal" onclick="closeModal()">×</button>
            </div>

            <form id="productForm" action="{{ route('gestion.productos') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="productId" name="id">
                <input type="hidden" id="methodField" name="_method" value="">

                <div class="form-group">
                    <label>Nombre del Producto *</label>
                    <input type="text" id="nombre" name="nombre" required
                        placeholder="Ej: Vestido Elegante de Noche">
                </div>

                <div class="form-group">
                    <label>Descripción Corta</label>
                    <input type="text" id="descripcion_corta" name="descripcion_corta"
                        placeholder="Breve descripción" maxlength="255">
                </div>

                <div class="form-group">
                    <label>Descripción Completa</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Describe el producto en detalle..."></textarea>
                </div>

                <div class="form-group">
                    <label>Precio *</label>
                    <input type="number" id="precio" name="precio" step="0.01" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Descuento (%)</label>
                    <input type="number" id="descuento" name="descuento" min="0" max="100"
                        placeholder="0">
                </div>

                <div class="form-group">
                    <label>Stock *</label>
                    <input type="number" id="stock" name="stock" required placeholder="0" min="0">
                </div>

                <div class="form-group">
                    <label>Categoría *</label>
                    <select id="categoria" name="categoria_id" required>
                        <option value="">Seleccionar categoría</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Talla</label>
                    <select id="talla" name="talla">
                        <option value="">Seleccionar talla</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                        <option value="Única">Única</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Color</label>
                    <input type="text" id="color" name="color" placeholder="Ej: Negro, Blanco, Azul">
                </div>

                <div class="form-group">
                    <label>SKU (opcional)</label>
                    <input type="text" id="sku" name="sku"
                        placeholder="Se generará automáticamente si se deja vacío">
                </div>

                <div class="form-group">
                    <label>Imagen</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                    <small style="color: #666;">Formatos: JPG, PNG, WEBP (Máx: 2MB)</small>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/gestion-productos.js') }}"></script>
</body>

</html>
