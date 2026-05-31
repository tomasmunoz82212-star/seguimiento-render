@if($nivel == 'verde')
    <span class="badge" style="background:#E8F5E9; color:#2D7D32;">
        <i class="fa-solid fa-circle"></i> A tiempo
    </span>
@elseif($nivel == 'naranja')
    <span class="badge" style="background:#FFF3E0; color:#E65100;">
        <i class="fa-solid fa-clock"></i> Próximo a vencer
    </span>
@elseif($nivel == 'rojo')
    <span class="badge" style="background:#FFEBEE; color:#C62828;">
        <i class="fa-solid fa-exclamation-triangle"></i> Urgente
    </span>
@elseif($nivel == 'expirado')
    <span class="badge" style="background:#9DA3B4; color:#FFFFFF;">
        <i class="fa-solid fa-calendar-times"></i> Expirado
    </span>
@endif