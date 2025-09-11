<?php


$url = "http://localhost:8080/proveedores";
$consumo = file_get_contents($url);
$proveedores = json_decode($consumo);


echo "Ingresa el ID del proveedor que desea consultar (0 para todos):\n";
$idProveedor = readline("ID: ");


if ($idProveedor == 0) {
    foreach ($proveedores as $proveedor) {
        echo "Nombre: " . $proveedor->nombreProv . "\n";
    }
} else {
    foreach ($proveedores as $proveedor) {
        if ($proveedor->idProveedor == $idProveedor) {
            echo "Nombre: " . $proveedor->nombreProv . "\n";
            break; 
        }
    }
}