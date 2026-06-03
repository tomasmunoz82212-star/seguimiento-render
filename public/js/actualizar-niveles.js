console.log('Script de actualización de niveles cargado');

// =============================================
// 1. ACTUALIZACIÓN VISUAL DE BADGES
// =============================================
function actualizarNiveles() {
    const badges = document.querySelectorAll('[id^="badge-nivel-"]');
    console.log(`Actualizando ${badges.length} badges de nivel`);

    badges.forEach(badge => {
        const reporteId = badge.id.replace('badge-nivel-', '');
        
        fetch(`/api/reporte/${reporteId}/nivel-alerta`)
            .then(response => response.json())
            .then(data => {
                badge.innerHTML = generarBadgeNivel(data.nivel_alerta);
                
                const debugContainer = document.getElementById(`debug-nivel-${reporteId}`);
                if (debugContainer) {
                    debugContainer.innerHTML = `Límite: ${data.fecha_limite_legible}`;
                }
            })
            .catch(error => console.error('Error actualizando nivel:', error));
    });
}

function generarBadgeNivel(nivel) {
    const badges = {
        'verde':    '<span class="badge" style="background:#E8F5E9; color:#2D7D32;"><i class="fa-solid fa-circle"></i> A tiempo</span>',
        'naranja':  '<span class="badge" style="background:#FFF3E0; color:#E65100;"><i class="fa-solid fa-clock"></i> Próximo a vencer</span>',
        'rojo':     '<span class="badge" style="background:#FFEBEE; color:#C62828;"><i class="fa-solid fa-exclamation-triangle"></i> Urgente</span>',
        'expirado': '<span class="badge" style="background:#9DA3B4; color:#FFFFFF;"><i class="fa-solid fa-calendar-times"></i> Expirado</span>'
    };
    return badges[nivel] || '';
}

// =============================================
// 2. ACTUALIZACIÓN EN SEGUNDO PLANO
// =============================================
function actualizarNivelesSegundoPlano() {
    console.log('[Niveles] Enviando solicitud de actualización en segundo plano...');
    fetch('/api/reportes/actualizar-niveles?_=' + Date.now())
        .then(r => r.json())
        .then(d => console.log(`[Niveles] Segundo plano completado. Reportes actualizados: ${d.actualizados}`))
        .catch(e => console.error('[Niveles] Error en actualización de segundo plano:', e));
}

// =============================================
// 3. ACTUALIZAR CONTADOR DE NOTIFICACIONES
// =============================================
function actualizarContadorNotificaciones() {
    const badge = document.getElementById('notificacion-badge');
    if (!badge) return;
    
    fetch('/api/notificaciones/contador?_=' + Date.now())
        .then(response => response.json())
        .then(data => {
            const count = parseInt(data.count) || 0;
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-block' : 'none';
            console.log('[Notificaciones] Contador actualizado: ' + count);
        })
        .catch(error => console.error('Error actualizando contador:', error));
}

// =============================================
// 4. DETECTAR CAMBIO DE PERÍODO
// =============================================
function detectarCambioPeriodo() {
    // Escuchar cambios en el selector de período
    const selectPeriodo = document.querySelector('select[name="periodo_id"]');
    if (selectPeriodo) {
        selectPeriodo.addEventListener('change', function() {
            console.log('[Período] Cambio detectado, reiniciando contador...');
            setTimeout(() => {
                actualizarContadorNotificaciones();
                actualizarNiveles();
            }, 500);
        });
    }
}

// =============================================
// 5. INICIAR TODAS LAS FUNCIONES
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    // Actualización visual (badges) cada 30 segundos
    actualizarNiveles();
    setInterval(actualizarNiveles, 30000);

    // Actualización en segundo plano cada 30 segundos
    actualizarNivelesSegundoPlano();
    setInterval(actualizarNivelesSegundoPlano, 30000);
    
    // Actualizar contador de notificaciones cada 10 segundos
    actualizarContadorNotificaciones();
    setInterval(actualizarContadorNotificaciones, 10000);
    
    // Detectar cambio de período
    detectarCambioPeriodo();
});