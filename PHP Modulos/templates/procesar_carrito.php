<?php
// /procesar_carrito.php

// Iniciar la sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar que es un POST y que la acción es 'agregar'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'agregar') {
    
    $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1; // Asume 1 si no está seteada

    if ($producto_id > 0 && $cantidad > 0) {
        
        // Inicializar el carrito en la sesión si no existe
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }
        
        // Agregar o actualizar el producto en la sesión
        // Usamos el ID del producto como clave del array para fácil acceso y manejo de duplicados
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$producto_id] = [
                'id' => $producto_id,
                'cantidad' => $cantidad
            ];
        }
        
        // Redirección directa a la página del carrito
        header('Location: pedidos.php');
        exit;
    }
}

// Si la solicitud no es válida, redirigir al menú.
header('Location: menu.php');
exit;
?>