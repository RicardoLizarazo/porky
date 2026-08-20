<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Combos de estaciones
    |--------------------------------------------------------------------------
    |
    | Cada combo agrupa 2 o más kitchen_stations reales en una sola
    | pantalla (board/despacho), para los casos donde físicamente solo
    | hay una TV/tablet cubriendo varias estaciones. No reemplaza las
    | rutas individuales de cada estación (/kitchen-board/1 sigue
    | funcionando), solo agrega una vista combinada aparte.
    |
    | 'ids' en el mismo orden en que quieras que aparezca el nombre.
    |
    */

    'combos' => [
        [
            'ids'   => [1, 2],
            'label' => 'Parrilla y Sopas',
        ],
    ],

];
