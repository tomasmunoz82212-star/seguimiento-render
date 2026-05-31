//import './bootstrap';

// =============================================
// MÓDULO PRINCIPAL - MENÚ DE USUARIO Y MODALES
// =============================================

// Toggle del menú de usuario
if (typeof window.toggleUserMenu === 'undefined') {
    window.toggleUserMenu = function(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) dropdown.classList.toggle('hidden');
    };
}

// Mostrar modal de información del usuario
if (typeof window.mostrarModalUsuario === 'undefined') {
    window.mostrarModalUsuario = function() {
        const modal = document.getElementById('userModal');
        if (modal) modal.classList.remove('hidden');
    };
}

// Cerrar cualquier modal
if (typeof window.cerrarModal === 'undefined') {
    window.cerrarModal = function() {
        const userModal = document.getElementById('userModal');
        if (userModal) userModal.classList.add('hidden');
        
        const modalCrear = document.getElementById('modal-crear');
        const modalEditar = document.getElementById('modal-editar');
        const modalCarga = document.getElementById('modal-carga-masiva');
        
        if (modalCrear) modalCrear.classList.remove('abierto');
        if (modalEditar) modalEditar.classList.remove('abierto');
        if (modalCarga) modalCarga.classList.remove('abierto');
    };
}

// Cerrar modales con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const userModal = document.getElementById('userModal');
        if (userModal && !userModal.classList.contains('hidden')) {
            userModal.classList.add('hidden');
        }
        
        const modalCrear = document.getElementById('modal-crear');
        const modalEditar = document.getElementById('modal-editar');
        const modalCarga = document.getElementById('modal-carga-masiva');
        
        if (modalCrear && modalCrear.classList.contains('abierto')) modalCrear.classList.remove('abierto');
        if (modalEditar && modalEditar.classList.contains('abierto')) modalEditar.classList.remove('abierto');
        if (modalCarga && modalCarga.classList.contains('abierto')) modalCarga.classList.remove('abierto');
    }
});

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const trigger = document.querySelector('.user-menu-trigger');
    
    if (dropdown && !dropdown.classList.contains('hidden')) {
        if (trigger && !trigger.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    }
});

// =============================================
// CONTADOR DE NOTIFICACIONES (POLLING)
// =============================================
(function() {
    const badge = document.getElementById('notificacion-badge');
    let currentCount = badge ? parseInt(badge.textContent) || 0 : 0;

    window.actualizarContadorNotificaciones = function() {
        fetch('/api/notificaciones/contador?_=' + Date.now())
            .then(response => response.json())
            .then(data => {
                const newCount = parseInt(data.count) || 0;
                const badgeElement = document.getElementById('notificacion-badge');
                if (!badgeElement) return;
                
                badgeElement.textContent = newCount;
                badgeElement.style.display = newCount > 0 ? 'inline-block' : 'none';
                currentCount = newCount;
            })
            .catch(error => console.error('Error actualizando contador:', error));
    };

    setTimeout(function() {
        window.actualizarContadorNotificaciones();
        setInterval(window.actualizarContadorNotificaciones, 15000);
    }, 1000);
})();

// =============================================
// TOGGLE PASSWORD - Mostrar/ocultar contraseña
// =============================================

(function() {
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const input = document.getElementById(this.dataset.target);
            if (input) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            }
        });
    });
})();