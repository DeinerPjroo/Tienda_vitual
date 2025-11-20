<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Direcciones - BeLuxe</title>
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
                        <a href="{{ route('pedidos') }}" class="sidebar-link">Historial de Pedidos</a>
                        <a href="{{ route('direcciones') }}" class="sidebar-link active">Direcciones</a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="sidebar-link logout">Cerrar Sesión</button>
                        </form>
                    </nav>
                </aside>

                <!-- Content Area -->
                <section class="content-area">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="content-header">
                        <h1 class="content-title">Mis Direcciones</h1>
                        <button class="btn-add-address" onclick="showAddressModal()">
                            + Nueva Dirección
                        </button>
                    </div>

                    @if($direcciones->count() > 0)
                        <div class="addresses-grid">
                            @foreach($direcciones as $direccion)
                            <div class="address-card {{ $direccion->predeterminada ? 'default' : '' }}">
                                @if($direccion->predeterminada)
                                    <span class="default-badge">Predeterminada</span>
                                @endif

                                <div class="address-content">
                                    <h3 class="address-name">{{ $direccion->nombre_completo }}</h3>
                                    <p class="address-details">
                                        {{ $direccion->direccion_linea1 }}<br>
                                        @if($direccion->direccion_linea2)
                                            {{ $direccion->direccion_linea2 }}<br>
                                        @endif
                                        {{ $direccion->ciudad }}, {{ $direccion->departamento }}<br>
                                        {{ $direccion->codigo_postal }}, {{ $direccion->pais }}
                                    </p>
                                    <p class="address-phone">📱 {{ $direccion->telefono }}</p>
                                </div>

                                <div class="address-actions">
                                    <button class="btn-edit" onclick="editAddress({{ $direccion->id }})">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Editar
                                    </button>
                                    <form method="POST" action="{{ route('direccion.eliminar', $direccion->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" onclick="return confirm('¿Eliminar esta dirección?')">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                    @if(!$direccion->predeterminada)
                                    <form method="POST" action="{{ route('direccion.predeterminar', $direccion->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-set-default">
                                            Establecer como predeterminada
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <h3>No tienes direcciones guardadas</h3>
                            <p>Agrega una dirección para facilitar tus compras</p>
                            <button class="btn-start-shopping" onclick="showAddressModal()">Agregar Dirección</button>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>

    <!-- Modal para Agregar/Editar Dirección -->
    <div id="addressModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nueva Dirección</h2>
                <button class="modal-close" onclick="closeAddressModal()">&times;</button>
            </div>
            <form method="POST" action="{{ route('direccion.guardar') }}" id="addressForm">
                @csrf
                <input type="hidden" name="id" id="direccion_id">

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre_completo">Nombre Completo</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="direccion_linea1">Dirección Línea 1</label>
                    <input type="text" id="direccion_linea1" name="direccion_linea1" placeholder="Calle, número" required>
                </div>

                <div class="form-group">
                    <label for="direccion_linea2">Dirección Línea 2 (Opcional)</label>
                    <input type="text" id="direccion_linea2" name="direccion_linea2" placeholder="Apartamento, suite, edificio">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="ciudad">Ciudad</label>
                        <input type="text" id="ciudad" name="ciudad" required>
                    </div>
                    <div class="form-group">
                        <label for="departamento">Departamento</label>
                        <input type="text" id="departamento" name="departamento" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="codigo_postal">Código Postal</label>
                        <input type="text" id="codigo_postal" name="codigo_postal" required>
                    </div>
                    <div class="form-group">
                        <label for="pais">País</label>
                        <input type="text" id="pais" name="pais" value="Colombia" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="predeterminada" id="predeterminada">
                        <span>Establecer como dirección predeterminada</span>
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddressModal()">Cancelar</button>
                    <button type="submit" class="btn-submit">Guardar Dirección</button>
                </div>
            </form>
        </div>
    </div>

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

    <script>
        function showAddressModal() {
            document.getElementById('addressModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Nueva Dirección';
            document.getElementById('addressForm').reset();
            document.getElementById('direccion_id').value = '';
        }

        function closeAddressModal() {
            document.getElementById('addressModal').style.display = 'none';
        }

        function editAddress(id) {
            // Aquí cargarías los datos de la dirección con AJAX
            document.getElementById('addressModal').style.display = 'flex';
            document.getElementById('modalTitle').textContent = 'Editar Dirección';
            document.getElementById('direccion_id').value = id;
            // Cargar datos...
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('addressModal');
            if (event.target == modal) {
                closeAddressModal();
            }
        }
    </script>
</body>
</html>