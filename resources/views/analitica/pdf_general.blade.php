<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Alertas</title>
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
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2D7D32;
        }
        .header h1 {
            color: #2D7D32;
            margin: 0;
            font-size: 22px;
            font-weight: bold;
        }
        .header p {
            color: #6c757d;
            margin: 5px 0 0;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        /* Tarjetas de estadísticas - 2x2 */
        .stats-table {
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        .stats-table td {
            border: none;
            padding: 8px;
            vertical-align: top;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px 15px;
            border-radius: 10px;
            text-align: center;
            border-top: 4px solid #2D7D32;
        }
        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #2D7D32;
        }
        .stat-label {
            font-size: 11px;
            color: #6c757d;
            margin-top: 6px;
        }
        
        /* Tablas de datos */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #dee2e6;
            padding: 8px 6px;
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
        
        .col-fecha { width: 8%; }
        .col-documento { width: 10%; }
        .col-estudiante { width: 15%; }
        .col-carrera { width: 15%; }
        .col-tipo { width: 8%; }
        .col-descripcion { width: 29%; }
        .col-registrado { width: 15%; }
        
        .title-section {
            font-size: 15px;
            font-weight: bold;
            color: #2D7D32;
            margin: 20px 0 10px;
            padding-bottom: 5px;
            border-bottom: 3px solid #2D7D32;
        }
        .subtitle-section {
            font-size: 11px;
            font-weight: bold;
            color: #1565C0;
            margin: 8px 0 12px;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #dee2e6;
            font-size: 9px;
            color: #6c757d;
        }
        .generado-por {
            margin-top: 15px;
            font-size: 9px;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Reporte General de Alertas</h1>
        <p>Sistema de Seguimiento CRU - Politécnico Colombiano Jaime Isaza Cadavid</p>
        <p><strong>Período: {{ $nombrePeriodo }}</strong></p>
        <p>Fecha de generación: {{ $fechaGeneracion }}</p>
    </div>

    <!-- TARJETAS EN 2 FILAS DE 2 -->
    <table class="stats-table">
        <tr>
            <td width="50%">
                <div class="stat-card">
                    <div class="stat-number">{{ $totalAlertas }}</div>
                    <div class="stat-label">Total Alertas</div>
                </div>
            </td>
            <td width="50%">
                <div class="stat-card" style="border-top-color: #E65100">
                    <div class="stat-number">{{ $alertasPorEstado['pendiente'] ?? 0 }}</div>
                    <div class="stat-label">Pendientes</div>
                </div>
            </td>
        </tr>
        <tr>
            <td width="50%">
                <div class="stat-card" style="border-top-color: #1565C0">
                    <div class="stat-number">{{ $alertasPorEstado['en_seguimiento'] ?? 0 }}</div>
                    <div class="stat-label">En Seguimiento</div>
                </div>
            </td>
            <td width="50%">
                <div class="stat-card" style="border-top-color: #2D7D32">
                    <div class="stat-number">{{ $alertasPorEstado['cerrado'] ?? 0 }}</div>
                    <div class="stat-label">Cerrados</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ALERTAS POR TIPO -->
    <div class="title-section">Alertas por Tipo</div>
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th style="width: 50%;">Tipo</th>
            <th style="width: 25%;">Cantidad</th>
            <th style="width: 25%;">Porcentaje</th>
        </tr>
        <tr>
            <td><strong>Académico</strong></td>
            <td class="text-center">{{ $alertasPorTipo['academico'] ?? 0 }}</td>
            <td class="text-center">{{ $totalAlertas > 0 ? round((($alertasPorTipo['academico'] ?? 0) / $totalAlertas) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td><strong>Asistencia</strong></td>
            <td class="text-center">{{ $alertasPorTipo['asistencia'] ?? 0 }}</td>
            <td class="text-center">{{ $totalAlertas > 0 ? round((($alertasPorTipo['asistencia'] ?? 0) / $totalAlertas) * 100, 1) : 0 }}%</td>
        </tr>
        <tr>
            <td><strong>Comportamiento</strong></td>
            <td class="text-center">{{ $alertasPorTipo['comportamiento'] ?? 0 }}</td>
            <td class="text-center">{{ $totalAlertas > 0 ? round((($alertasPorTipo['comportamiento'] ?? 0) / $totalAlertas) * 100, 1) : 0 }}%</td>
        </tr>
    </table>

    <!-- ALERTAS POR PROGRAMA -->
    <div class="title-section" style="margin-top: 25px;">Alertas por Programa</div>
    @if(count($alertasPorPrograma) > 0)
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th style="width: 50%;">Programa</th>
            <th style="width: 25%;">Cantidad</th>
            <th style="width: 25%;">Porcentaje</th>
        </tr>
        @foreach($alertasPorPrograma as $programa)
        <tr>
            <td><strong>{{ $programa['nombre'] }}</strong></td>
            <td class="text-center">{{ $programa['total'] }}</td>
            <td class="text-center">{{ $totalAlertas > 0 ? round(($programa['total'] / $totalAlertas) * 100, 1) : 0 }}%</td>
        </tr>
        @endforeach
    </table>
    @else
        <p style="color: #6c757d; text-align: center; padding: 20px;">No hay alertas registradas por programa</p>
    @endif

    <!-- ALERTAS PENDIENTES -->
    @if($alertasPendientes->count() > 0)
    <div class="page-break"></div>
    
    <div class="title-section">Alertas Pendientes</div>
    <div class="subtitle-section">Total: {{ $alertasPendientes->count() }} alertas</div>
    
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th class="col-fecha">Fecha</th>
            <th class="col-documento">Documento</th>
            <th class="col-estudiante">Estudiante</th>
            <th class="col-carrera">Carrera</th>
            <th class="col-tipo">Tipo</th>
            <th class="col-descripcion">Descripción</th>
            <th class="col-registrado">Registrado por</th>
        </tr>
        @foreach($alertasPendientes as $alerta)
        <tr>
            <td class="text-center">{{ \Carbon\Carbon::parse($alerta->creado_en)->format('d/m/Y') }}</td>
            <td class="text-center">{{ $alerta->estudiante->documento }}</td>
            <td><strong>{{ $alerta->estudiante->nombre }}</strong></td>
            <td>{{ $alerta->carrera_nombre }}</td>
            <td class="text-center">{{ ucfirst($alerta->tipo) }}</td>
            <td style="font-size: 8.5px; line-height: 1.4;">{{ $alerta->descripcion }}</td>
            <td>{{ $alerta->usuario->nombre_completo ?? 'Sistema' }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- ALERTAS EN SEGUIMIENTO -->
    @if($alertasSeguimiento->count() > 0)
    <div class="page-break"></div>
    
    <div class="title-section">Alertas en Seguimiento</div>
    <div class="subtitle-section">Total: {{ $alertasSeguimiento->count() }} alertas</div>
    
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th class="col-fecha">Fecha</th>
            <th class="col-documento">Documento</th>
            <th class="col-estudiante">Estudiante</th>
            <th class="col-carrera">Carrera</th>
            <th class="col-tipo">Tipo</th>
            <th class="col-descripcion">Descripción</th>
            <th class="col-registrado">Registrado por</th>
        </tr>
        @foreach($alertasSeguimiento as $alerta)
        <tr>
            <td class="text-center">{{ \Carbon\Carbon::parse($alerta->creado_en)->format('d/m/Y') }}</td>
            <td class="text-center">{{ $alerta->estudiante->documento }}</td>
            <td><strong>{{ $alerta->estudiante->nombre }}</strong></td>
            <td>{{ $alerta->carrera_nombre }}</td>
            <td class="text-center">{{ ucfirst($alerta->tipo) }}</td>
            <td style="font-size: 8.5px; line-height: 1.4;">{{ $alerta->descripcion }}</td>
            <td>{{ $alerta->usuario->nombre_completo ?? 'Sistema' }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    <!-- ALERTAS CERRADAS -->
    @if($alertasCerrados->count() > 0)
    <div class="page-break"></div>
    
    <div class="title-section">Alertas Cerradas</div>
    <div class="subtitle-section">Total: {{ $alertasCerrados->count() }} alertas</div>
    
    <table class="data-table">
        <tr style="background: #f8f9fa; font-weight: bold;">
            <th class="col-fecha">Fecha</th>
            <th class="col-documento">Documento</th>
            <th class="col-estudiante">Estudiante</th>
            <th class="col-carrera">Carrera</th>
            <th class="col-tipo">Tipo</th>
            <th class="col-descripcion">Descripción</th>
            <th class="col-registrado">Registrado por</th>
        </tr>
        @foreach($alertasCerrados as $alerta)
        <tr>
            <td class="text-center">{{ \Carbon\Carbon::parse($alerta->creado_en)->format('d/m/Y') }}</td>
            <td class="text-center">{{ $alerta->estudiante->documento }}</td>
            <td><strong>{{ $alerta->estudiante->nombre }}</strong></td>
            <td>{{ $alerta->carrera_nombre }}</td>
            <td class="text-center">{{ ucfirst($alerta->tipo) }}</td>
            <td style="font-size: 8.5px; line-height: 1.4;">{{ $alerta->descripcion }}</td>
            <td>{{ $alerta->usuario->nombre_completo ?? 'Sistema' }}</td>
        </tr>
        @endforeach
    </table>
    @endif

    @if($totalAlertas == 0)
    <div class="title-section">Listado de Alertas</div>
    <p style="text-align: center; color: #6c757d; padding: 40px;">No hay alertas registradas en este período.</p>
    @endif

    <div class="footer">
        <p>Reporte generado por el Sistema de Seguimiento CRU</p>
        <p>Polotécnico Colombiano Jaime Isaza Cadavid - Sede Urabá</p>
        <div class="generado-por">
            Generado por: {{ session('nombre_completo') ?? session('usuario') }} ({{ ucfirst(session('rol')) }})
        </div>
    </div>

</body>
</html>