// =============================================
// MÓDULO DE USUARIOS
// =============================================

// 1. FUNCIONES PARA CÉDULA Y TELÉFONO
function limpiarCedula(input) {
    let valor = input.value;
    valor = valor.replace(/[^0-9]/g, '');
    if (valor.length > 10) valor = valor.substring(0, 10);
    if (valor.length > 0 && valor.charAt(0) === '0') valor = valor.substring(1);
    input.value = valor;
    validarCedula(input);
}

function validarCedula(input) {
    const valor = input.value;
    const regex = /^[1-9][0-9]{5,9}$/;
    const errorSpan = document.getElementById(input.id + '_error');
    
    if (valor.length === 0) {
        if (errorSpan) errorSpan.style.display = 'none';
        input.classList.remove('is-invalid', 'is-valid');
        return true;
    }
    
    if (!regex.test(valor)) {
        if (errorSpan) {
            errorSpan.textContent = 'La cédula debe tener entre 6 y 10 dígitos y no comenzar con 0';
            errorSpan.style.display = 'block';
        }
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        return false;
    } else {
        if (errorSpan) errorSpan.style.display = 'none';
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }
}

function procesarPegadoCedula(event) {
    const textoPegado = (event.clipboardData || window.clipboardData).getData('text');
    const numeros = textoPegado.replace(/[^0-9]/g, '');
    const limpio = numeros.charAt(0) === '0' ? numeros.substring(1) : numeros;
    event.preventDefault();
    const input = event.target;
    input.value = limpio.substring(0, 10);
    const eventoInput = new Event('input', { bubbles: true });
    input.dispatchEvent(eventoInput);
}

function limpiarTelefono(input) {
    let valor = input.value;
    valor = valor.replace(/[^0-9]/g, '');
    if (valor.length > 10) valor = valor.substring(0, 10);
    input.value = valor;
    validarTelefono(input);
}

function validarTelefono(input) {
    const valor = input.value;
    const regex = /^[0-9]{10}$/;
    const errorSpan = document.getElementById(input.id + '_error');
    
    if (valor.length === 0) {
        if (errorSpan) errorSpan.style.display = 'none';
        input.classList.remove('is-invalid', 'is-valid');
        return true;
    }
    
    if (!regex.test(valor)) {
        if (errorSpan) {
            errorSpan.textContent = 'El teléfono debe tener exactamente 10 dígitos';
            errorSpan.style.display = 'block';
        }
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        return false;
    } else {
        if (errorSpan) errorSpan.style.display = 'none';
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        return true;
    }
}

function procesarPegadoTelefono(event) {
    const textoPegado = (event.clipboardData || window.clipboardData).getData('text');
    const numeros = textoPegado.replace(/[^0-9]/g, '');
    event.preventDefault();
    const input = event.target;
    input.value = numeros.substring(0, 10);
    const eventoInput = new Event('input', { bubbles: true });
    input.dispatchEvent(eventoInput);
}

function validarCampo(input) {
    if (input.id === 'crear_documento') return validarCedula(input);
    if (input.id === 'crear_telefono') return validarTelefono(input);
    return true;
}

// 2. GENERACIÓN DE USUARIO
function limpiarTexto(texto) {
    if (!texto) return '';
    const mapa = {
        'á':'a', 'é':'e', 'í':'i', 'ó':'o', 'ú':'u',
        'Á':'a', 'É':'e', 'Í':'i', 'Ó':'o', 'Ú':'u',
        'ñ':'n', 'Ñ':'n', 'ü':'u', 'Ü':'u'
    };
    return texto.toLowerCase().replace(/[áéíóúñü]/gi, function(match) {
        return mapa[match] || match;
    }).replace(/[^a-z]/g, '');
}

function generarUsuario() {
    const documento = document.getElementById('crear_documento')?.value || '';
    const primerNombre = document.getElementById('crear_primer_nombre')?.value || '';
    const usuarioInput = document.getElementById('usuario_generado');
    
    if (!usuarioInput) return;
    
    if (documento && documento.length >= 6 && primerNombre) {
        const nombreLimpio = limpiarTexto(primerNombre);
        const usuario = documento + '.' + nombreLimpio;
        usuarioInput.value = usuario;
    } else if (documento && !primerNombre) {
        usuarioInput.value = documento + '.';
    } else {
        usuarioInput.value = '';
    }
}

// 3. LÓGICA PARA FILTRAR ROLES EN EL MODAL CREAR
let originalRoleOptions = null;

function guardarOpcionesOriginales() {
    const select = document.getElementById('crear_rol_id');
    if (!originalRoleOptions && select) {
        originalRoleOptions = Array.from(select.options).map(opt => ({
            value: opt.value,
            text: opt.text
        }));
    }
}

function restaurarOpcionesRoles() {
    const select = document.getElementById('crear_rol_id');
    if (!select || !originalRoleOptions) return;
    
    // ✅ ELIMINAR CAMPO HIDDEN si existe
    const hiddenInput = document.getElementById('hidden_rol_id');
    if (hiddenInput) hiddenInput.remove();
    
    select.innerHTML = '';
    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = '— Seleccionar rol —';
    select.appendChild(defaultOpt);
    originalRoleOptions.forEach(opt => {
        const option = document.createElement('option');
        option.value = opt.value;
        option.textContent = opt.text;
        select.appendChild(option);
    });
    select.disabled = false;
}

function filtrarRolesAdministrativos() {
    const select = document.getElementById('crear_rol_id');
    if (!select || !originalRoleOptions) return;
    const rolesPermitidos = ['ADM', 'COO', 'BIE'];
    select.innerHTML = '';
    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = '— Seleccionar rol —';
    select.appendChild(defaultOpt);
    originalRoleOptions.forEach(opt => {
        const esPermitido = rolesPermitidos.some(sigla => opt.text.includes(sigla));
        if (esPermitido) {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.text;
            select.appendChild(option);
        }
    });
    select.disabled = false;
}

function fijarRolDocente() {
    const select = document.getElementById('crear_rol_id');
    if (!select || !originalRoleOptions) return;
    const docOption = originalRoleOptions.find(opt => opt.text.includes('DOC'));
    if (docOption) {
        select.innerHTML = '';
        const option = document.createElement('option');
        option.value = docOption.value;
        option.textContent = docOption.text;
        select.appendChild(option);
        select.disabled = true;
        
        // ✅ CREAR CAMPO HIDDEN para enviar el valor
        let hiddenInput = document.getElementById('hidden_rol_id');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'rol_id';
            hiddenInput.id = 'hidden_rol_id';
            select.parentNode.appendChild(hiddenInput);
        }
        hiddenInput.value = docOption.value;
    } else {
        restaurarOpcionesRoles();
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].text.includes('DOC')) {
                select.value = select.options[i].value;
                break;
            }
        }
        select.disabled = true;
    }
}

// 4. ABRIR MODALES
function abrirModalCrear(rol = null) {
    const form = document.querySelector('#modal-crear form');
    if (form) form.reset();
    
    const campos = ['crear_primer_nombre', 'crear_segundo_nombre', 'crear_primer_apellido', 
                    'crear_segundo_apellido', 'crear_documento', 'crear_telefono', 'crear_correo'];
    campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    
    const usuarioGenerado = document.getElementById('usuario_generado');
    if (usuarioGenerado) usuarioGenerado.value = '';
    
    const docInput = document.getElementById('crear_documento');
    const telInput = document.getElementById('crear_telefono');
    if (docInput) docInput.classList.remove('is-invalid', 'is-valid');
    if (telInput) telInput.classList.remove('is-invalid', 'is-valid');
    
    const docError = document.getElementById('crear_documento_error');
    const telError = document.getElementById('crear_telefono_error');
    if (docError) docError.style.display = 'none';
    if (telError) telError.style.display = 'none';
    
    guardarOpcionesOriginales();
    
    const modalTitulo = document.getElementById('modal-crear-titulo');
    
    if (rol === 'doc') {
        fijarRolDocente();
        if (modalTitulo) modalTitulo.innerHTML = '<i class="fa-solid fa-chalkboard-user"></i> Nuevo Docente';
    } else if (rol === 'adm') {
        filtrarRolesAdministrativos();
        if (modalTitulo) modalTitulo.innerHTML = '<i class="fa-solid fa-user-plus"></i> Nuevo Usuario Administrativo';
    } else {
        restaurarOpcionesRoles();
        if (modalTitulo) modalTitulo.innerHTML = '<i class="fa-solid fa-user-plus"></i> Nuevo usuario';
    }
    
    const modal = document.getElementById('modal-crear');
    if (modal) modal.classList.add('abierto');
}

function abrirModalCargaMasiva() {
    const modal = document.getElementById('modal-carga-masiva');
    if (modal) modal.classList.add('abierto');
}

function abrirModalEditar(id, primerNombre, segundoNombre, primerApellido, segundoApellido, documento, correo, telefono, usuario, rolId) {
    const editPrimerNombre = document.getElementById('edit-primer_nombre');
    const editSegundoNombre = document.getElementById('edit-segundo_nombre');
    const editPrimerApellido = document.getElementById('edit-primer_apellido');
    const editSegundoApellido = document.getElementById('edit-segundo_apellido');
    const editDocumento = document.getElementById('edit-documento');
    const editCorreo = document.getElementById('edit-correo');
    const editTelefono = document.getElementById('edit-telefono');
    const editUsuario = document.getElementById('edit-usuario');
    const editRol = document.getElementById('edit-rol');
    const formEditar = document.getElementById('form-editar');
    
    if (editPrimerNombre) editPrimerNombre.value = primerNombre;
    if (editSegundoNombre) editSegundoNombre.value = segundoNombre || '';
    if (editPrimerApellido) editPrimerApellido.value = primerApellido;
    if (editSegundoApellido) editSegundoApellido.value = segundoApellido || '';
    if (editDocumento) editDocumento.value = documento;
    if (editCorreo) editCorreo.value = correo || '';
    if (editTelefono) editTelefono.value = telefono || '';
    if (editUsuario) editUsuario.value = usuario;
    if (editRol) editRol.value = rolId;
    if (formEditar) formEditar.action = '/usuarios/' + id;
    
    const modal = document.getElementById('modal-editar');
    if (modal) modal.classList.add('abierto');
}

// 5. INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    const documento = document.getElementById('crear_documento');
    const primerNombre = document.getElementById('crear_primer_nombre');
    const telefono = document.getElementById('crear_telefono');
    
    if (documento) documento.addEventListener('input', generarUsuario);
    if (primerNombre) primerNombre.addEventListener('input', generarUsuario);
    if (telefono) telefono.addEventListener('input', function() { validarTelefono(this); });
    
    guardarOpcionesOriginales();
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modalCrear = document.getElementById('modal-crear');
            const modalEditar = document.getElementById('modal-editar');
            const modalCarga = document.getElementById('modal-carga-masiva');
            
            if (modalCrear && modalCrear.classList.contains('abierto')) modalCrear.classList.remove('abierto');
            if (modalEditar && modalEditar.classList.contains('abierto')) modalEditar.classList.remove('abierto');
            if (modalCarga && modalCarga.classList.contains('abierto')) modalCarga.classList.remove('abierto');
        }
    });
    
    document.querySelectorAll('.modal-overlay').forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('abierto');
                if (this.id === 'modal-crear') {
                    restaurarOpcionesRoles();
                    const select = document.getElementById('crear_rol_id');
                    if (select) select.value = '';
                }
            }
        });
    });
});

// Hacer funciones globales
window.limpiarCedula = limpiarCedula;
window.validarCedula = validarCedula;
window.procesarPegadoCedula = procesarPegadoCedula;
window.limpiarTelefono = limpiarTelefono;
window.validarTelefono = validarTelefono;
window.procesarPegadoTelefono = procesarPegadoTelefono;
window.validarCampo = validarCampo;
window.generarUsuario = generarUsuario;
window.abrirModalCrear = abrirModalCrear;
window.abrirModalEditar = abrirModalEditar;
window.abrirModalCargaMasiva = abrirModalCargaMasiva;
window.cerrarModal = cerrarModal;