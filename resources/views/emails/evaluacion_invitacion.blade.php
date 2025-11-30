<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación para Evaluación - GEVOPI</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f5f5f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #343a40 0%, #212529 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 600;">
                                GEVOPI
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">
                                Sistema de Gestión de Voluntarios
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #333333; margin: 0 0 20px 0; font-size: 24px;">
                                ¡Hola, {{ $voluntario->nombres }}!
                            </h2>
                            
                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Has sido invitado/a a realizar una <strong>evaluación de voluntarios</strong> en el sistema GEVOPI.
                            </p>

                            <p style="color: #666666; font-size: 16px; line-height: 1.6; margin: 0 0 30px 0;">
                                Por favor, haz clic en el siguiente botón para acceder al formulario de evaluación. Esta evaluación nos ayudará a conocer mejor tu estado físico y emocional.
                            </p>

                            <!-- Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ $enlaceEvaluacion }}" 
                                           style="display: inline-block; 
                                                  background: linear-gradient(135deg, #343a40 0%, #212529 100%); 
                                                  color: #ffffff; 
                                                  text-decoration: none; 
                                                  padding: 16px 40px; 
                                                  border-radius: 8px; 
                                                  font-size: 18px; 
                                                  font-weight: 600;
                                                  box-shadow: 0 4px 15px rgba(52, 58, 64, 0.4);">
                                            Realizar Evaluación
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #888888; font-size: 14px; line-height: 1.6; margin: 30px 0 0 0; text-align: center;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="color: #343a40; font-size: 14px; word-break: break-all; text-align: center; margin: 10px 0 0 0;">
                                {{ $enlaceEvaluacion }}
                            </p>
                        </td>
                    </tr>

                    <!-- Info Box -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8f9fa; border-radius: 8px; border-left: 4px solid #343a40;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="color: #555555; font-size: 14px; margin: 0; line-height: 1.6;">
                                            <strong>Información importante:</strong><br>
                                            • Este enlace es personal y único para ti.<br>
                                            • La evaluación es confidencial.<br>
                                            • Puedes completarla en cualquier momento.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #eeeeee;">
                            <p style="color: #888888; font-size: 13px; margin: 0;">
                                Este correo fue enviado automáticamente por el sistema GEVOPI.
                            </p>
                            <p style="color: #aaaaaa; font-size: 12px; margin: 10px 0 0 0;">
                                © {{ date('Y') }} GEVOPI - Sistema de Gestión de Voluntarios
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
