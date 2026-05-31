// =============================================
// AUTO-REFRESH - Recarga la página SOLO cuando hay nuevos reportes
// =============================================

(function() {
    'use strict';

    let refreshInterval = null;
    let ultimoTimestamp = 0;
    let primeraVez = true;

    const AUTO_REFRESH_URLS = ['/dashboard', '/bienestar', '/seguimiento', '/notificaciones'];

    function shouldAutoRefresh() {
        return AUTO_REFRESH_URLS.some(url => window.location.pathname === url);
    }

    function obtenerUltimoTimestamp() {
        return fetch('/api/ultimo-reporte')
            .then(res => res.json())
            .then(data => data.timestamp)
            .catch(() => 0);
    }

    function verificarYActualizar() {
        obtenerUltimoTimestamp().then(nuevoTimestamp => {
            if (primeraVez) {
                ultimoTimestamp = nuevoTimestamp;
                primeraVez = false;
                console.log('⏳ Timestamp inicial:', nuevoTimestamp);
                return;
            }
            
            // ✅ SOLO cuando hay un NUEVO reporte
            if (nuevoTimestamp > ultimoTimestamp) {
                console.log('✅ Nuevo reporte detectado. Recargando página...');
                ultimoTimestamp = nuevoTimestamp;
                location.reload();  // 👈 Recarga TODO
            }
        });
    }

    function iniciarPolling() {
        if (refreshInterval) clearInterval(refreshInterval);
        
        if (shouldAutoRefresh()) {
            verificarYActualizar();
            refreshInterval = setInterval(() => {
                if (!document.hidden) verificarYActualizar();
            }, 5000);
        }
    }

    function detenerPolling() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            refreshInterval = null;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(iniciarPolling, 1000);
        document.addEventListener('visibilitychange', function() {
            document.hidden ? detenerPolling() : iniciarPolling();
        });
    });
})();