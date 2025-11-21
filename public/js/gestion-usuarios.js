// ==================== MODAL FUNCTIONS ====================

function openModal(mode, userId = null) {
    const modal = document.getElementById('userModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('userForm');
    const passwordField = document.getElementById('password');
    
    if (mode === 'create') {
        modalTitle.textContent = 'Agregar Nuevo Usuario';
        form.reset();
        form.action = '/admin/usuarios';
        document.getElementById('userId').value = '';
        document.getElementById('formMethod').value = 'POST';
        passwordField.required = true;
        passwordField.placeholder = 'Mínimo 8 caracteres';
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Editar Usuario';
        document.getElementById('formMethod').value = 'PUT';
        passwordField.required = false;
        passwordField.placeholder = 'Dejar en blanco para mantener la contraseña actual';
        loadUserData(userId);
    }
    
    modal.classList.add('active');
}

function closeModal() {
    const modal = document.getElementById('userModal');
    modal.classList.remove('active');
}

function closeViewModal() {
    const modal = document.getElementById('viewUserModal');
    modal.classList.remove('active');
}

function loadUserData(userId) {
    fetch(`/admin/usuarios/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const usuario = data.usuario;
                document.getElementById('userId').value = usuario.id;
                document.getElementById('nombre').value = usuario.nombre;
                document.getElementById('apellido').value = usuario.apellido || '';
                document.getElementById('correo').value = usuario.correo;
                document.getElementById('rol_id').value = usuario.rol_id;
                document.getElementById('telefono').value = usuario.telefono || '';
                document.getElementById('fecha_nacimiento').value = usuario.fecha_nacimiento || '';
                document.getElementById('activo').value = usuario.activo ? '1' : '0';
                
                // Actualizar action del form
                const form = document.getElementById('userForm');
                form.action = `/admin/usuarios/${usuario.id}`;
            }
        })
        .catch(error => {
            console.error('Error al cargar el usuario:', error);
            alert('Error al cargar los datos del usuario');
        });
}

function viewUser(userId) {
    fetch(`/admin/usuarios/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const usuario = data.usuario;
                const modal = document.getElementById('viewUserModal');
                const content = document.getElementById('userDetailsContent');
                
                content.innerHTML = `
                    <div class="detail-row">
                        <div class="detail-label">👤 ID:</div>
                        <div class="detail-value"><strong>#${String(usuario.id).padStart(4, '0')}</strong></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">👤 Nombre:</div>
                        <div class="detail-value">${usuario.nombre} ${usuario.apellido || ''}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📧 Email:</div>
                        <div class="detail-value">${usuario.correo}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">👨‍💼 Rol:</div>
                        <div class="detail-value">
                            <span class="role-badge ${usuario.rol && usuario.rol.nombre === 'Administrador' ? 'role-admin' : 'role-client'}">
                                ${usuario.rol ? (usuario.rol.nombre === 'Administrador' ? '👨‍💼 Administrador' : '👤 Cliente') : 'No asignado'}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📱 Teléfono:</div>
                        <div class="detail-value">${usuario.telefono || 'No especificado'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">🎂 Fecha Nacimiento:</div>
                        <div class="detail-value">${usuario.fecha_nacimiento ? new Date(usuario.fecha_nacimiento).toLocaleDateString('es-CO') : 'No especificada'}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📅 Fecha Registro:</div>
                        <div class="detail-value">${new Date(usuario.created_at).toLocaleDateString('es-CO')}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">📊 Estado:</div>
                        <div class="detail-value">
                            <span style="color: ${usuario.activo ? '#28a745' : '#dc3545'}; font-weight: 600;">
                                ${usuario.activo ? '✓ Activo' : '✗ Inactivo'}
                            </span>
                        </div>
                    </div>
                `;
                
                modal.classList.add('active');
            }
        })
        .catch(error => {
            console.error('Error al cargar el usuario:', error);
            alert('Error al cargar los detalles del usuario');
        });
}

function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?\n\nEsta acción no se puede deshacer.')) {
        fetch(`/admin/usuarios/${userId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Usuario eliminado exitosamente');
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error al eliminar el usuario');
        });
    }
}

// ==================== FILTER FUNCTIONS ====================

function limpiarFiltros() {
    window.location.href = '/admin/usuarios';
}

function aplicarFiltros() {
    const rol = document.getElementById('filterRol').value;
    const fecha = document.getElementById('filterFecha').value;
    const estado = document.getElementById('filterEstado').value;
    
    const params = new URLSearchParams();
    if (rol) params.append('rol', rol);
    if (fecha) params.append('fecha', fecha);
    if (estado) params.append('estado', estado);
    
    window.location.href = `/admin/usuarios?${params.toString()}`;
}

// ==================== SEARCH FUNCTION ====================

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.toLowerCase();
            
            searchTimeout = setTimeout(() => {
                if (searchTerm.length >= 2 || searchTerm.length === 0) {
                    window.location.href = `/admin/usuarios?buscar=${searchTerm}`;
                }
            }, 500);
        });
    }
});

// ==================== FORM SUBMISSION ====================

document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const method = document.getElementById('formMethod').value;
            
            // Validar correo
            const correo = document.getElementById('correo').value;
            if (!validateEmail(correo)) {
                alert('⚠️ Por favor, ingresa un correo electrónico válido');
                return;
            }
            
            // Validar contraseña si es creación
            const password = document.getElementById('password').value;
            const userId = document.getElementById('userId').value;
            if (!userId && password.length < 8) {
                alert('⚠️ La contraseña debe tener al menos 8 caracteres');
                return;
            }
            
            const actionUrl = this.action;
            
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success === false) {
                    alert('❌ ' + (data.message || 'Error al guardar el usuario'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Si hay redirección, no mostrar error
                if (!error.message.includes('redirect')) {
                    alert('❌ Error al guardar el usuario');
                }
            });
        });
    }
});

// ==================== UTILITY FUNCTIONS ====================

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// ==================== MODAL CLOSE ON OUTSIDE CLICK ====================

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

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
        closeViewModal();
    }
    
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput')?.focus();
    }
});

// Actualizar hints de contraseña según el modo
function updatePasswordHints(mode) {
    const passwordRequired = document.getElementById('passwordRequired');
    const passwordHint = document.getElementById('passwordHint');
    const passwordField = document.getElementById('password');
    
    if (mode === 'create') {
        passwordRequired.style.display = 'inline';
        passwordField.required = true;
        passwordHint.textContent = 'La contraseña es obligatoria';
    } else {
        passwordRequired.style.display = 'none';
        passwordField.required = false;
        passwordHint.textContent = 'Dejar en blanco para mantener la contraseña actual';
    }
}

// Actualizar la función openModal
function openModal(mode, userId = null) {
    const modal = document.getElementById('userModal');
    const modalTitle = document.getElementById('modalTitle');
    const form = document.getElementById('userForm');
    
    if (mode === 'create') {
        modalTitle.textContent = 'Agregar Nuevo Usuario';
        form.reset();
        form.action = '/admin/usuarios';
        document.getElementById('userId').value = '';
        document.getElementById('formMethod').value = 'POST';
        updatePasswordHints('create');
    } else if (mode === 'edit') {
        modalTitle.textContent = 'Editar Usuario';
        document.getElementById('formMethod').value = 'PUT';
        updatePasswordHints('edit');
        loadUserData(userId);
    }
    
    modal.classList.add('active');
}