<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Credenciales de usuarios de prueba (testing técnico)
    |--------------------------------------------------------------------------
    | Solo se muestran en la pantalla de login cuando APP_ENV=local.
    | Contraseña común para todos: cumple reglas (mayúscula, minúscula, número, símbolo).
    |
    */

    'password' => env('TEST_USERS_PASSWORD', 'Password1!'),

    'users' => [
        [
            'email' => 'maria@ejemplo.cl',
            'name' => 'María González',
            'description' => 'Propietaria (publicar propiedades, mensajes)',
        ],
        [
            'email' => 'carlos@ejemplo.cl',
            'name' => 'Carlos Rojas',
            'description' => 'Arrendatario / segundo usuario verificado',
        ],
        [
            'email' => 'test@ejemplo.cl',
            'name' => 'Usuario Test',
            'description' => 'Usuario genérico para pruebas',
        ],
    ],

];
