<?php
// config/configInventario.php

return [
    'inventario' => [
        'base_url' => 'http://localhost:8080/inventario',

        'produccion' => [
            'registrar' => '/produccion',         // POST
            'receta'    => '/produccion/receta'   // GET /{idProducto}
        ],
    ]
];
