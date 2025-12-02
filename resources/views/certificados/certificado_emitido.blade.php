<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Felicitaciones! Tu Certificado GEVOPI</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0072C6 0%, #00A4EF 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 32px; font-weight: bold;">
                                🎓 ¡Felicitaciones!
                            </h1>
                            <p style="margin: 10px 0 0 0; color: #ffffff; font-size: 16px;">
                                Has completado tu capacitación en GEVOPI
                            </p>
                        </td>
                    </tr>

                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <p style="margin: 0 0 20px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Hola <strong>{{ $nombreVoluntario }}</strong>,
                            </p>

                            <p style="margin: 0 0 20px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Nos complace informarte que has <strong>completado exitosamente</strong> la capacitación:
                            </p>

                            <div style="background-color: #f8f9fa; border-left: 4px solid #0072C6; padding: 20px; margin: 20px 0; border-radius: 5px;">
                                <h2 style="margin: 0 0 10px 0; color: #0072C6; font-size: 20px;">
                                    {{ $nombreCapacitacion }}
                                </h2>
                                <p style="margin: 0; color: #666666; font-size: 14px;">
                                    <strong>Código de Certificado:</strong> {{ $codigo }}
                                </p>
                            </div>

                            <p style="margin: 20px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Tu certificado oficial está adjunto a este email en formato PDF. Puedes descargarlo, imprimirlo o compartirlo según lo necesites.
                            </p>

                            <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0;">
                                <p style="margin: 0; color: #856404; font-size: 14px;">
                                    <strong>💡 Tip:</strong> Guarda este certificado en un lugar seguro. Puedes acceder a él en cualquier momento desde la aplicación móvil en la sección "Mis Certificados".
                                </p>
                            </div>

                            <p style="margin: 20px 0; color: #333333; font-size: 16px; line-height: 1.6;">
                                Gracias por tu dedicación y compromiso con GEVOPI. Tu esfuerzo contribuye a hacer un mundo mejor.
                            </p>

                            <div style="text-align: center; margin: 30px 0;">
                                <p style="margin: 0; color: #999999; font-size: 14px; font-style: italic;">
                                    "El voluntariado es la expresión última de lo que significa ser humano"
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <p style="margin: 0 0 10px 0; color: #666666; font-size: 14px;">
                                <strong>GEVOPI</strong><br>
                                Sistema de Gestión de Voluntarios de Protección Integral
                            </p>
                            <p style="margin: 10px 0 0 0; color: #999999; font-size: 12px;">
                                Este es un correo automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>