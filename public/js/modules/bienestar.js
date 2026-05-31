// =============================================
// MÓDULO DE BIENESTAR
// =============================================

// Toggle para "otro" motivo (aspectos)
function toggleOtro() {
    const wrap = document.getElementById('detalle-otro-wrap');
    if (wrap) {
        wrap.style.display = document.getElementById('chk-otro').checked ? 'block' : 'none';
    }
}

// Toggle para motivo de no contacto en el formulario inicial
function toggleMotivoNoContacto() {
    const wrap = document.getElementById('motivo-no-contacto-wrap');
    if (wrap) {
        wrap.style.display = document.getElementById('contacto_fallido').checked ? 'block' : 'none';
    }
}

// Toggle para motivo de no contacto en el formulario de nueva observación
function toggleMotivoNoContactoNuevo() {
    const wrap = document.getElementById('motivo-no-contacto-nuevo-wrap');
    if (wrap) {
        wrap.style.display = document.getElementById('contacto_fallido_nuevo').checked ? 'block' : 'none';
    }
}

// Validación del formulario de cierre
function validarCierre(event) {
    const razon = document.getElementById('razon_cierre');
    if (!razon.value || razon.value.trim() === '') {
        event.preventDefault();
        alert('Por favor indique la solución o razón de cierre');
        return false;
    }
    if (razon.value.trim().length < 5) {
        event.preventDefault();
        alert('La razón de cierre debe tener al menos 5 caracteres');
        return false;
    }
    return confirm('¿Cerrar este caso definitivamente? La solución quedará registrada.');
}

// Hacer funciones globales
window.toggleOtro = toggleOtro;
window.toggleMotivoNoContacto = toggleMotivoNoContacto;
window.toggleMotivoNoContactoNuevo = toggleMotivoNoContactoNuevo;
window.validarCierre = validarCierre;