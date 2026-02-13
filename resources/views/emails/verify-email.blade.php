<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Verifica tu correo</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>
    <p>Gracias por registrarte. Haz clic en el siguiente enlace para verificar tu correo electrónico:</p>
    <p><a href="{{ $verificationUrl }}">Verificar correo</a></p>
    <p>Este enlace expira en 60 minutos. Si no creaste esta cuenta, ignora este mensaje.</p>
</body>
</html>
