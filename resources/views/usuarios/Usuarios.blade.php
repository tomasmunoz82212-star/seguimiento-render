@extends('layouts.panel')
@section('titulo', 'Usuarios del Sistema')

@push('estilos')
<link rel="stylesheet" href="{{ asset('css/modules/usuarios.css') }}">
@endpush

@section('contenido')
{{-- Stat cards --}}
<div class="stat-grid">
    <div class="stat-card" style="--accent: #1B5E20">
        <div class="stat-value">{{ $usuarios->count() }}</div>
        <div class="stat-label">Total usuarios</div>
    </div>
    <div class="stat-card" style="--accent: #1565C0">
        <div class="stat-value">{{ $usuarios->filter(fn($u) => $u->rol->sigla === 'ADM')->count() }}</div>
        <div class="stat-label">Administradores</div>
    </div>
    <div class="stat-card" style="--accent: #E65100">
        <div class="stat-value">{{ $usuarios->filter(fn($u) => $u->rol->sigla === 'COO')->count() }}</div>
        <div class="stat-label">Coordinación</div>
    </div>
    <div class="stat-card" style="--accent: #F2C200">
        <div class="stat-value">{{ $usuarios->filter(fn($u) => $u->rol->sigla === 'BIE')->count() }}</div>
        <div class="stat-label">Bienestar</div>
    </div>
    <div class="stat-card" style="--accent: #6A1B9A">
        <div class="stat-value">{{ $usuarios->filter(fn($u) => $u->rol->sigla === 'DOC')->count() }}</div>
        <div class="stat-label">Docentes</div>
    </div>
</div>

{{-- Header --}}
<div class="page-header">
    <div>
        <div class="page-title">Usuarios del Sistema</div>
        <div class="page-sub">Gestiona los usuarios y sus roles</div>
    </div>
</div>

@if(session('success'))
    <div class="alert-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

{{-- SECCIÓN 1: USUARIOS ADMINISTRATIVOS --}}
<div class="seccion-usuarios">
    <div class="seccion-header">
        <div class="seccion-titulo">
            <i class="fa-solid fa-building"></i> Usuarios Administrativos
        </div>
        <div class="seccion-sub">Administradores, Coordinación y Bienestar</div>
        <button class="btn btn-secondary btn-sm" onclick="abrirModalCrear('adm')">
            <i class="fa-solid fa-plus"></i> Nuevo usuario administrativo
        </button>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Persona</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios->filter(fn($u) => in_array($u->rol->sigla, ['ADM', 'COO', 'BIE'])) as $u)
                <tr>
                    <td style="text-align: left">
                        <div class="usuario-cell">
                            <div class="usuario-avatar">
                                {{ strtoupper(substr($u->persona->primer_nombre, 0, 1)) }}{{ strtoupper(substr($u->persona->primer_apellido, 0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $u->persona->nombre_completo }}</strong>
                                <div class="text-muted" style="font-size:.8rem">CC {{ $u->persona->documento }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center">{{ $u->usuario }}</td>
                    <td style="text-align: center">
                        <span class="badge badge-rol badge-rol-{{ strtolower($u->rol->sigla) }}">
                            {{ $u->rol->sigla }}
                        </span>
                    </td>
                    <td style="text-align: center">
                        @if($u->estado === 'activo')
                            <span class="badge" style="background: #E8F5E9; color: #2D7D32; padding: 4px 10px; border-radius: 20px;">
                                <i class="fa-solid fa-circle-check"></i> Activo
                            </span>
                        @else
                            <span class="badge" style="background: #FFEBEE; color: #C62828; padding: 4px 10px; border-radius: 20px;">
                                <i class="fa-solid fa-circle-xmark"></i> Inactivo
                            </span>
                        @endif
                    </td>
                    <td style="text-align: center">
                        <div style="display: flex; gap: 6px; justify-content: center;">
                            <button class="btn-link" onclick="abrirModalEditar(
                                {{ $u->id }},
                                '{{ addslashes($u->persona->primer_nombre) }}',
                                '{{ addslashes($u->persona->segundo_nombre) }}',
                                '{{ addslashes($u->persona->primer_apellido) }}',
                                '{{ addslashes($u->persona->segundo_apellido) }}',
                                '{{ $u->persona->documento }}',
                                '{{ addslashes($u->persona->correo) }}',
                                '{{ addslashes($u->persona->telefono) }}',
                                '{{ $u->usuario }}',
                                {{ $u->rol_id }}
                            )">
                                <i class="fa-solid fa-pen"></i> Editar
                            </button>
                            @if($u->estado === 'activo')   
                                <form method="POST" action="/usuarios/{{ $u->id }}/cambiar-estado" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="inactivo">
                                    <button type="submit" class="btn-link btn-link-warning" onclick="return confirm('¿Desactivar usuario {{ $u->usuario }}?')">
                                        <i class="fa-solid fa-ban"></i> Desactivar
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="/usuarios/{{ $u->id }}/cambiar-estado" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="activo">
                                    <button type="submit" class="btn-link btn-link-success" onclick="return confirm('¿Activar usuario {{ $u->usuario }}?')">
                                        <i class="fa-solid fa-check-circle"></i> Activar
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="/usuarios/{{ $u->id }}" style="display:inline" onsubmit="return confirm('¿Eliminar a {{ $u->persona->nombre_completo }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-link btn-link-danger">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="tabla-vacia">
                        <i class="fa-solid fa-users-slash"></i>
                        <span>No hay usuarios administrativos registrados.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SECCIÓN 2: DOCENTES --}}
<div class="seccion-usuarios" style="margin-top: 40px;">
    <div class="seccion-header">
        <div class="seccion-titulo">
            <i class="fa-solid fa-chalkboard-user"></i> Docentes
        </div>
        <div class="seccion-sub">Gestión de profesores</div>
        <div style="display: flex; gap: 10px;">
            <button class="btn btn-secondary btn-sm" onclick="abrirModalCrear('doc')">
                <i class="fa-solid fa-plus"></i> Nuevo docente
            </button>
            <button class="btn btn-secondary btn-sm" onclick="abrirModalCargaMasiva()">
                <i class="fa-solid fa-upload"></i> Carga masiva
            </button>
        </div>
    </div>
    
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Persona</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios->filter(fn($u) => $u->rol->sigla === 'DOC') as $u)
                <tr>
                    <td style="text-align: left">
                        <div class="usuario-cell">
                            <div class="usuario-avatar">
                                {{ strtoupper(substr($u->persona->primer_nombre, 0, 1)) }}{{ strtoupper(substr($u->persona->primer_apellido, 0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $u->persona->nombre_completo }}</strong>
                                <div class="text-muted" style="font-size:.8rem">CC {{ $u->persona->documento }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center">{{ $u->usuario }}</td>
                    <td style="text-align: center">
                        <span class="badge badge-rol badge-rol-doc">DOC</span>
                    </td>
                    <td style="text-align: center">
                        @if($u->estado === 'activo')
                            <span class="badge" style="background: #E8F5E9; color: #2D7D32; padding: 4px 10px; border-radius: 20px;">
                                <i class="fa-solid fa-circle-check"></i> Activo
                            </span>
                        @else
                            <span class="badge" style="background: #FFEBEE; color: #C62828; padding: 4px 10px; border-radius: 20px;">
                                <i class="fa-solid fa-circle-xmark"></i> Inactivo
                            </span>
                        @endif
                    </td>
                    <td style="text-align: center">
                        <div style="display: flex; gap: 6px; justify-content: center;">
                            <button class="btn-link" onclick="abrirModalEditar(
                                {{ $u->id }},
                                '{{ addslashes($u->persona->primer_nombre) }}',
                                '{{ addslashes($u->persona->segundo_nombre) }}',
                                '{{ addslashes($u->persona->primer_apellido) }}',
                                '{{ addslashes($u->persona->segundo_apellido) }}',
                                '{{ $u->persona->documento }}',
                                '{{ addslashes($u->persona->correo) }}',
                                '{{ addslashes($u->persona->telefono) }}',
                                '{{ $u->usuario }}',
                                {{ $u->rol_id }}
                            )">
                                <i class="fa-solid fa-pen"></i> Editar
                            </button>
                            @if($u->estado === 'activo')   
                                <form method="POST" action="/usuarios/{{ $u->id }}/cambiar-estado" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="inactivo">
                                    <button type="submit" class="btn-link btn-link-warning" onclick="return confirm('¿Desactivar usuario {{ $u->usuario }}?')">
                                        <i class="fa-solid fa-ban"></i> Desactivar
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="/usuarios/{{ $u->id }}/cambiar-estado" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="activo">
                                    <button type="submit" class="btn-link btn-link-success" onclick="return confirm('¿Activar usuario {{ $u->usuario }}?')">
                                        <i class="fa-solid fa-check-circle"></i> Activar
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="/usuarios/{{ $u->id }}" style="display:inline" onsubmit="return confirm('¿Eliminar a {{ $u->persona->nombre_completo }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-link btn-link-danger">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="tabla-vacia">
                        <i class="fa-solid fa-users-slash"></i>
                        <span>No hay docentes registrados.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL CREAR --}}
<div class="modal-overlay" id="modal-crear" onclick="cerrarModal('modal-crear')">
    <div class="modal modal-lg" onclick="event.stopPropagation()">
        <div class="modal-header">
            <span class="modal-title" id="modal-crear-titulo"><i class="fa-solid fa-user-plus"></i> Nuevo usuario</span>
            <button class="modal-close" onclick="cerrarModal('modal-crear')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="/usuarios" id="form-crear-usuario">
            @csrf
            <div class="modal-section-title">Datos de la persona</div>
            <div class="form-grid-2">
                <div class="form-group"><label>Primer nombre <span class="req">*</span></label><input type="text" name="primer_nombre" id="crear_primer_nombre" placeholder="Ej: Dulfran" required oninput="generarUsuario(); validarCampo(this)"></div>
                <div class="form-group"><label>Segundo nombre <span class="opt">(opcional)</span></label><input type="text" name="segundo_nombre" id="crear_segundo_nombre" placeholder="Ej: Alberto" oninput="generarUsuario()"></div>
                <div class="form-group"><label>Primer apellido <span class="req">*</span></label><input type="text" name="primer_apellido" id="crear_primer_apellido" placeholder="Ej: Martínez" required oninput="generarUsuario()"></div>
                <div class="form-group"><label>Segundo apellido <span class="opt">(opcional)</span></label><input type="text" name="segundo_apellido" id="crear_segundo_apellido" placeholder="Ej: Pérez"></div>
                <div class="form-group">
                    <label>Documento (cédula) <span class="req">*</span></label>
                    <input type="text" name="documento" id="crear_documento" class="form-control" placeholder="Ej: 12345678" maxlength="10" required oninput="limpiarCedula(this); generarUsuario(); validarCampo(this)" onpaste="procesarPegadoCedula(event)">
                    <small class="text-muted" style="color:#6c757d; display:block; margin-top:4px;">Mínimo 6 dígitos, máximo 10. Solo números. No puede comenzar con 0.</small>
                    <span id="crear_documento_error" class="field-error" style="display:none;"></span>
                </div>
                <div class="form-group"><label>Correo electrónico</label><input type="email" name="correo" id="crear_correo" placeholder="Ej: dulfran@correo.com"></div>
                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" id="crear_telefono" class="form-control" placeholder="Ej: 3001234567" maxlength="10" oninput="limpiarTelefono(this); validarCampo(this)" onpaste="procesarPegadoTelefono(event)">
                    <small class="text-muted" style="color:#6c757d; display:block; margin-top:4px;">Exactamente 10 dígitos. Solo números.</small>
                    <span id="crear_telefono_error" class="field-error" style="display:none;"></span>
                </div>
            </div>

            <div class="modal-section-title" style="margin-top:1rem">Credenciales de acceso</div>
            <div class="form-grid-2">
                <div class="form-group"><label>Usuario <span class="req">*</span></label><input type="text" name="usuario" id="usuario_generado" placeholder="Se genera automáticamente" readonly style="background:#F0F1F4"><small style="color:#9DA3B4; display:block; margin-top:4px">Formato: [documento].[nombre] (ej: 1234567890.dulfran)</small></div>
                <div class="form-group"><label>Rol <span class="req">*</span></label><select name="rol_id" id="crear_rol_id" required><option value="">— Seleccionar rol —</option>@foreach($roles as $rol)<option value="{{ $rol->id }}">{{ $rol->sigla }} · {{ ucfirst($rol->nombre) }}</option>@endforeach</select></div>
            </div>

            <hr class="modal-sep">
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary" style="flex:1"><i class="fa-solid fa-floppy-disk"></i> Crear usuario</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModal('modal-crear')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL CARGA MASIVA --}}
<div class="modal-overlay" id="modal-carga-masiva" onclick="cerrarModal('modal-carga-masiva')">
    <div class="modal modal-lg" onclick="event.stopPropagation()">
        <div class="modal-header"><span class="modal-title"><i class="fa-solid fa-upload"></i> Carga masiva de docentes</span><button class="modal-close" onclick="cerrarModal('modal-carga-masiva')"><i class="fa-solid fa-xmark"></i></button></div>
        <form method="POST" action="/usuarios/carga-masiva" enctype="multipart/form-data">
            @csrf
            <div class="modal-section-title">Instrucciones</div>
            <div style="background: #E3F2FD; padding: 12px; border-radius: 8px; margin-bottom: 20px;"><p style="margin: 0; font-size: 12px;"><i class="fa-solid fa-info-circle"></i> El archivo debe tener las siguientes columnas en este orden:</p><ul style="margin: 8px 0 0 20px; font-size: 12px;"><li><strong>Primer nombre</strong> (requerido)</li><li><strong>Segundo nombre</strong> (opcional)</li><li><strong>Primer apellido</strong> (requerido)</li><li><strong>Segundo apellido</strong> (opcional)</li><li><strong>Documento (cédula)</strong> (requerido, 6-10 dígitos)</li><li><strong>Correo electrónico</strong> (opcional)</li><li><strong>Teléfono</strong> (opcional, 10 dígitos)</li></ul></div>
            <div class="form-group"><label>Archivo (CSV o Excel) <span class="req">*</span></label><input type="file" name="archivo" accept=".csv,.xlsx,.xls" required></div>
            <div class="modal-actions"><button type="submit" class="btn btn-primary" style="flex:1"><i class="fa-solid fa-upload"></i> Subir y crear docentes</button><button type="button" class="btn btn-outline" onclick="cerrarModal('modal-carga-masiva')">Cancelar</button></div>
        </form>
    </div>
</div>

{{-- MODAL EDITAR --}}
<div class="modal-overlay" id="modal-editar" onclick="cerrarModal('modal-editar')">
    <div class="modal modal-lg" onclick="event.stopPropagation()">
        <div class="modal-header"><span class="modal-title"><i class="fa-solid fa-pen"></i> Editar usuario</span><button class="modal-close" onclick="cerrarModal('modal-editar')"><i class="fa-solid fa-xmark"></i></button></div>
        <form method="POST" id="form-editar" action="">
            @csrf
            @method('PUT')
            <div class="modal-section-title">Datos de la persona</div>
            <div class="form-grid-2">
                <div class="form-group"><label>Primer nombre <span class="req">*</span></label><input type="text" name="primer_nombre" id="edit-primer_nombre" required></div>
                <div class="form-group"><label>Segundo nombre <span class="opt">(opcional)</span></label><input type="text" name="segundo_nombre" id="edit-segundo_nombre"></div>
                <div class="form-group"><label>Primer apellido <span class="req">*</span></label><input type="text" name="primer_apellido" id="edit-primer_apellido" required></div>
                <div class="form-group"><label>Segundo apellido <span class="opt">(opcional)</span></label><input type="text" name="segundo_apellido" id="edit-segundo_apellido"></div>
                <div class="form-group"><label>Documento (cédula) <span class="req">*</span></label><input type="text" name="documento" id="edit-documento" required></div>
                <div class="form-group"><label>Correo electrónico</label><input type="email" name="correo" id="edit-correo"></div>
                <div class="form-group"><label>Teléfono</label><input type="text" name="telefono" id="edit-telefono" maxlength="10"></div>
            </div>
            <div class="modal-section-title" style="margin-top:1rem">Credenciales de acceso</div>
            <div class="form-grid-2">
                <div class="form-group"><label>Usuario <span class="req">*</span></label><input type="text" name="usuario" id="edit-usuario" required></div>
                <div class="form-group">
                    <label>Nueva contraseña <span class="label-hint">(dejar en blanco para no cambiar)</span></label>
                    <div class="password-wrapper">
                        <input type="password" name="contraseña" id="edit_contraseña" placeholder="••••••••">
                        <button type="button" class="toggle-password" data-target="edit_contraseña">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group"><label>Rol <span class="req">*</span></label><select name="rol_id" id="edit-rol" required>@foreach($roles as $rol)<option value="{{ $rol->id }}">{{ $rol->sigla }} · {{ ucfirst($rol->nombre) }}</option>@endforeach</select></div>
            </div>
            <hr class="modal-sep">
            <div class="modal-actions"><button type="submit" class="btn btn-primary" style="flex:1"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button><button type="button" class="btn btn-outline" onclick="cerrarModal('modal-editar')">Cancelar</button></div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/modules/usuarios.js') }}"></script>
@endpush