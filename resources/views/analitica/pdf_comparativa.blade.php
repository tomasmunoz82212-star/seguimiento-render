<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Comparativa: {{ $periodoAnterior->nombre }} vs {{ $periodoActual->nombre }}</title>
    <style>
        * {
            font-family: 'DejaVu Sans', 'Segoe UI', 'Helvetica', Arial, sans-serif !important;
        }
        
        body {
            margin: 20px;
            color: #1A1D27;
            font-size: 11px;
        }
        
        @page {
            size: landscape;
            margin: 15mm;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #2D7D32;
        }
        .header h1 {
            color: #2D7D32;
            margin: 0;
            font-size: 20px;
        }
        .header h3 {
            margin: 5px 0;
            color: #1565C0;
            font-size: 13px;
        }
        .header p {
            color: #6c757d;
            margin: 0;
            font-size: 9px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Tarjetas - 2 columnas (usando tabla) */
        .stats-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .stats-table td {
            border: none;
            padding: 6px;
            vertical-align: top;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            border-top: 3px solid #2D7D32;
        }
        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #2D7D32;
        }
        .stat-label {
            font-size: 9px;
            color: #6c757d;
            margin-top: 4px;
        }
        .stat-card-desertor { border-top-color: #E65100; }
        .stat-card-cambio { border-top-color: #1565C0; }
        .stat-card-retencion { border-top-color: #6A1B9A; }
        .stat-card-nuevo { border-top-color: #F2C200; }
        
        /* Tablas de datos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        .data-table th, .data-table td {
            border: 1px solid #dee2e6;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }
        .data-table th {
            background: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .data-table .text-center {
            text-align: center;
        }
        
        .col-doc { width: 12%; }
        .col-nombre { width: 20%; }
        .col-tel { width: 12%; }
        .col-correo { width: 25%; }
        .col-semestre { width: 8%; }
        .col-reportes { width: 8%; }
        .col-estado { width: 15%; }
        .col-carrera-ant { width: 20%; }
        .col-carrera-act { width: 20%; }
        
        .title-section {
            font-size: 14px;
            font-weight: bold;
            color: #2D7D32;
            margin: 20px 0 10px;
            padding-bottom: 4px;
            border-bottom: 2px solid #2D7D32;
        }
        .subtitle-section {
            font-size: 12px;
            font-weight: bold;
            color: #1565C0;
            margin: 15px 0 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid #1565C0;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #6c757d;
        }
        .text-center {
            text-align: center;
        }
        .nota-explicativa {
            background: #FFF8E1;
            padding: 8px 12px;
            border-radius: 6px;
            margin: 10px 0;
            font-size: 10px;
            border-left: 3px solid #F2C200;
        }
        .generado-por {
            margin-top: 15px;
            font-size: 9px;
            text-align: right;
            font-style: italic;
        }
        .leyenda {
            background: #F8F9FA;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 6px 12px;
            margin: 10px 0;
            font-size: 9px;
            display: inline-block;
        }
        .leyenda span {
            margin-right: 12px;
            white-space: nowrap;
        }
        .resumen-info {
            background: #E3F2FD;
            padding: 12px;
            border-radius: 8px;
            font-size: 10px;
            line-height: 1.4;
            margin-bottom: 0;
        }
        .resumen-info strong {
            color: #1565C0;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte Comparativo de Períodos</h1>
        <h3>{{ $periodoAnterior->nombre }} vs {{ $periodoActual->nombre }}</h3>
        <p>Fecha de generación: {{ $fechaGeneracion }}</p>
    </div>

    <!-- TARJETAS EN 2 COLUMNAS (3 filas de 2) -->
    <table class="stats-table">
        <tr>
            <td width="50%"><div class="stat-card"><div class="stat-number">{{ $totalAnterior }}</div><div class="stat-label">Estudiantes {{ $periodoAnterior->nombre }}</div></div></td>
            <td width="50%"><div class="stat-card"><div class="stat-number">{{ $totalActual }}</div><div class="stat-label">Estudiantes {{ $periodoActual->nombre }}</div></div></td>
        </tr>
        <tr>
            <td width="50%"><div class="stat-card stat-card-nuevo"><div class="stat-number">{{ $totalNuevos }}</div><div class="stat-label">Nuevos Estudiantes</div></div></td>
            <td width="50%"><div class="stat-card stat-card-desertor"><div class="stat-number">{{ $totalDesertores }}</div><div class="stat-label">Desertores</div></div></td>
        </tr>
        <tr>
            <td width="50%"><div class="stat-card stat-card-cambio"><div class="stat-number">{{ $totalCambiosCarrera }}</div><div class="stat-label">Cambios de Carrera</div></div></td>
            <td width="50%"><div class="stat-card stat-card-retencion"><div class="stat-number">{{ $porcentajeRetencion }}%</div><div class="stat-label">Tasa de Retención</div></div></td>
        </tr>
    </table>

    <!-- Resumen Ejecutivo -->
    <div class="resumen-info">
        <strong>📊 Resumen Ejecutivo</strong><br>
        De <strong>{{ $totalAnterior }}</strong> estudiantes matriculados en <strong>{{ $periodoAnterior->nombre }}</strong>, 
        <strong>{{ $totalActivos }}</strong> continúan activos, <strong>{{ $totalDesertores }}</strong> desertaron, 
        <strong>{{ $totalCambiosCarrera }}</strong> cambiaron de programa. 
        Además, <strong>{{ $totalNuevos }}</strong> nuevos estudiantes ingresaron en <strong>{{ $periodoActual->nombre }}</strong>.
    </div>

    <!-- NUEVOS ESTUDIANTES -->
    @if($totalNuevos > 0)
    <div class="page-break"></div>
    
    <div class="title-section">Nuevos Estudiantes ({{ $totalNuevos }})</div>
    
    @foreach($nuevosPorPrograma as $programa => $estudiantes)
    <div class="subtitle-section">{{ $programa }} ({{ count($estudiantes) }} nuevos)</div>
    
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th class="col-doc">Documento</th>
            <th class="col-nombre">Estudiante</th>
            <th class="col-tel">Teléfono</th>
            <th class="col-correo">Correo</th>
            <th class="col-semestre">Semestre</th>
        </tr>
        @foreach($estudiantes as $n)
        <tr>
            <td class="text-center">{{ $n['documento'] }}</td>
            <td><strong>{{ $n['estudiante']->nombre }}</strong></td>
            <td>{{ $n['estudiante']->telefono ?? '—' }}</td>
            <td>{{ $n['estudiante']->correo ?? '—' }}</td>
            <td class="text-center">{{ $n['semestre'] }}°</td>
        </tr>
        @endforeach
    </table>
    @endforeach
    @endif

    <!-- DESERTORES -->
    @if($totalDesertores > 0)
    <div class="page-break"></div>
    
    <div class="title-section">Desertores ({{ $totalDesertores }})</div>
    
    <div class="leyenda">
        <span>[C] Cerrados</span>
        <span>[S] En seguimiento</span>
        <span>[P] Pendientes</span>
    </div>
    
    @foreach($desertoresPorPrograma as $programa => $estudiantes)
    <div class="subtitle-section">{{ $programa }} ({{ count($estudiantes) }} desertores)</div>
    
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th class="col-doc">Documento</th>
            <th class="col-nombre">Estudiante</th>
            <th class="col-tel">Teléfono</th>
            <th class="col-correo">Correo</th>
            <th class="col-semestre">Semestre</th>
            <th class="col-reportes">Reportes</th>
            <th class="col-estado">Estado</th>
        </tr>
        @foreach($estudiantes as $d)
        <tr>
            <td class="text-center">{{ $d['documento'] }}</td>
            <td><strong>{{ $d['estudiante']->nombre }}</strong></td>
            <td>{{ $d['estudiante']->telefono ?? '—' }}</td>
            <td>{{ $d['estudiante']->correo ?? '—' }}</td>
            <td class="text-center">{{ $d['semestre'] }}°</td>
            <td class="text-center">{{ $d['total_reportes'] }}</td>
            <td>
                @if($d['total_reportes'] > 0)
                    [C]: {{ $d['reportes_cerrados'] }} - [S]: {{ $d['reportes_seguimiento'] }} - [P]: {{ $d['reportes_pendientes'] }}
                @else
                    Sin reportes
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endforeach
    @endif

    <!-- CAMBIOS DE CARRERA -->
    @if($totalCambiosCarrera > 0)
    <div class="page-break"></div>
    
    <div class="title-section">Cambios de Carrera ({{ $totalCambiosCarrera }})</div>
    
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th class="col-doc">Documento</th>
            <th class="col-nombre">Estudiante</th>
            <th class="col-tel">Teléfono</th>
            <th class="col-correo">Correo</th>
            <th class="col-carrera-ant">Carrera Anterior</th>
            <th class="col-carrera-act">Carrera Actual</th>
            <th class="col-reportes">Reportes</th>
        </tr>
        @foreach($cambiosCarrera as $c)
        <tr>
            <td class="text-center">{{ $c['documento'] }}</td>
            <td><strong>{{ $c['estudiante']->nombre }}</strong></td>
            <td>{{ $c['estudiante']->telefono ?? '—' }}</td>
            <td>{{ $c['estudiante']->correo ?? '—' }}</td>
            <td>{{ $c['programa_anterior'] }}</td>
            <td>{{ $c['programa_actual'] }}</td>
            <td class="text-center">{{ $c['total_reportes'] }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- POSIBLES GRADUADOS -->
    @if($totalGraduados > 0)
    <div class="page-break"></div>
    
    <div class="title-section">Posibles Graduados ({{ $totalGraduados }})</div>
    
    <div class="nota-explicativa">
        <strong>¿Qué significa "Posibles Graduados"?</strong><br>
        Son estudiantes que cursaron el último semestre de su programa 
        ({{ $periodoAnterior->nombre }}) y no aparecen matriculados en el período siguiente. 
        Esto sugiere que pudieron haber completado su plan de estudios satisfactoriamente.
    </div>
    
    <div style="background: #E8F5E9; padding: 12px; border-radius: 8px; margin: 10px 0;">
        <strong>Resumen por programa académico:</strong>
        <ul style="margin: 8px 0 0 20px;">
            @foreach($graduadosPorPrograma as $programa => $cantidad)
            <li><strong>{{ $programa }}</strong>: {{ $cantidad }} posibles graduados</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <!-- MENSAJE SI NO HAY DATOS -->
    @if($totalDesertores == 0 && $totalGraduados == 0 && $totalCambiosCarrera == 0 && $totalNuevos == 0)
    <div class="page-break"></div>
    <div style="background: #FFF3E0; padding: 12px; text-align: center; margin-top: 20px; border-radius: 8px;">
        <strong>No se encontraron cambios significativos</strong><br>
        Todos los estudiantes del período {{ $periodoAnterior->nombre }} continúan activos en el mismo programa durante el período {{ $periodoActual->nombre }}.
    </div>
    @endif
    
    <div class="footer">
        <p>Reporte generado por el Sistema de Seguimiento CRU - Politécnico Colombiano Jaime Isaza Cadavid</p>
        <p>* Un desertor es un estudiante que no continúa y no ha completado los semestres mínimos (10 profesionales / 6 tecnologías)</p>
        <p>* Un posible graduado es un estudiante que completó el plan de estudios y ya no aparece matriculado en el período siguiente</p>
        <p>* Un nuevo estudiante es aquel que aparece matriculado en el período actual pero no en el anterior</p>
        
        <div class="generado-por">
            Reporte generado por: {{ session('nombre_completo') ?? session('usuario') }} ({{ ucfirst(session('rol')) }})
        </div>
    </div>

</body>
</html>