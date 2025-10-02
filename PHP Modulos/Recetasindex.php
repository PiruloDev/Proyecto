<?php
// C:\xampp\htdocs\REbase\PHP Modulos\Recetasindex.php

// Definimos la acción (aunque la vista de recetas la maneja internamente,
// es útil si quieres pasarle parámetros GET adicionales)
$accion = $_GET['accion'] ?? 'listar'; 

// 1. Definimos la ruta de la vista
// Según tu estructura de carpetas, la vista está en:
// C:\xampp\htdocs\REbase\PHP Modulos\views\recetasViews\recetasViews.php
$ruta_vista = __DIR__ . '/views/recetasViews/recetasViews.php';

// 2. Comprobamos si el archivo existe antes de incluirlo
if (file_exists($ruta_vista)) {
    // Incluimos directamente el archivo de la vista.
    // Este archivo contiene la lógica del controlador (POST-Redirect-GET)
    // y el renderizado del HTML.
    require_once $ruta_vista;
} else {
    // Si la vista no se encuentra
    // Aquí puedes incluir una página 404 o un mensaje de error.
    echo "Error: La vista de Recetas no se encontró en la ruta esperada.";
}