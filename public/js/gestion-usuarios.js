// ==================== MODAL FUNCTIONS ====================

/**
 * Abre el modal para crear o editar un usuario
 * @param {string} mode - 'create' o 'edit'
 * @param {number|null} userId - ID del usuario (solo para editar)
 */
function openModal(mode, userId = null) {
    const modal = document.getElementById('userModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('userForm');
    const passwordField = document.getElementById('password');
    
    if (mode === 'create') {
        modalTitle.textContent = 'Agregar Nuevo Usuario';
        form.reset();
        form.action = '/usuarios/store';
        document.getElementById('userId').value = '';
        document.getElementById('formMethod').value = 'POST';
        passwordField.required = true;
        passwordField.placeholder = 'Mínimo 8 caracteres';
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Editar Usuario';
        form.action = '/usuarios/update';
        document.getElementById('formMethod').value = 'PUT';
        passwordField.required = false;
        passwordField.placeholder = 'Dejar en blanco para mantener la contraseña actual';
        // Cargar datos del usuario
        loadUserData(userId);
    }
    
    modal.classList.add('active');
}

/**
 * Cierra el modal
 */
function closeModal() {
    const modal = document.getElementById('userModal');
    modal.classList.remove('active');
}

/**
 * Cierra el modal de vista de detalles
 */
function closeViewModal() {
    const modal = document.getElementById('viewUserModal');
    modal.classList.remove('active');
}

/**
 * Carga los datos de un usuario para edición
 * @param {number} userId - ID del usuario
 */
function loadUserData(userId) {
    fetch(`/usuarios/${userId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('userId').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('email').value = data.email;
            document.getElementById('rol_id').value = data.rol_id;
            document.getElementById('telefono').value = data.telefono || '';
            document.getElementById('direccion').value = data.direccion || '';
            document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento || '';
            document.getElementById('estado').value = data.estado || 'activo';
        })
        .catch(error => {
            console.error('Error al cargar el usuario:', error);
            alert('Error al cargar los datos del usuario');
        });
}

// ==================== USER FUNCTIONS ====================

/**
 * Ver detalles completos de un usuario
 * @param {number} userId - ID del usuario
 */
function viewUser(userId) {
    fetch(`/usuarios/${userId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('viewUserModal');
            const content = document.getElementById('userDetailsContent');
            
            content.innerHTML = `
                <div class="user-details">
                    <div class="detail-row">
                        <div class="detail-label">👤 ID:</div>
                        <div class="detail-value"><strong>#${String(data.id).padStart(4, '0')}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">👤 Nombre:</div>
                        <div class="detail-value">${data.nombre}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📧 Email:</div>
                        <div class="detail-value">${data.email}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">👨‍💼 Rol:</div>
                        <div class="detail-value">
                            <span class="role-badge ${data.rol && data.rol.nombre === 'admin' ? 'role-admin' : 'role-client'}">
                                ${data.rol ? (data.rol.nombre === 'admin' ? '👨‍💼 Administrador' : '👤 Cliente') : 'No asignado'}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📱 Teléfono:</div>
                        <div class="detail-value">${data.telefono || 'No especificado'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📍 Dirección:</div>
                        <div class="detail-value">${data.direccion || 'No especificada'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">🎂 Fecha Nacimiento:</div>
                        <div class="detail-value">${data.fecha_nacimiento ? new Date(data.fecha_nacimiento).toLocaleDateString('es-CO') : 'No especificada'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📅 Fecha Registro:</div>
                        <div class="detail-value">${new Date(data.created_at).toLocaleDateString('es-CO')}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">🔄 Última Actualización:</div>
                        <div class="detail-value">${new Date(data.updated_at).toLocaleDateString('es-CO')}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">✅ Email Verificado:</div>
                        <div class="detail-value">
                            ${data.email_verified_at ? 
                                '<span style="color: #28a745; font-weight: 600;">✓ Verificado</span>' : 
                                '<span style="color: #dc3545; font-weight: 600;">✗ No verificado</span>'}
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📊 Estado:</div>
                        <div class="detail-value">
                            <span style="color: ${data.estado === 'activo' ? '#28a745' : data.estado === 'bloqueado' ? '#dc3545' : '#ffc107'}; font-weight: 600;">
                                ${data.estado ? data.estado.charAt(0).toUpperCase() + data.estado.slice(1) : 'Activo'}
                            </span>
                        </div>
                    </div>
                </div>
            `;
            
            modal.classList.add('active');
        })
        .catch(error => {
            console.error('Error al cargar el usuario:', error);
            alert('Error al cargar los detalles del usuario');
        });
}

/**
 * Eliminar un usuario
 * @param {number} userId - ID del usuario
 */
function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?\n\nEsta acción no se puede deshacer.')) {
        fetch(`/usuarios/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Usuario eliminado exitosamente');
                location.reload();
            } else {
                alert('Error al eliminar el usuario: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el usuario');
        });
    }
}

// ==================== FILTER FUNCTIONS ====================

/**
 * Limpia todos los filtros
 */
function limpiarFiltros() {
    document.getElementById('filterRol').value = '';
    document.getElementById('filterFecha').value = '';
    document.getElementById('filterEmail').value = '';
    document.getElementById('filterEstado').value = '';
    aplicarFiltros();
}

/**
 * Aplica los filtros seleccionados
 */
function aplicarFiltros() {
    const rol = document.getElementById('filterRol').value;
    const fecha = document.getElementById('filterFecha').value;
    const email = document.getElementById('filterEmail').value;
    const estado = document.getElementById('filterEstado').value;
    
    const params = new URLSearchParams();
    if (rol) params.append('rol', rol);
    if (fecha) params.append('fecha', fecha);
    if (email) params.append('email', email);
    if (estado) params.append('estado', estado);
    
    window.location.href = `/gestion-usuarios?${params.toString()}`;
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
                if (searchTerm.length >= 2 || searchTerm.length === 0) {
                    window.location.href = `/gestion-usuarios?search=${searchTerm}`;
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
    const userForm = document.getElementById('userForm');
    
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const actionUrl = this.action;
            
            // Validar email
            const email = document.getElementById('email').value;
            if (!validateEmail(email)) {
                alert('Por favor, ingresa un email válido');
                return;
            }
            
            // Validar contraseña si es creación
            const password = document.getElementById('password').value;
            const userId = document.getElementById('userId').value;
            if (!userId && password.length < 8) {
                alert('La contraseña debe tener al menos 8 caracteres');
                return;
            }
            
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
                    alert('Usuario guardado exitosamente');
                    closeModal();
                    location.reload();
                } else {
                    alert('Error al guardar el usuario: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar el usuario');
            });
        });
    }
});

// ==================== UTILITY FUNCTIONS ====================

/**
 * Valida formato de email
 * @param {string} email - Email a validar
 * @returns {boolean}
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Valida formato de teléfono colombiano
 * @param {string} phone - Teléfono a validar
 * @returns {boolean}
 */
function validatePhone(phone) {
    if (!phone) return true; // Opcional
    const re = /^(\+57)?[0-9]{10}$/;
    return re.test(phone.replace(/\s/g, ''));
}

// ==================== MODAL CLOSE ON OUTSIDE CLICK ====================

/**
 * Cierra el modal al hacer clic fuera de él
 */
window.onclick = function(event) {
    const userModal = document.getElementById('userModal');
    const viewModal = document.getElementById('viewUserModal');
    
    if (event.target === userModal) {
        closeModal();
    }
    
    if (event.target === viewModal) {
        closeViewModal();
    }
}

// ==================== KEYBOARD SHORTCUTS ====================

/**
 * Atajos de teclado
 */
document.addEventListener('keydown', function(e) {
    // Escape para cerrar modales
    if (e.key === 'Escape') {
        closeModal();
        closeViewModal();
    }
    
    // Ctrl/Cmd + K para enfocar búsqueda
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
    }
});