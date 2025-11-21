/**
 * ============================================
 * GESTIÓN DE PRODUCTOS - FUNCIONES JAVASCRIPT
 * ============================================
 * 
 * Este archivo contiene todas las funciones JavaScript necesarias para:
 * - Abrir/cerrar modales de crear/editar productos
 * - Ver detalles de productos
 * - Eliminar productos
 * - Aplicar filtros y búsqueda
 * 
 * ============================================
 */

// ===== FUNCIONES PARA MODAL =====

/**
 * Abre el modal para crear o editar un producto
 * 
 * @param {string} mode - Modo del modal: 'create' para crear, 'edit' para editar
 * @param {number|null} productId - ID del producto a editar (solo necesario en modo 'edit')
 * 
 * Funcionalidad:
 * - Si mode es 'create': Limpia el formulario y lo prepara para crear un nuevo producto
 * - Si mode es 'edit': Carga los datos del producto desde el servidor y los muestra en el formulario
 */
function openModal(mode, productId = null) {
    const modal = document.getElementById('productModal');
    const form = document.getElementById('productForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');
    
    modal.style.display = 'flex';
    
    if (mode === 'create') {
        modalTitle.textContent = 'Agregar Nuevo Producto';
        form.reset();
        form.action = '/gestion-productos';
        
        // Asegurarse de que sea POST para crear
        if (methodField) {
            methodField.value = '';
        }
        
        // Limpiar el ID del producto
        document.getElementById('productId').value = '';
        
    } else if (mode === 'edit' && productId) {
        modalTitle.textContent = 'Editar Producto';
        
        // Cargar datos del producto
        fetch(`/gestion-productos/${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const producto = data.producto;
                    
                    // Llenar el formulario
                    document.getElementById('productId').value = producto.id;
                    document.getElementById('nombre').value = producto.nombre;
                    document.getElementById('descripcion_corta').value = producto.descripcion_corta || '';
                    document.getElementById('descripcion').value = producto.descripcion || '';
                    document.getElementById('precio').value = producto.precio;
                    document.getElementById('descuento').value = producto.descuento || 0;
                    document.getElementById('categoria').value = producto.categoria_id;
                    document.getElementById('sku').value = producto.sku;
                    
                    // Stock de la primera variación
                    if (producto.variaciones && producto.variaciones.length > 0) {
                        document.getElementById('stock').value = producto.variaciones[0].stock;
                        document.getElementById('color').value = producto.variaciones[0].color;
                        document.getElementById('talla').value = producto.variaciones[0].talla;
                    }
                    
                    // Configurar formulario para actualizar
                    form.action = `/gestion-productos/${productId}`;
                    
                    // Establecer método PUT
                    if (methodField) {
                        methodField.value = 'PUT';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar el producto');
            });
    }
}

/**
 * Cierra el modal de crear/editar producto
 * 
 * Resetea el formulario para limpiar todos los campos
 */
function closeModal() {
    const modal = document.getElementById('productModal');
    modal.style.display = 'none';
    document.getElementById('productForm').reset();
}

/**
 * Cierra el modal cuando se hace click fuera de él
 * 
 * Detecta si el click fue en el fondo del modal (no en el contenido)
 * y lo cierra automáticamente para mejorar la UX
 */
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeModal();
    }
}

// ===== VER PRODUCTO =====

/**
 * Muestra un modal con los detalles completos de un producto
 * 
 * @param {number} productId - ID del producto a mostrar
 * 
 * Proceso:
 * 1. Hace una petición AJAX al servidor para obtener los datos del producto
 * 2. Construye dinámicamente el HTML del modal con:
 *    - Información general (nombre, precio, SKU, etc.)
 *    - Imágenes del producto (con manejo de rutas locales/externas)
 *    - Variaciones (colores, tallas, stock)
 * 3. Muestra el modal con toda la información
 * 
 * Nota: Las imágenes se muestran con ruta /storage/ para imágenes locales
 */
function viewProduct(productId) {
    fetch(`/gestion-productos/${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const producto = data.producto;
                
                // Crear modal de vista
                let viewModal = document.getElementById('viewProductModal');
                
                if (!viewModal) {
                    viewModal = document.createElement('div');
                    viewModal.id = 'viewProductModal';
                    viewModal.className = 'modal';
                    document.body.appendChild(viewModal);
                }
                
                // Construir el HTML para las imágenes del producto
                let imagenesHtml = '';
                if (producto.imagenes && producto.imagenes.length > 0) {
                    imagenesHtml = producto.imagenes.map(img => {
                        // Verificar si la URL es externa (http/https) o local
                        let imageUrl = img.url || '';
                        if (imageUrl && !imageUrl.startsWith('http://') && !imageUrl.startsWith('https://')) {
                            // Si es una URL local, agregar el prefijo /storage/
                            // Las imágenes se guardan en storage/app/public/productos/
                            imageUrl = '/storage/' + imageUrl;
                        }
                        if (!imageUrl) {
                            return '';
                        }
                        // Crear un contenedor para cada imagen con estilo y funcionalidad de click
                        return `<div style="flex: 0 0 auto; margin: 5px;">
                            <img src="${imageUrl}" alt="${img.texto_alternativo || producto.nombre}" 
                                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; cursor: pointer; display: block;" 
                                 onerror="this.style.display='none';"
                                 onclick="window.open('${imageUrl}', '_blank')"
                                 title="Click para ver imagen completa">
                        </div>`;
                    }).filter(html => html !== '').join(''); // Filtrar imágenes vacías
                    
                    if (!imagenesHtml) {
                        imagenesHtml = '<p>Sin imágenes disponibles</p>';
                    }
                } else {
                    imagenesHtml = '<p>Sin imágenes</p>';
                }
                
                let variacionesHtml = '';
                if (producto.variaciones && producto.variaciones.length > 0) {
                    variacionesHtml = producto.variaciones.map(v => 
                        `<tr>
                            <td>${v.color}</td>
                            <td>${v.talla}</td>
                            <td>${v.stock}</td>
                            <td>$${Number(v.precio_adicional).toLocaleString()}</td>
                        </tr>`
                    ).join('');
                } else {
                    variacionesHtml = '<tr><td colspan="4">Sin variaciones</td></tr>';
                }
                
                viewModal.innerHTML = `
                    <div class="modal-content" style="max-width: 800px;">
                        <div class="modal-header">
                            <h2>Detalles del Producto</h2>
                            <button class="close-modal" onclick="closeViewModal()">×</button>
                        </div>
                        
                        <div style="padding: 20px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div>
                                    <h3>Información General</h3>
                                    <p><strong>ID:</strong> #${producto.id}</p>
                                    <p><strong>Nombre:</strong> ${producto.nombre}</p>
                                    <p><strong>SKU:</strong> ${producto.sku}</p>
                                    <p><strong>Categoría:</strong> ${producto.categoria?.nombre || 'Sin categoría'}</p>
                                    <p><strong>Precio:</strong> $${Number(producto.precio).toLocaleString()}</p>
                                    <p><strong>Descuento:</strong> ${producto.descuento}%</p>
                                    <p><strong>Descripción Corta:</strong> ${producto.descripcion_corta || 'N/A'}</p>
                                    <p><strong>Descripción:</strong> ${producto.descripcion || 'N/A'}</p>
                                    <p><strong>Estado:</strong> ${producto.activo ? '✅ Activo' : '❌ Inactivo'}</p>
                                </div>
                                
                                <div>
                                    <h3>Imágenes</h3>
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                                        ${imagenesHtml}
                                    </div>
                                </div>
                            </div>
                            
                            <div style="margin-top: 30px;">
                                <h3>Variaciones</h3>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #f5f5f5;">
                                            <th style="padding: 10px; text-align: left;">Color</th>
                                            <th style="padding: 10px; text-align: left;">Talla</th>
                                            <th style="padding: 10px; text-align: left;">Stock</th>
                                            <th style="padding: 10px; text-align: left;">Precio Adicional</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${variacionesHtml}
                                    </tbody>
                                </table>
                            </div>
                            
                            <div style="margin-top: 20px; text-align: right;">
                                <button class="btn-primary" onclick="closeViewModal()">Cerrar</button>
                            </div>
                        </div>
                    </div>
                `;
                
                viewModal.style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar el producto');
        });
}

/**
 * Cierra el modal de detalles del producto
 */
function closeViewModal() {
    const modal = document.getElementById('viewProductModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// ===== ELIMINAR PRODUCTO =====

/**
 * Elimina un producto después de confirmar con el usuario
 * 
 * @param {number} productId - ID del producto a eliminar
 * 
 * Proceso:
 * 1. Muestra un diálogo de confirmación
 * 2. Si el usuario confirma, envía una petición DELETE al servidor
 * 3. Recarga la página si la eliminación fue exitosa
 * 4. Muestra un mensaje de error si algo falla
 * 
 * IMPORTANTE: Esta acción es irreversible
 */
function deleteProduct(productId) {
    if (!confirm('¿Estás seguro de que deseas eliminar este producto? Esta acción no se puede deshacer.')) {
        return;
    }
    
    fetch(`/gestion-productos/${productId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || 
                           document.querySelector('input[name="_token"]')?.value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Producto eliminado exitosamente');
            location.reload();
        } else {
            alert('Error al eliminar el producto: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el producto');
    });
}

// ===== FILTROS Y BÚSQUEDA =====

/**
 * Limpia todos los filtros y recarga la página
 * 
 * Redirige a la URL base sin parámetros de filtrado
 */
function limpiarFiltros() {
    window.location.href = '/gestion-productos';
}

/**
 * Aplica los filtros del formulario
 * 
 * Envía el formulario de filtros al servidor para aplicar los filtros seleccionados
 */
function aplicarFiltros() {
    document.getElementById('filtersForm')?.submit();
}

// ===== BÚSQUEDA EN TIEMPO REAL =====

/**
 * Inicializa los eventos de búsqueda y filtrado cuando la página carga
 * 
 * Funcionalidades:
 * 1. Búsqueda con debounce: Espera 500ms después de que el usuario deje de escribir
 *    antes de enviar la búsqueda (evita demasiadas peticiones al servidor)
 * 2. Auto-submit de filtros: Cuando se cambia un filtro (categoría, stock, precio),
 *    se envía automáticamente el formulario
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    // Configurar búsqueda con debounce (espera 500ms después de escribir)
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            // Limpiar el timeout anterior si existe
            clearTimeout(this.searchTimeout);
            // Crear un nuevo timeout que enviará el formulario después de 500ms
            this.searchTimeout = setTimeout(() => {
                document.getElementById('filtersForm')?.submit();
            }, 500);
        });
    }
    
    // Auto-submit al cambiar cualquier filtro (categoría, stock, precio)
    // Esto mejora la UX: no necesitas hacer click en "Aplicar Filtros"
    document.querySelectorAll('#filterCategoria, #filterStock, #filterPrecio').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filtersForm')?.submit();
        });
    });
});