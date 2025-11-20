<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeLuxe - Gestión de Productos</title>
    <link rel="stylesheet" href="{{ asset('css/gestion-productos.css') }}">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <a class="logo" href="/homeadmin">
                <img src="{{ asset('images/beluxe-logo.png') }}" alt="BeLuxe Logo" class="logo-image">
            </a>

            <nav>
                <a href="/gestion-productos" class="active">Gestión de Productos</a>
                <a href="/gestion-clientes">Gestión de Clientes</a> 
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
            <h1>Gestión de Productos</h1>
            <button class="btn-primary" onclick="openModal('create')">➕ Agregar Producto</button>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>Categoría</label>
                    <select id="filterCategoria">
                        <option value="">Todas las categorías</option>
                        <option value="camisetas">Camisetas y Tops</option>
                        <option value="pantalones">Pantalones</option>
                        <option value="vestidos">Vestidos y Faldas</option>
                        <option value="abrigos">Abrigos y Chaquetas</option>
                        <option value="calzado">Calzado</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Género</label>
                    <select id="filterGenero">
                        <option value="">Todos</option>
                        <option value="mujer">Mujer</option>
                        <option value="hombre">Hombre</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Stock</label>
                    <select id="filterStock">
                        <option value="">Todos</option>
                        <option value="disponible">Disponible (>10)</option>
                        <option value="bajo">Stock Bajo (<10)</option>
                        <option value="agotado">Agotado (0)</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Precio</label>
                    <select id="filterPrecio">
                        <option value="">Todos los precios</option>
                        <option value="0-50">$0 - $50.000</option>
                        <option value="50-100">$50.000 - $100.000</option>
                        <option value="100-200">$100.000 - $200.000</option>
                        <option value="200+">$200.000+</option>
                    </select>
                </div>
            </div>

            <div class="filter-actions">
                <button class="btn-secondary" onclick="limpiarFiltros()">Limpiar Filtros</button>
                <button class="btn-primary" onclick="aplicarFiltros()">Aplicar Filtros</button>
            </div>
        </div>

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
                        <th>Género</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @forelse($productos ?? [] as $producto)
                    <tr>
                        <td>{{ $producto->id }}</td>
                        <td>
                            <img src="{{ $producto->imagen ? asset($producto->imagen) : 'https://via.placeholder.com/60' }}" 
                                 alt="{{ $producto->nombre }}" 
                                 class="product-image">
                        </td>
                        <td>
                            <div class="product-name">{{ $producto->nombre }}</div>
                            <div class="product-description">{{ Str::limit($producto->descripcion, 50) }}</div>
                        </td>
                        <td><span class="category-badge">{{ $producto->categoria }}</span></td>
                        <td>{{ ucfirst($producto->genero) }}</td>
                        <td><span class="price">${{ number_format($producto->precio, 2) }}</span></td>
                        <td>
                            @if($producto->stock > 10)
                                <span class="stock-badge stock-high">En Stock ({{ $producto->stock }})</span>
                            @elseif($producto->stock > 0)
                                <span class="stock-badge stock-medium">Stock Bajo ({{ $producto->stock }})</span>
                            @else
                                <span class="stock-badge stock-low">Agotado (0)</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                <button class="btn-icon btn-view" title="Ver" onclick="viewProduct({{ $producto->id }})">👁️</button>
                                <button class="btn-icon btn-edit" onclick="openModal('edit', {{ $producto->id }})" title="Editar">✏️</button>
                                <button class="btn-icon btn-delete" onclick="deleteProduct({{ $producto->id }})" title="Eliminar">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-state-icon">📦</div>
                            <h3>No hay productos disponibles</h3>
                            <p>Agrega productos desde el panel de administración</p>
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

            <form id="productForm"  method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="productId" name="id">
                
                <div class="form-group">
                    <label>Nombre del Producto *</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej: Vestido Elegante de Noche">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="descripcion" name="descripcion" placeholder="Describe el producto..."></textarea>
                </div>

                <div class="form-group">
                    <label>Precio *</label>
                    <input type="number" id="precio" name="precio" step="0.01" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label>Stock *</label>
                    <input type="number" id="stock" name="stock" required placeholder="0">
                </div>

                <div class="form-group">
                    <label>Imagen</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*">
                </div>

                <div class="form-group">
                    <label>Categoría *</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Seleccionar categoría</option>
                        <option value="Camisetas y Tops">Camisetas y Tops</option>
                        <option value="Pantalones">Pantalones</option>
                        <option value="Vestidos y Faldas">Vestidos y Faldas</option>
                        <option value="Abrigos y Chaquetas">Abrigos y Chaquetas</option>
                        <option value="Calzado">Calzado</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Género *</label>
                    <select id="genero" name="genero" required>
                        <option value="">Seleccionar género</option>
                        <option value="mujer">Mujer</option>
                        <option value="hombre">Hombre</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Talla</label>
                    <select id="talla" name="talla">
                        <option value="">Seleccionar talla</option>
                        <option value="xs">XS</option>
                        <option value="s">S</option>
                        <option value="m">M</option>
                        <option value="l">L</option>
                        <option value="xl">XL</option>
                        <option value="xxl">XXL</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Color</label>
                    <input type="text" id="color" name="color" placeholder="Ej: Negro, Blanco, Azul">
                </div>

                <div class="form-group">
                    <label>Material</label>
                    <input type="text" id="material" name="material" placeholder="Ej: Algodón, Poliéster, Cuero">
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