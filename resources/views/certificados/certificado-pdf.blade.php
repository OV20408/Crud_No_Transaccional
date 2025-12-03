<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado - {{ $voluntario->nombres }} {{ $voluntario->apellidos }}</title>
    <style>
        @page {
            margin: 0;
            size: landscape; /* Certificado horizontal */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            width: 297mm;
            height: 210mm;
            position: relative;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        /* Marco decorativo */
        .border-frame {
            position: absolute;
            top: 15mm;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            border: 8px solid #0072C6;
            border-image: linear-gradient(135deg, #0072C6 0%, #00A4EF 100%) 1;
        }

        .inner-border {
            position: absolute;
            top: 20mm;
            left: 20mm;
            right: 20mm;
            bottom: 20mm;
            border: 2px solid #0072C6;
            padding: 20mm;
        }

        /* Header con logos */
        .header {
            text-align: center;
            margin-bottom: 15mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            flex: 1;
        }

        .logo-text {
            font-size: 24pt;
            font-weight: bold;
            color: #0072C6;
            letter-spacing: 3px;
        }

        /* Título principal */
        .titulo-certificado {
            text-align: center;
            font-size: 42pt;
            font-weight: bold;
            color: #0072C6;
            text-transform: uppercase;
            letter-spacing: 8px;
            margin: 20mm 0 10mm 0;
            font-family: 'Georgia', serif;
        }

        .subtitulo {
            text-align: center;
            font-size: 16pt;
            color: #495057;
            margin-bottom: 15mm;
            font-style: italic;
        }

        .contenido {
            text-align: center;
            padding: 0 30mm;
            margin-top: -15mm;  /* ← CAMBIAR de -10mm a -15mm (sube TODO más) */
        }

        .texto-otorga {
            font-size: 14pt;
            color: #495057;
            margin-bottom: 3mm;  /* ← CAMBIAR de 4mm a 3mm */
        }

        .nombre-voluntario {
            font-size: 32pt;
            font-weight: bold;
            color: #212529;
            margin: 4mm 0;  /* ← CAMBIAR de 6mm a 4mm */
            text-transform: uppercase;
            letter-spacing: 2px;
            border-bottom: 3px solid #0072C6;
            display: inline-block;
            padding-bottom: 5mm;
        }

        .texto-completado {
            font-size: 14pt;
            color: #495057;
            margin: 4mm 0;  /* ← CAMBIAR de 6mm a 4mm */
            line-height: 1.6;
        }

        .nombre-capacitacion {
            font-size: 24pt;
            font-weight: bold;
            color: #0072C6;
            margin: 3mm 0;  /* ← CAMBIAR: agregar margin completo */
        }

        .detalles-curso {
            font-size: 12pt;
            color: #6c757d;
            margin-top: 2mm;  /* ← Ya está bien */
            margin-bottom: 15mm;  /* ← NUEVO: espacio antes del footer */
        }

        

        .firma-seccion {
            text-align: center;
            flex: 1;
        }

        .linea-firma {
            border-top: 2px solid #212529;
            width: 180px;
            margin: 0 auto 5px auto;
        }

        .nombre-firma {
            font-size: 11pt;
            font-weight: bold;
            color: #212529;
        }

        .cargo-firma {
            font-size: 9pt;
            color: #6c757d;
            font-style: italic;
        }

        /* Metadata */
        .metadata {
            position: absolute;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #6c757d;
        }

        .codigo-verificacion {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #0072C6;
        }

        
    </style>
</head>
<body>
    <div class="border-frame"></div>
    <div class="inner-border">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo-text">GEVOPI</div>
            </div>
            <div class="logo-section" style="text-align: right;">
                <div style="font-size: 11pt; color: #6c757d;">
                    Sistema de Seguimiento <br> Post-Incendios de Voluntarios
                </div>
            </div>
        </div>


    

        <!-- Título -->
        <div class="titulo-certificado">CERTIFICADO</div>
        <div class="subtitulo">de Finalización y Aprobación</div>

        <!-- Contenido -->
        <div class="contenido">
            <p class="texto-otorga">Se otorga el presente certificado a:</p>
            
            <div class="nombre-voluntario">
                {{ strtoupper($voluntario->nombres . ' ' . $voluntario->apellidos) }}
            </div>

            <p class="texto-completado">
                Por haber completado satisfactoriamente la capacitación:
            </p>

            <div class="nombre-capacitacion">
                {{ $capacitacion->nombre }}
            </div>

            <p class="detalles-curso">
                Duración: {{ $totalHoras }} horas | 
                Etapas completadas: {{ $totalEtapas }} | 
                Fecha: {{ \Carbon\Carbon::parse($fechaFinalizacion)->format('d/m/Y') }}
            </p>
        </div>

        

        <!-- Metadata -->
        <div class="metadata">
            <div>
                <strong>Código de Certificado:</strong> 
                <span class="codigo-verificacion">{{ $codigoCertificado }}</span>
            </div>
            <div>
                <strong>Fecha de Emisión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}
            </div>
        </div>
    </div>
</body>
</html>