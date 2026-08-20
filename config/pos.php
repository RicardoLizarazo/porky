<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PIN de autorización para quitar productos ya enviados a cocina
    |--------------------------------------------------------------------------
    | Independiente de cualquier contraseña de usuario. Cámbialo editando
    | PRODUCT_REMOVAL_PIN en el archivo .env — no requiere tocar código
    | ni la base de datos.
    */

    'removal_pin' => env('PRODUCT_REMOVAL_PIN', '0000'),

];
