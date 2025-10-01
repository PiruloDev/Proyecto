<?php
class CategoriaProductosService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function listarCategorias() {
        $query = "SELECT ID_CATEGORIA_PRODUCTO, NOMBRE_CATEGORIAPRODUCTO FROM Categoria_Productos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function crearCategoria($nombre) {
        $query = "INSERT INTO Categoria_Productos (NOMBRE_CATEGORIAPRODUCTO) VALUES (:nombre)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        return $stmt->execute();
    }

    public function actualizarCategoria($id, $nombre) {
        $query = "UPDATE Categoria_Productos SET NOMBRE_CATEGORIAPRODUCTO = :nombre WHERE ID_CATEGORIA_PRODUCTO = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    public function eliminarCategoria($id) {
        $query = "DELETE FROM Categoria_Productos WHERE ID_CATEGORIA_PRODUCTO = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
