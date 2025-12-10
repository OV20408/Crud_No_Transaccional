<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Clínico - {{ $voluntario->nombres }} {{ $voluntario->apellidos }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10pt;
            color: #2c3e50;
            line-height: 1.5;
            background: #1e3a8a;
            padding-top: 20px;
        }

        /* HEADER MEJORADO */
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.3);
            margin-bottom: 20px;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
        }

        .header-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 8px 24px;
            border-radius: 30px;
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 4px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            border: 2px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 26pt;
            margin-bottom: 8px;
            font-weight: 800;
            position: relative;
            z-index: 1;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.15);
        }

        .header p {
            font-size: 11pt;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            font-weight: 300;
        }

        /* DATOS PERSONALES - Estilo Clínico */
        .info-box {
            background: white;
            margin: 30px 20px 20px 20px;
            border: 2px solid #64748b;
        }

        .info-box-header {
            background: #475569;
            color: white;
            padding: 12px 20px;
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-box-body {
            padding: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 12px 15px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
        }

        .info-table td:first-child {
            background: #f1f5f9;
            font-weight: 700;
            color: #334155;
            width: 35%;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        .info-table td:last-child {
            color: #1e293b;
            font-weight: 500;
        }

        /* SECCIONES CLÍNICAS */
        .section {
            margin: 40px 20px 25px 20px;
        }

        .section.historial-clinico {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .section.historial-clinico .section-header {
            page-break-after: avoid;
        }

        .section.historial-clinico .section-body {
            page-break-before: avoid;
        }

        .section-header {
            background: #475569;
            color: white;
            padding: 12px 20px;
            font-size: 12pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0;
        }

        .section-body {
            background: white;
            border: 2px solid #64748b;
            border-top: none;
            padding: 20px;
        }

        /* CONTENEDOR DE EVALUACIÓN (2 reportes por página) */
        .evaluacion-container {
            page-break-after: always;
            page-break-inside: avoid;
            min-height: 850px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 0;
        }

        .evaluacion-container:last-child {
            page-break-after: auto;
        }

        /* REPORTES CLÍNICOS */
        .reporte-card {
            background: white;
            border: 2px solid #cbd5e1;
            padding: 18px;
            margin-bottom: 25px;
            page-break-inside: avoid;
            min-height: 340px;
            display: flex;
            flex-direction: column;
        }

        .reporte-card.fisico {
            border-left: 5px solid #3b82f6;
        }

        .reporte-card.psicologico {
            border-left: 5px solid #8b5cf6;
        }

        .reporte-tipo {
            background: #3b82f6;
            color: white;
            padding: 6px 14px;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .reporte-card.psicologico .reporte-tipo {
            background: #8b5cf6;
        }

        .reporte-fecha {
            color: #64748b;
            font-size: 8pt;
            margin-bottom: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .reporte-contenido {
            color: #334155;
            line-height: 1.7;
            padding: 15px;
            background: white;
            border: 1px solid #e2e8f0;
            font-size: 9pt;
            flex: 1;
        }

        .reporte-observaciones {
            margin-top: 12px;
            padding: 12px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            color: #92400e;
            font-size: 9pt;
        }

        .reporte-observaciones strong {
            display: block;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        /* TABLAS CLÍNICAS */
        .clinical-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #cbd5e1;
        }

        .clinical-table thead {
            background: #3b82f6;
            color: white;
        }

        .clinical-table th {
            padding: 12px 10px;
            text-align: left;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #2563eb;
        }

        .clinical-table td {
            padding: 10px;
            border: 1px solid #cbd5e1;
            font-size: 9pt;
            background: white;
        }

        .clinical-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        /* BADGES CLÍNICOS */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: #10b981;
            color: white;
        }

        .badge-warning {
            background: #f59e0b;
            color: white;
        }

        .badge-secondary {
            background: #6b7280;
            color: white;
        }

        /* NO DATA */
        .no-data {
            text-align: center;
            color: #94a3b8;
            font-style: italic;
            padding: 30px 20px;
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            font-size: 9pt;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
            border-top: 4px solid #475569;
            padding: 15px 20px;
            text-align: center;
            font-size: 8pt;
            color: white;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.15);
        }

        .footer strong {
            color: #94a3b8;
            font-weight: 700;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        /* Ajuste para páginas adicionales */
        @page {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-badge">GEVOPI</div>
        <h1>HISTORIAL DE EVALUACION CLINICA</h1>
        <p>Sistema de Gestión de Voluntarios de Protección Integral</p>
    </div>

    <!-- DATOS PERSONALES -->
    <div class="info-box">
        <div class="info-box-header">DATOS PERSONALES</div>
        <div class="info-box-body">
            <table class="info-table">
                <tr>
                    <td>Nombre Completo:</td>
                    <td>{{ $voluntario->nombres }} {{ $voluntario->apellidos }}</td>
                </tr>
                <tr>
                    <td>CI:</td>
                    <td>{{ $voluntario->ci }}</td>
                </tr>
                <tr>
                    <td>Fecha de Nacimiento:</td>
                    <td>{{ $voluntario->fecha_nacimiento ? \Carbon\Carbon::parse($voluntario->fecha_nacimiento)->format('d/m/Y') : 'N/D' }}</td>
                </tr>
                <tr>
                    <td>Edad:</td>
                    <td>{{ $voluntario->fecha_nacimiento ? \Carbon\Carbon::parse($voluntario->fecha_nacimiento)->age . ' años' : 'N/D' }}</td>
                </tr>
                <tr>
                    <td>Género:</td>
                    <td>{{ $voluntario->genero ?? 'N/D' }}</td>
                </tr>
                <tr>
                    <td>Tipo de Sangre:</td>
                    <td>{{ $voluntario->tipo_sangre ?? 'N/D' }}</td>
                </tr>
                <tr>
                    <td>Teléfono:</td>
                    <td>{{ $voluntario->telefono ?? 'N/D' }}</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{ $voluntario->email ?? 'N/D' }}</td>
                </tr>
                <tr>
                    <td>Dirección:</td>
                    <td>{{ $voluntario->direccion_domicilio ?? 'N/D' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- EVALUACIONES -->
    @if(count($reportes) > 0)
        @foreach($reportes as $reporte)
                    <div class="evaluacion-container">
                        {{-- Reporte Físico --}}
                        @if($reporte->resumen_fisico)
                            <div class="reporte-card fisico">
                                <span class="reporte-tipo">Evaluación Física</span>
                                <div class="reporte-fecha">
                                    Fecha: {{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y H:i') }}
                                </div>
                                <div class="reporte-contenido">
                                    {{ $reporte->resumen_fisico }}
                                </div>
                                @if($reporte->observaciones)
                                    <div class="reporte-observaciones">
                                        <strong>Observaciones:</strong>
                                        {{ $reporte->observaciones }}
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="reporte-card fisico">
                                <span class="reporte-tipo">Evaluación Física</span>
                                <div class="reporte-fecha">
                                    Fecha: {{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y H:i') }}
                                </div>
                                <div class="no-data">No hay evaluación física registrada</div>
                            </div>
                        @endif

                        {{-- Reporte Emocional --}}
                        @if($reporte->resumen_emocional)
                            <div class="reporte-card psicologico">
                                <span class="reporte-tipo">Evaluación Emocional</span>
                                <div class="reporte-fecha">
                                    Fecha: {{ \Carbon\Carbon::parse($reporte->fecha_generado)->timezone('America/La_Paz')->format('d/m/Y H:i') }}
                                </div>
                                <div class="reporte-contenido">
                                    {{ $reporte->resumen_emocional }}
                                </div>
                            </div>
                        @else
                            <div class="reporte-card psicologico">
                                <span class="reporte-tipo">Evaluación Emocional</span>
                                <div class="reporte-fecha">
                                    Fecha: {{ \Carbon\Carbon::parse($reporte->fecha_generado)->timezone('America/La_Paz')->format('d/m/Y H:i') }}
                                </div>
                                <div class="no-data">No hay evaluación emocional registrada</div>
                            </div>
                        @endif
                </div>
            @endforeach
        @else
            <div class="no-data">No hay reportes clínicos registrados</div>
        @endif

    <!-- CAPACITACIONES Y PROGRESO -->
    <div class="section">
        <div class="section-header">CAPACITACIONES</div>
        <div class="section-body">
            @if(count($capacitaciones) > 0)
                <table class="clinical-table">
                    <thead>
                        <tr>
                            <th>Capacitación</th>
                            <th>Curso</th>
                            <th>Etapa</th>
                            <th>Estado</th>
                            <th>Fecha Inicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($capacitaciones as $cap)
                            <tr>
                                <td><strong>{{ $cap->capacitacion }}</strong></td>
                                <td>{{ $cap->curso }}</td>
                                <td>{{ $cap->etapa }}</td>
                                <td>
                                    @if($cap->estado == 'completado')
                                        <span class="badge badge-success">Completado</span>
                                    @elseif($cap->estado == 'en_progreso')
                                        <span class="badge badge-warning">En Progreso</span>
                                    @else
                                        <span class="badge badge-secondary">No Iniciado</span>
                                    @endif
                                </td>
                                <td>{{ $cap->fecha_inicio ? \Carbon\Carbon::parse($cap->fecha_inicio)->format('d/m/Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">No hay capacitaciones asignadas</div>
            @endif
        </div>
    </div>

    <!-- NECESIDADES IDENTIFICADAS -->
    <div class="section">
        <div class="section-header">NECESIDADES</div>
        <div class="section-body">
            @if(count($necesidades) > 0)
                <table class="clinical-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Fecha Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($necesidades as $nec)
                            <tr>
                                <td><strong>{{ $nec->tipo }}</strong></td>
                                <td>{{ $nec->descripcion }}</td>
                                <td>{{ \Carbon\Carbon::parse($nec->fecha_generado)->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-data">No hay necesidades identificadas</div>
            @endif
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
    <p>
        <strong>GEVOPI - Sistema de Gestión de Voluntarios de Protección Integral</strong><br>
        Documento generado el {{ \Carbon\Carbon::now()->timezone('America/La_Paz')->format('d/m/Y H:i') }}
    </p>
    <div class="page-number"></div>
</div>
</body>
</html>