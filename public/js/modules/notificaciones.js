// =============================================
// MÓDULO DE NOTIFICACIONES
// =============================================

(function() {
    'use strict';

    // =========================================
    // NUEVO: Actualizar contador automáticamente
    // =========================================
    
    window.actualizarContadorNotificaciones = function() {
        const badge = document.getElementById('notificacion-badge');
        if (!badge) return;
        
        fetch('/api/notificaciones/contador?_=' + Date.now())
            .then(response => response.json())
            .then(data => {
                const count = parseInt(data.count) || 0;
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline-block' : 'none';
            })
            .catch(error => console.error('Error actualizando contador:', error));
    };

    // =========================================
    // Marcar una notificación como leída
    // =========================================
    
    function marcarComoLeida(id, cardElement) {
        fetch(`/notificaciones/${id}/leer`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                cardElement.classList.remove('no-leida');
                const badge = cardElement.querySelector('.notificacion-badge');
                if (badge) badge.remove();

                // Actualizar contador después de marcar como leída
                window.actualizarContadorNotificaciones();
            }
        })
        .catch(error => console.error('Error al marcar como leída:', error));
    }

    // =========================================
    // INICIALIZACIÓN
    // =========================================
    
    document.addEventListener('DOMContentLoaded', function() {
        // Iniciar el contador y actualizar cada 15 segundos
        window.actualizarContadorNotificaciones();
        setInterval(window.actualizarContadorNotificaciones, 15000);
        
        // Marcar al hacer clic en la tarjeta (excepto en enlaces)
        document.querySelectorAll('.notificacion-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (e.target.closest('a')) return;
                const id = this.dataset.id;
                if (!id) return;
                marcarComoLeida(id, this);
            });
        });

        // Marcar al hacer clic en "Ver reporte"
        document.querySelectorAll('.notificacion-enlace').forEach(enlace => {
            enlace.addEventListener('click', function(e) {
                const card = this.closest('.notificacion-card');
                if (!card) return;
                const id = card.dataset.id;
                if (!id) return;
                marcarComoLeida(id, card);
            });
        });
    });
})();