<?php
session_start();

require_once __DIR__ . '/controllers/inventariocontroller/ProduccionController.php';

$controller = new ProduccionController();
$controller->manejarPeticion();
