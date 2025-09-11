<?php
$url = "http://localhost:8080/ingredientes/lista";
$consumo = file_get_contents($url);
$ingredientes = json_decode($consumo);

echo "Ingresa el ID del ingrediente (0 para todos):\n";
$idIngrediente = readline("ID: ");


if ($idIngrediente == 0) {

    if ($ingredientes) {
        foreach ($ingredientes as $ingrediente) {
            echo "Nombre: " . $ingrediente->nombreIngrediente . "\n";
        }
    }
} else {

    if ($ingredientes) {
        foreach ($ingredientes as $ingrediente) {
            if ($ingrediente->idIngrediente == $idIngrediente) {
                echo "Nombre: " . $ingrediente->nombreIngrediente . "\n";
                break;
            }
        }
    }
}