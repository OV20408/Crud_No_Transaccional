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
            font-family: 'Segoe UI', 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #2c3e50;
            line-height: 1.5;
            background: #0c4a6e;
        }

        /* HEADER MEJORADO */
        /* HEADER MEJORADO */
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.4);
        }

        .header::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .header-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            padding: 8px 24px;
            border-radius: 30px;
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 4px;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
            border: 2px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .header h1 {
            font-size: 26pt;
            margin-bottom: 8px;
            font-weight: 800;
            position: relative;
            z-index: 1;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        }

        .header p {
            font-size: 11pt;
            opacity: 0.95;
            position: relative;
            z-index: 1;
            font-weight: 300;
        }

        /* DATOS PERSONALES */
        .info-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px 20px;
            border-top: 5px solid #4a5fd9;
        }

        .info-box h2 {
            color: #4a5fd9;
            font-size: 15pt;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #e5e7eb;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box h2::before {
            content: '👤';
            font-size: 18pt;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 10px;
        }

        .info-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4a5fd9;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(74, 95, 217, 0.15);
        }

        .info-label {
            font-weight: 700;
            color: #4a5fd9;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            color: #1e293b;
            font-size: 10pt;
            font-weight: 500;
        }

        /* SECCIONES */
        .section {
            margin: 30px 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background: linear-gradient(135deg, #4a5fd9 0%, #7c3aed 100%);
            color: white;
            padding: 16px 24px;
            font-size: 13pt;
            font-weight: 700;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(74, 95, 217, 0.3);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* REPORTES */
        .reporte-card {
            background: white;
            border-left: 6px solid #4a5fd9;
            padding: 20px;
            margin-bottom: 18px;
            page-break-inside: avoid;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .reporte-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .reporte-card.psicologico {
            border-left-color: #7c3aed;
        }

        .reporte-header {
            font-weight: 700;
            color: #4a5fd9;
            margin-bottom: 12px;
            font-size: 11pt;
            display: inline-block;
            background: linear-gradient(135deg, #4a5fd9 0%, #7c3aed 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 9pt;
        }

        .reporte-card.psicologico .reporte-header {
            background: linear-gradient(135deg, #7c3aed 0%, #c026d3 100%);
        }

        .reporte-fecha {
            color: #64748b;
            font-size: 9pt;
            margin-bottom: 15px;
            padding: 8px 0;
            font-weight: 500;
        }

        .reporte-contenido {
            color: #334155;
            line-height: 1.7;
            padding: 18px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        /* TABLAS */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .table th {
            background: linear-gradient(135deg, #4a5fd9 0%, #7c3aed 100%);
            color: white;
            padding: 16px 14px;
            text-align: left;
            font-size: 9pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10pt;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.01);
        }

        .table tr:nth-child(even) {
            background: #fafbfc;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .badge-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .badge-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .badge-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-top: 4px solid #4a5fd9;
            padding: 18px 20px;
            text-align: center;
            font-size: 9pt;
            color: white;
            box-shadow: 0 -4px 16px rgba(0, 0, 0, 0.2);
        }

        .footer strong {
            color: #a78bfa;
            font-weight: 700;
        }

        .page-number:before {
            content: "Página " counter(page);
        }

        /* NO DATA */
        .no-data {
            text-align: center;
            color: #94a3b8;
            font-style: italic;
            padding: 40px 20px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 10px;
            border: 2px dashed #cbd5e1;
        }

        .no-data::before {
            content: '📋';
            display: block;
            font-size: 36pt;
            margin-bottom: 10px;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <div class="header-badge">GEVOPI</div>
        <h1>HISTORIAL DE EVALUACIÓN CLÍNICA</h1>
        <p>Sistema de Gestión de Voluntarios de Protección Integral</p>
    </div>

    {{-- DATOS PERSONALES --}}
    <div class="info-box">
        <h2>DATOS PERSONALES</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nombre Completo</div>
                <div class="info-value">{{ $voluntario->nombres }} {{ $voluntario->apellidos }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">CI</div>
                <div class="info-value">{{ $voluntario->ci }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Fecha de Nacimiento</div>
                <div class="info-value">{{ $voluntario->fecha_nacimiento ? \Carbon\Carbon::parse($voluntario->fecha_nacimiento)->format('d/m/Y') : 'N/D' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Género</div>
                <div class="info-value">{{ $voluntario->genero ?? 'N/D' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tipo de Sangre</div>
                <div class="info-value">{{ $voluntario->tipo_sangre ?? 'N/D' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Teléfono</div>
                <div class="info-value">{{ $voluntario->telefono ?? 'N/D' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $voluntario->email ?? 'N/D' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Dirección</div>
                <div class="info-value">{{ $voluntario->direccion_domicilio ?? 'N/D' }}</div>
            </div>
        </div>
    </div>

    {{-- HISTORIAL CLÍNICO --}}
    <div class="section">
        <div class="section-title">🏥 HISTORIAL CLÍNICO</div>
        
        @if(count($reportes) > 0)
            @foreach($reportes as $reporte)
                {{-- Reporte Físico --}}
                @if($reporte->resumen_fisico)
                    <div class="reporte-card">
                        <div class="reporte-header">Evaluación Física</div>
                        <div class="reporte-fecha">
                            📅 Fecha: {{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y H:i') }}
                        </div>
                        <div class="reporte-contenido">
                            {{ $reporte->resumen_fisico }}
                        </div>
                        @if($reporte->observaciones)
                            <div style="margin-top: 12px; padding: 12px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; color: #92400e; font-size: 9pt;">
                                <strong>⚠️ Observaciones:</strong> {{ $reporte->observaciones }}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Reporte Psicológico --}}
                @if($reporte->resumen_emocional)
                    <div class="reporte-card psicologico">
                        <div class="reporte-header">Evaluación Psicológica</div>
                        <div class="reporte-fecha">
                            📅 Fecha: {{ \Carbon\Carbon::parse($reporte->fecha_generado)->format('d/m/Y H:i') }}
                        </div>
                        <div class="reporte-contenido">
                            {{ $reporte->resumen_emocional }}
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <div class="no-data">No hay reportes clínicos registrados</div>
        @endif
    </div>

    {{-- CAPACITACIONES Y PROGRESO --}}
    <div class="section">
        <div class="section-title">📚 CAPACITACIONES Y PROGRESO</div>
        
        @if(count($capacitaciones) > 0)
            <table class="table">
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
                                    <span class="badge badge-success">✓ Completado</span>
                                @elseif($cap->estado == 'en_progreso')
                                    <span class="badge badge-warning">⟳ En Progreso</span>
                                @else
                                    <span class="badge badge-secondary">○ No Iniciado</span>
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

    {{-- NECESIDADES IDENTIFICADAS --}}
    <div class="section">
        <div class="section-title">🎯 NECESIDADES IDENTIFICADAS</div>
        
        @if(count($necesidades) > 0)
            <table class="table">
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

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            <strong>GEVOPI - Sistema de Gestión de Voluntarios de Protección Integral</strong><br>
            Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </p>
        <div class="page-number"></div>
    </div>
</body>
</html>