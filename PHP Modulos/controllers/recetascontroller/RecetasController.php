<?php
// controllers/recetascontroller/RecetasController.php

class RecetasController {

    private $baseUrl = "http://localhost:8080/inventario/recetas";

    // =========================================================
    // 1. LISTAR TODAS LAS RECETAS
    // =========================================================
    public function listar() {
        $data = @file_get_contents($this->baseUrl);
        $recetas = $data ? json_decode($data, true) : [];
        include __DIR__ . "/../../views/recetasViews/index.php";
    }

    // =========================================================
    // 2. MOSTRAR DETALLE DE UNA RECETA
    // =========================================================
    public function detalle($idProducto) {
        $url = $this->baseUrl . "/$idProducto";
        $data = @file_get_contents($url);
        $receta = $data ? json_decode($data, true) : null;
        include __DIR__ . "/../../views/recetasViews/detalle.php";
    }

    // =========================================================
    // 3. MOSTRAR FORMULARIO DE CREACIÓN
    // =========================================================
    public function crear() {
        include __DIR__ . "/../../views/recetasViews/crear.php";
    }

    // =========================================================
    // 4. GUARDAR NUEVA RECETA (POST → backend)
    // =========================================================
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $receta = [
                "idProducto" => (int) $_POST['idProducto'],
                "ingredientes" => []
            ];

            if (!empty($_POST['ingredientes'])) {
                foreach ($_POST['ingredientes'] as $ing) {
                    $receta['ingredientes'][] = [
                        "idIngrediente" => (int) $ing['idIngrediente'],
                        "cantidadNecesaria" => $ing['cantidadNecesaria'],
                        "unidadMedida" => $ing['unidadMedida']
                    ];
                }
            }

            $jsonData = json_encode($receta);

            $ch = curl_init($this->baseUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 201) {
                header("Location: ?accion=listar&msg=Receta creada con éxito");
                exit;
            } else {
                echo "❌ Error al crear receta (HTTP $httpCode): $result";
            }
        }
    }

    // =========================================================
    // 5. MOSTRAR FORMULARIO DE EDICIÓN
    // =========================================================
    public function editar($idProducto) {
        $url = $this->baseUrl . "/$idProducto";
        $data = @file_get_contents($url);
        $receta = $data ? json_decode($data, true) : null;
        include __DIR__ . "/../../views/recetasViews/editar.php";
    }

    // =========================================================
    // 6. ACTUALIZAR UNA RECETA (PUT → backend)
    // =========================================================
    public function actualizar($idProducto) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $receta = [
                "idProducto" => (int) $_POST['idProducto'],
                "ingredientes" => []
            ];

            if (!empty($_POST['ingredientes'])) {
                foreach ($_POST['ingredientes'] as $ing) {
                    $receta['ingredientes'][] = [
                        "idIngrediente" => (int) $ing['idIngrediente'],
                        "cantidadNecesaria" => $ing['cantidadNecesaria'],
                        "unidadMedida" => $ing['unidadMedida']
                    ];
                }
            }

            $jsonData = json_encode($receta);

            $url = $this->baseUrl . "/$idProducto";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ]);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                header("Location: ?accion=detalle&id=$idProducto&msg=Receta actualizada");
                exit;
            } else {
                echo "❌ Error al actualizar receta (HTTP $httpCode): $result";
            }
        }
    }

    // =========================================================
    // 7. ELIMINAR UNA RECETA (DELETE → backend)
    // =========================================================
    public function eliminar($idProducto) {
        $url = $this->baseUrl . "/$idProducto";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 204) {
            header("Location: ?accion=listar&msg=Receta eliminada");
            exit;
        } else {
            echo "❌ Error al eliminar receta (HTTP $httpCode): $result";
        }
    }
}
