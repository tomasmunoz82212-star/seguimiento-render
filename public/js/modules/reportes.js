// =============================================
// MÓDULO DE REPORTES - Con modal de materias
// =============================================

let todasLasMaterias = [];
let materiaSeleccionadaId = null;
let materiaSeleccionadaNombre = null;

function buscarEstudiante() {
    const valor = document.getElementById('documento-input')?.value.trim();
    
    if (!valor || valor.length < 4) {
        alert('Ingrese un número de documento válido (mínimo 4 dígitos)');
        return;
    }

    const btnBuscar = document.getElementById('btn-buscar');
    const textoOriginal = btnBuscar.innerHTML;
    btnBuscar.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Buscando...';
    btnBuscar.disabled = true;

    fetch(`/buscar-estudiante?documento=${valor}`)
        .then(r => r.json())
        .then(data => {
            if (data.found) {
                const nombreInput = document.getElementById('est-nombre');
                const correoInput = document.getElementById('est-correo');
                const documentoHidden = document.getElementById('documento-hidden');
                const estudianteInfo = document.getElementById('estudiante-info');
                const programaId = document.getElementById('programa_id');
                const carreraInput = document.getElementById('est-carrera');
                const btnMateria = document.getElementById('btn-seleccionar-materia');
                
                if (nombreInput) nombreInput.value = data.nombre;
                if (correoInput) correoInput.value = data.correo ?? '—';
                if (documentoHidden) documentoHidden.value = valor;
                if (estudianteInfo) {
                    estudianteInfo.style.display = 'block';
                    estudianteInfo.classList.add('encontrado');
                }
                
                if (data.programa_id) {
                    if (programaId) programaId.value = data.programa_id;
                    if (carreraInput) carreraInput.value = data.programa_nombre;
                    cargarMaterias(data.materias);
                    if (btnMateria) btnMateria.disabled = false;
                } else {
                    if (carreraInput) carreraInput.value = 'No matriculado en período actual';
                    if (btnMateria) btnMateria.disabled = true;
                    document.getElementById('materia-seleccionada').textContent = '— No hay materias disponibles —';
                }
            } else {
                limpiarEstudiante();
                alert('Estudiante no encontrado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            limpiarEstudiante();
            alert('Error al buscar el estudiante');
        })
        .finally(() => {
            btnBuscar.innerHTML = textoOriginal;
            btnBuscar.disabled = false;
        });
}

function cargarMaterias(materias) {
    todasLasMaterias = materias;
    materiaSeleccionadaId = null;
    materiaSeleccionadaNombre = null;
    document.getElementById('materia-seleccionada').textContent = '— Seleccionar materia —';
    document.getElementById('materia_id_input').value = '';
    
    // Preparar lista para el modal
    const listaDiv = document.getElementById('lista-materias');
    if (!listaDiv) return;
    
    if (!materias || materias.length === 0) {
        listaDiv.innerHTML = '<div class="text-center" style="padding: 20px; color: #9DA3B4;">No hay materias disponibles para esta carrera</div>';
        return;
    }
    
    listaDiv.innerHTML = '';
    materias.forEach(m => {
        const item = document.createElement('div');
        item.className = 'materia-item';
        item.dataset.id = m.id;
        item.dataset.nombre = m.nombre;
        item.innerHTML = `
            <div class="materia-nombre">${m.nombre}</div>
            <div class="materia-programa">${m.programa_nombre || ''}</div>
        `;
        item.onclick = () => seleccionarMateria(m.id, m.nombre);
        listaDiv.appendChild(item);
    });
}

function seleccionarMateria(id, nombre) {
    materiaSeleccionadaId = id;
    materiaSeleccionadaNombre = nombre;
    document.getElementById('materia-seleccionada').textContent = nombre;
    document.getElementById('materia_id_input').value = id;
    cerrarModalMaterias();
}

function abrirModalMaterias() {
    const btnMateria = document.getElementById('btn-seleccionar-materia');
    if (btnMateria.disabled) {
        alert('Primero debe seleccionar un estudiante');
        return;
    }
    
    const modal = document.getElementById('modal-materias');
    modal.style.display = 'flex';
    document.getElementById('buscador-materias').value = '';
    filtrarListaMaterias();
    document.getElementById('buscador-materias').focus();
}

function cerrarModalMaterias() {
    const modal = document.getElementById('modal-materias');
    modal.style.display = 'none';
}

function filtrarListaMaterias() {
    const busqueda = document.getElementById('buscador-materias')?.value.toLowerCase() || '';
    const items = document.querySelectorAll('#lista-materias .materia-item');
    
    // Normalizar búsqueda: eliminar tildes y caracteres especiales
    const normalizar = (texto) => {
        return texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    };
    
    const busquedaNormalizada = normalizar(busqueda);
    
    items.forEach(item => {
        const nombre = item.dataset.nombre || '';
        const nombreNormalizado = normalizar(nombre);
        
        if (busqueda === '' || nombreNormalizado.includes(busquedaNormalizada)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function buscarConEnter(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        buscarEstudiante();
    }
}

function verificarBorradoCedula() {
    const documentoInput = document.getElementById('documento-input');
    const valorActual = documentoInput?.value.trim() || '';
    const documentoHidden = document.getElementById('documento-hidden')?.value || '';
    
    if (valorActual === '' && documentoHidden !== '') {
        limpiarEstudiante();
    }
}

function limpiarEstudiante() {
    const nombreInput = document.getElementById('est-nombre');
    const correoInput = document.getElementById('est-correo');
    const carreraInput = document.getElementById('est-carrera');
    const documentoHidden = document.getElementById('documento-hidden');
    const programaId = document.getElementById('programa_id');
    const estudianteInfo = document.getElementById('estudiante-info');
    const btnMateria = document.getElementById('btn-seleccionar-materia');
    
    if (nombreInput) nombreInput.value = '';
    if (correoInput) correoInput.value = '';
    if (carreraInput) carreraInput.value = '';
    if (documentoHidden) documentoHidden.value = '';
    if (programaId) programaId.value = '';
    if (estudianteInfo) {
        estudianteInfo.style.display = 'none';
        estudianteInfo.classList.remove('encontrado');
    }
    if (btnMateria) btnMateria.disabled = true;
    
    // Limpiar materia seleccionada
    materiaSeleccionadaId = null;
    materiaSeleccionadaNombre = null;
    document.getElementById('materia-seleccionada').textContent = '— Seleccionar materia —';
    document.getElementById('materia_id_input').value = '';
    todasLasMaterias = [];
}

function limpiarTodo() {
    const documentoInput = document.getElementById('documento-input');
    if (documentoInput) documentoInput.value = '';
    
    limpiarEstudiante();
    const tipoSelect = document.querySelector('select[name="tipo"]');
    if (tipoSelect) tipoSelect.value = '';
    const textarea = document.querySelector('textarea[name="descripcion"]');
    if (textarea) textarea.value = '';
}

// Eventos
document.addEventListener('DOMContentLoaded', function() {
    const btnBuscar = document.getElementById('btn-buscar');
    const documentoInput = document.getElementById('documento-input');
    const btnMateria = document.getElementById('btn-seleccionar-materia');
    const buscadorMaterias = document.getElementById('buscador-materias');
    
    if (btnBuscar) {
        btnBuscar.addEventListener('click', buscarEstudiante);
    }
    
    if (btnMateria) {
        btnMateria.addEventListener('click', abrirModalMaterias);
    }
    
    if (buscadorMaterias) {
        buscadorMaterias.addEventListener('input', filtrarListaMaterias);
    }
    
    if (documentoInput) {
        documentoInput.addEventListener('keypress', buscarConEnter);
        documentoInput.addEventListener('input', verificarBorradoCedula);
    }
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalMaterias();
        }
    });
    
    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('modal-materias');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                cerrarModalMaterias();
            }
        });
    }
});

// Hacer funciones globales
window.buscarEstudiante = buscarEstudiante;
window.limpiarTodo = limpiarTodo;
window.cerrarModalMaterias = cerrarModalMaterias;