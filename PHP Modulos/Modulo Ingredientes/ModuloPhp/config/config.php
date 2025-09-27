<?php
// config.php
// Archivo de configuración global para los servicios PHP

return [
    // URL base del backend Spring Boot
    "BASE_URL" => "http://localhost:8080",

    // Endpoints organizados por controlador
    "ENDPOINTS" => [

        
        // CATEGORÍAS DE INGREDIENTES
        
        "CATEGORIAS" => [
            "LISTAR"   => "/categorias/ingredientes",        
            "CREAR"    => "/nuevacategoriaingrediente",      
            "EDITAR"   => "/categoriaingrediente",           
            "ELIMINAR" => "/eliminarcategoria"               
        ],

        // ---------------------------
        // INGREDIENTES
        // ---------------------------
        "INGREDIENTES" => [
            "LISTAR"   => "/ingredientes",                   // GET
            "CREAR"    => "/nuevoingrediente",               // POST
            "EDITAR"   => "/ingrediente",                    // PUT (/{id})
            "ELIMINAR" => "/eliminaringrediente"             // DELETE (/{id})
        ],

        // ---------------------------
        // PROVEEDORES
        // ---------------------------
        "PROVEEDORES" => [
            "LISTAR"   => "/proveedores",                    // GET
            "CREAR"    => "/nuevoproveedor",                 // POST
            "EDITAR"   => "/proveedor",                      // PUT (/{id})
            "ELIMINAR" => "/eliminarproveedor"               // DELETE (/{id})
        ],

        // ---------------------------
        // DETALLE PEDIDOS
        // ---------------------------
        "DETALLE_PEDIDOS" => [
            "LISTAR"   => "/detallepedidos",                 // GET
            "CREAR"    => "/nuevodetallepedido",             // POST
            "EDITAR"   => "/detallepedido",                  // PUT (/{id})
            "ELIMINAR" => "/eliminardetallepedido"           // DELETE (/{id})
        ]
    ]
];
