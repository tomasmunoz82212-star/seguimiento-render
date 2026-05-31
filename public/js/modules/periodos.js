// =============================================
// MÓDULO DE PERIODOS
// =============================================

// Mostrar nombre de archivo seleccionado (Nuevo.blade.php)
function mostrarArchivo(input) {
    const nombre = input.files[0]?.name || 'Ninguno';
    const nombreEl = document.getElementById('nombre-archivo');
    const uploadZone = document.getElementById('upload-zone');
    
    if (nombreEl) {
        nombreEl.textContent = nombre;
        nombreEl.style.color = '#2D7D32';
    }
    if (uploadZone) {
        uploadZone.classList.add('upload-zone-active');
    }
}

// Mostrar nombre de archivo seleccionado (Editar.blade.php)
function mostrarArchivoEdit(input) {
    const nombre = input.files[0]?.name || 'Ninguno';
    const el = document.getElementById('nombre-archivo-edit');
    const uploadZone = document.getElementById('upload-zone-edit');
    
    if (el) {
        el.textContent = nombre;
        el.style.color = '#2D7D32';
    }
    if (uploadZone) {
        uploadZone.classList.add('upload-zone-active');
    }
}

// Buscador de estudiantes (Estudiantes.blade.php)
function filtrar() {
    const texto = document.getElementById('buscador')?.value.toLowerCase() || '';
    const filas = document.querySelectorAll('#tabla-estudiantes tbody tr');
    filas.forEach(fila => {
        fila.style.display = fila.textContent.toLowerCase().includes(texto) ? '' : 'none';
    });
}

// Configurar drag & drop para el upload zone (Nuevo.blade.php)
document.addEventListener('DOMContentLoaded', function() {
    const zona = document.getElementById('upload-zone');
    if (zona) {
        zona.addEventListener('dragover', e => { 
            e.preventDefault(); 
            zona.classList.add('upload-zone-active'); 
        });
        zona.addEventListener('dragleave', () => { 
            zona.classList.remove('upload-zone-active'); 
        });
        zona.addEventListener('drop', e => {
            e.preventDefault();
            const archivo = document.getElementById('archivo');
            if (archivo) {
                archivo.files = e.dataTransfer.files;
                mostrarArchivo(archivo);
            }
        });
    }
    
    // Drag & drop para el upload zone de edición
    const zonaEdit = document.getElementById('upload-zone-edit');
    if (zonaEdit) {
        zonaEdit.addEventListener('dragover', e => { 
            e.preventDefault(); 
            zonaEdit.classList.add('upload-zone-active'); 
        });
        zonaEdit.addEventListener('dragleave', () => { 
            zonaEdit.classList.remove('upload-zone-active'); 
        });
        zonaEdit.addEventListener('drop', e => {
            e.preventDefault();
            const archivo = document.getElementById('archivo-edit');
            if (archivo) {
                archivo.files = e.dataTransfer.files;
                mostrarArchivoEdit(archivo);
            }
        });
    }
});

// Hacer funciones globales
window.mostrarArchivo = mostrarArchivo;
window.mostrarArchivoEdit = mostrarArchivoEdit;
window.filtrar = filtrar;