// ==================== MODAL FUNCTIONS ====================

/**
 * Abre el modal para crear o editar un producto
 * @param {string} mode - 'create' o 'edit'
 * @param {number|null} productId - ID del producto (solo para editar)
 */
function openModal(mode, productId = null) {
    const modal = document.getElementById('productModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('productForm');
    
    if (mode === 'create') {
        modalTitle.textContent = 'Agregar Nuevo Producto';
        form.reset();
        form.action = '/productos/store';
        document.getElementById('productId').value = '';
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Editar Producto';
        form.action = '/productos/update';
        // Cargar datos del producto mediante AJAX
        loadProductData(productId);
    }
    
    modal.classList.add('active');
}

/**
 * Cierra el modal
 */
function closeModal() {
    const modal = document.getElementById('productModal');
    modal.classList.remove('active');
}

/**
 * Carga los datos de un producto para edición
 * @param {number} productId - ID del producto
 */
function loadProductData(productId) {
    fetch(`/productos/${productId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('productId').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('descripcion').value = data.descripcion;
            document.getElementById('precio').value = data.precio;
            document.getElementById('stock').value = data.stock;
            document.getElementById('categoria').value = data.categoria;
            document.getElementById('genero').value = data.genero;
            document.getElementById('talla').value = data.talla || '';
            document.getElementById('color').value = data.color || '';
            document.getElementById('material').value = data.material || '';
        })
        .catch(error => {
            console.error('Error al cargar el producto:', error);
            alert('Error al cargar los datos del producto');
        });
}

// ==================== PRODUCT FUNCTIONS ====================

/**
 * Ver detalles de un producto
 * @param {number} productId - ID del producto
 */
function viewProduct(productId) {
    window.location.href = `/productos/${productId}`;
}

/**
 * Eliminar un producto
 * @param {number} productId - ID del producto
 */
function deleteProduct(productId) {
    if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        fetch(`/productos/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Producto eliminado exitosamente');
                location.reload();
            } else {
                alert('Error al eliminar el producto');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el producto');
        });
    }
}

// ==================== FILTER FUNCTIONS ====================

/**
 * Limpia todos los filtros
 */
function limpiarFiltros() {
    document.getElementById('filterCategoria').value = '';
    document.getElementById('filterGenero').value = '';
    document.getElementById('filterStock').value = '';
    document.getElementById('filterPrecio').value = '';
    aplicarFiltros();
}

/**
 * Aplica los filtros seleccionados
 */
function aplicarFiltros() {
    const categoria = document.getElementById('filterCategoria').value;
    const genero = document.getElementById('filterGenero').value;
    const stock = document.getElementById('filterStock').value;
    const precio = document.getElementById('filterPrecio').value;
    
    const params = new URLSearchParams();
    if (categoria) params.append('categoria', categoria);
    if (genero) params.append('genero', genero);
    if (stock) params.append('stock', stock);
    if (precio) params.append('precio', precio);
    
    window.location.href = `/gestion-productos?${params.toString()}`;
}

// ==================== SEARCH FUNCTION ====================

/**
 * Búsqueda en tiempo real
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.toLowerCase();
            
            searchTimeout = setTimeout(() => {
                if (searchTerm.length >= 3 || searchTerm.length === 0) {
                    window.location.href = `/gestion-productos?search=${searchTerm}`;
                }
            }, 500);
        });
    }
});

// ==================== FORM SUBMISSION ====================

/**
 * Manejo del envío del formulario
 */
document.addEventListener('DOMContentLoaded', function() {
    const productForm = document.getElementById('productForm');
    
    if (productForm) {
        productForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Producto guardado exitosamente');
                    closeModal();
                    location.reload();
                } else {
                    alert('Error al guardar el producto: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el producto');
            });
        });
    }
});

// ==================== MODAL CLOSE ON OUTSIDE CLICK ====================

/**
 * Cierra el modal al hacer clic fuera de él
 */
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeModal();
    }
}

// ==================== IMAGE PREVIEW ====================

/**
 * Vista previa de imagen al seleccionar archivo
 */
document.addEventListener('DOMContentLoaded', function() {
    const imagenInput = document.getElementById('imagen');
    
    if (imagenInput && imagenInput.type === 'file') {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Aquí podrías mostrar una vista previa de la imagen
                    console.log('Imagen cargada:', file.name);
                };
                reader.readAsDataURL(file);
            }
        });
    }
});