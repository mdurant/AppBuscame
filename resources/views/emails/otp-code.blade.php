<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Código de verificación</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>
    <p>Tu código de verificación de 6 dígitos es: <strong>{{ $code }}</strong></p>
    <p>Expira en 15 minutos. Si no solicitaste este código, ignora este mensaje.</p>
</body>
</html>
