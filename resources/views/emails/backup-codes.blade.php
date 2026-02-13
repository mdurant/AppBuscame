<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Códigos de respaldo 2FA</title>
</head>
<body>
    <p>Hola {{ $user->name }},</p>
    <p>Estos son tus códigos de respaldo para la autenticación en dos pasos. Guárdalos en un lugar seguro. Cada código solo puede usarse una vez.</p>
    <ul>
        @foreach($backupCodes as $code)
            <li><code>{{ $code }}</code></li>
        @endforeach
    </ul>
    <p>Si no activaste la verificación en dos pasos, contacta a soporte.</p>
</body>
</html>
