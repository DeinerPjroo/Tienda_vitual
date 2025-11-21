// ===== FUNCIONES PARA MODAL =====

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

function closeModal() {
    const modal = document.getElementById('productModal');
    modal.style.display = 'none';
    document.getElementById('productForm').reset();
}

// Cerrar modal al hacer click fuera
window.onclick = function(event) {
    const modal = document.getElementById('productModal');
    if (event.target === modal) {
        closeModal();
    }
}

// ===== VER PRODUCTO =====

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
                
                // Construir contenido
                let imagenesHtml = '';
                if (producto.imagenes && producto.imagenes.length > 0) {
                    imagenesHtml = producto.imagenes.map(img => {
                        // Verificar si la URL es externa o local
                        let imageUrl = img.url || '';
                        if (imageUrl && !imageUrl.startsWith('http://') && !imageUrl.startsWith('https://')) {
                            // Si es una URL local, agregar el prefijo /storage/
                            imageUrl = '/storage/' + imageUrl;
                        }
                        if (!imageUrl) {
                            return '';
                        }
                        return `<div style="flex: 0 0 auto; margin: 5px;">
                            <img src="${imageUrl}" alt="${img.texto_alternativo || producto.nombre}" 
                                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; cursor: pointer; display: block;" 
                                 onerror="this.style.display='none';"
                                 onclick="window.open('${imageUrl}', '_blank')"
                                 title="Click para ver imagen completa">
                        </div>`;
                    }).filter(html => html !== '').join('');
                    
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

function closeViewModal() {
    const modal = document.getElementById('viewProductModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// ===== ELIMINAR PRODUCTO =====

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

// ===== LIMPIAR FILTROS =====

function limpiarFiltros() {
    window.location.href = '/gestion-productos';
}

// ===== APLICAR FILTROS (ya no es necesario si usas el formulario) =====

function aplicarFiltros() {
    document.getElementById('filtersForm')?.submit();
}

// ===== BÚSQUEDA EN TIEMPO REAL =====

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                document.getElementById('filtersForm')?.submit();
            }, 500);
        });
    }
    
    // Auto-submit al cambiar filtros
    document.querySelectorAll('#filterCategoria, #filterStock, #filterPrecio').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filtersForm')?.submit();
        });
    });
});