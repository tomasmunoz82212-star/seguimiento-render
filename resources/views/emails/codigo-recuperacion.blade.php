<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperación de contraseña</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-top: 4px solid #2D7D32;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo h2 {
            color: #2D7D32;
            margin: 0;
        }
        .codigo {
            background: #F0F1F4;
            padding: 20px;
            text-align: center;
            font-size: 32px;
            letter-spacing: 8px;
            font-weight: 800;
            font-family: monospace;
            border-radius: 10px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #9DA3B4;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #E2E4EA;
        }
        .alert-warning {
            background: #FFF3E0;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #E65100;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h2>📚 Sistema de Seguimiento CRU</h2>
            <p>Polotécnico Colombiano Jaime Isaza Cadavid - Sede Urabá</p>
        </div>

        <p>Hola <strong>{{ $nombre }}</strong>,</p>

        <p>Hemos recibido una solicitud para restablecer tu contraseña. Utiliza el siguiente código de verificación:</p>

        <div class="codigo">
            {{ $codigo }}
        </div>

        <div class="alert-warning">
            ⏰ Este código expirará en <strong>15 minutos</strong>.
        </div>

        <p>Si no solicitaste este cambio, puedes ignorar este mensaje.</p>

        <div class="footer">
            <p>© {{ date('Y') }} Politécnico Colombiano Jaime Isaza Cadavid - Sede Urabá</p>
            <p>Este es un correo automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>