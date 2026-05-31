<div id="userModal" class="modal-simple hidden">
    <div class="modal-simple-overlay" onclick="cerrarModal()"></div>
    <div class="modal-simple-box">
        <div class="modal-simple-header">
            <h3><i class="fa-solid fa-user-circle"></i> Mi información</h3>
            <button class="modal-simple-close" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="modal-simple-body">
            <div class="info-row-modal">
                <span class="info-label-modal">Nombre completo:</span>
                <span class="info-value-modal">{{ session('nombre_completo') ?? session('usuario') }}</span>
            </div>
            <div class="info-row-modal">
                <span class="info-label-modal">Usuario:</span>
                <span class="info-value-modal">{{ session('usuario') }}</span>
            </div>
            <div class="info-row-modal">
                <span class="info-label-modal">Rol:</span>
                <span class="info-value-modal">{{ ucfirst(session('rol')) }}</span>
            </div>
            <div class="info-row-modal">
                <span class="info-label-modal">Correo electrónico:</span>
                <span class="info-value-modal">
                    {{ session('correo') ?: 'No registrado' }}
                </span>
            </div>
        </div>
        <div class="modal-simple-footer">
            <button class="btn-modal-close" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>
</div>