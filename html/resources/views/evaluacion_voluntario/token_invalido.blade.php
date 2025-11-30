<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enlace Inválido - GEVOPI</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }

        .error-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            color: white;
            font-size: 45px;
        }

        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 15px;
        }

        p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .info-list {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-list h4 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .info-list ul {
            list-style: none;
        }

        .info-list li {
            color: #666;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-list li i {
            color: #353b41;
            width: 20px;
        }

        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            text-decoration: none;
            padding: 14px 30px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
        }

        .logo {
            font-size: 18px;
            color: #667eea;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="logo">GEVOPI</div>
        
        <div class="error-icon">
            <i class="fas fa-link-slash"></i>
        </div>
        
        <h1>Enlace Inválido o Expirado</h1>
        
        <p>Lo sentimos, el enlace que estás intentando usar no es válido o ha expirado.</p>
        
        <div class="info-list">
            <h4>Esto puede ocurrir porque:</h4>
            <ul>
                <li>
                    <i class="fas fa-clock"></i>
                    El enlace ha expirado (validez de 7 días)
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    Ya completaste esta evaluación anteriormente
                </li>
                <li>
                    <i class="fas fa-unlink"></i>
                    El enlace fue copiado incorrectamente
                </li>
            </ul>
        </div>
        
        <p>Si necesitas acceder a una evaluación, contacta al administrador para que te envíe un nuevo enlace.</p>
        
        <a href="{{ url('/') }}" class="btn-home">
            <i class="fas fa-home"></i>
            Ir al Inicio
        </a>
    </div>
</body>
</html>
