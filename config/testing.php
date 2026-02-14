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
            'email' => 'admin@integraltech.cl',
            'name' => 'Admin',
            'description' => 'Administrador (panel admin, usuarios y publicaciones)',
        ],
        [
            'email' => 'cliente@integraltech.cl',
            'name' => 'Cliente',
            'description' => 'Cliente de la plataforma',
        ],
        [
            'email' => 'usuario@integraltech.cl',
            'name' => 'Usuario',
            'description' => 'Usuario publicante',
        ],
    ],

];
