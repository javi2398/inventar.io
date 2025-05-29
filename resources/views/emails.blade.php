<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensaje de Contacto | Inventar.io</title>
    <style>
        body {
            background-color: #0f172a;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #e2e8f0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #1e293b;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }
        .header {
            background-color: #0f172a;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #334155;
        }
        .header img {
            max-width: 140px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            color: #38bdf8;
        }
        .header p {
            margin: 4px 0 0;
            color: #94a3b8;
            font-size: 14px;
        }
        .body {
            padding: 30px;
            font-size: 15px;
            line-height: 1.6;
            color: #e2e8f0;
        }
        blockquote {
            margin: 20px 0;
            padding-left: 15px;
            border-left: 4px solid #38bdf8;
            color: #f1f5f9;
            background-color: #334155;
            padding: 15px;
            border-radius: 6px;
            font-style: italic;
        }
        .footer {
            background-color: #0f172a;
            padding: 20px 30px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
            border-top: 1px solid #334155;
        }
        .footer a {
            color: #38bdf8;
            text-decoration: none;
        }

    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Inventar.io</h1>
            <p>Nuevo mensaje de contacto</p>
        </div>
        <div class="body">
            <p>Hola equipo de Inventar.io,</p>
            <p>Se ha recibido un nuevo mensaje desde la aplicación:</p>

            <blockquote>
                {!! nl2br(e($messageBody)) !!}
            </blockquote>

            <p>Por favor, revisen este mensaje y contacten al remitente si es necesario.</p>

            <p>Saludos,</p>
            <p><strong>Inventar.io</strong><br>Gestión inteligente de inventarios.</p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Inventar.io. Todos los derechos reservados. <br>
            <a href({{ route('inventario.index', ['id'=>1]) }})>www.inventar.io</a>
        </div>
    </div>
</body>
</html>
