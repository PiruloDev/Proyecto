package com.example.demo.Java1;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;

import java.util.List;
import java.util.Map;


@org.springframework.stereotype.Service
public class Service {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<Map<String, Object>> obtenerProductos() {
        String sql = "SELECT * FROM Productos";
        return jdbcTemplate.queryForList(sql);
    }

    // ===== GET PRODUCTO POR ID =====
    public Map<String, Object> obtenerProductoPorId(int id) {
        String sql = "SELECT * FROM Productos WHERE ID_PRODUCTO = ?";
        return jdbcTemplate.queryForMap(sql, id);
    }

    // ===== POST CREAR PRODUCTO =====
    public int crearProducto(Map<String, Object> producto) {
        String sql = "INSERT INTO Productos (ID_ADMIN, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, DESCRIPCION_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return jdbcTemplate.update(sql,
                producto.get("ID_ADMIN"),
                producto.get("ID_CATEGORIA_PRODUCTO"),
                producto.get("NOMBRE_PRODUCTO"),
                producto.get("DESCRIPCION_PRODUCTO"),
                producto.get("PRODUCTO_STOCK_MIN"),
                producto.get("PRECIO_PRODUCTO"),
                producto.get("FECHA_VENCIMIENTO_PRODUCTO"),
                producto.get("FECHA_INGRESO_PRODUCTO"),
                producto.get("TIPO_PRODUCTO_MARCA"));
    }

    // ===== PUT ACTUALIZAR PRODUCTO COMPLETO =====
    public int actualizarProducto(int id, Map<String, Object> producto) {
        String sql = "UPDATE Productos SET ID_ADMIN=?, ID_CATEGORIA_PRODUCTO=?, NOMBRE_PRODUCTO=?, DESCRIPCION_PRODUCTO=?, PRODUCTO_STOCK_MIN=?, PRECIO_PRODUCTO=?, FECHA_VENCIMIENTO_PRODUCTO=?, FECHA_INGRESO_PRODUCTO=?, TIPO_PRODUCTO_MARCA=?, ACTIVO=? WHERE ID_PRODUCTO=?";
        return jdbcTemplate.update(sql,
                producto.get("ID_ADMIN"),
                producto.get("ID_CATEGORIA_PRODUCTO"),
                producto.get("NOMBRE_PRODUCTO"),
                producto.get("DESCRIPCION_PRODUCTO"),
                producto.get("PRODUCTO_STOCK_MIN"),
                producto.get("PRECIO_PRODUCTO"),
                producto.get("FECHA_VENCIMIENTO_PRODUCTO"),
                producto.get("FECHA_INGRESO_PRODUCTO"),
                producto.get("TIPO_PRODUCTO_MARCA"),
                producto.get("ACTIVO"),
                id);
    }

    // ===== PATCH ACTUALIZAR SOLO NOMBRE Y PRECIO =====
    public int patchProducto(int id, String nombre, Double precio) {
        String sql = "UPDATE Productos SET NOMBRE_PRODUCTO=?, PRECIO_PRODUCTO=? WHERE ID_PRODUCTO=?";
        return jdbcTemplate.update(sql, nombre, precio, id);
    }

    // ===== DELETE ELIMINAR PRODUCTO =====
    public int eliminarProducto(int id) {
        String sql = "DELETE FROM Productos WHERE ID_PRODUCTO=?";
        return jdbcTemplate.update(sql, id);
    }
}


