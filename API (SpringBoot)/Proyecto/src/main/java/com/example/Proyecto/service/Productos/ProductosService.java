package com.example.Proyecto.service.Productos;

import com.example.Proyecto.model.PojoProductos;
import jakarta.transaction.Transactional;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import org.springframework.jdbc.core.RowMapper;

import java.math.BigDecimal;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service
public class ProductosService {
    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<Map<String, Object>> obtenerProductosPorCategoria(int idCategoria) {
        String sql = "SELECT ID_PRODUCTO, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, TIPO_PRODUCTO_MARCA " +
                "FROM Productos WHERE ID_CATEGORIA_PRODUCTO = ?";
        return jdbcTemplate.query(sql, new Object[]{idCategoria}, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> producto = new HashMap<>();
                producto.put("idProducto", rs.getInt("ID_PRODUCTO"));
                producto.put("idCategoriaProducto", rs.getInt("ID_CATEGORIA_PRODUCTO"));
                producto.put("nombreProducto", rs.getString("NOMBRE_PRODUCTO"));
                producto.put("stockMinimo", rs.getInt("PRODUCTO_STOCK_MIN"));
                producto.put("precio", rs.getBigDecimal("PRECIO_PRODUCTO"));
                producto.put("marcaProducto", rs.getString("TIPO_PRODUCTO_MARCA"));
                return producto;
            }
        });
    }

    public List<Map<String, Object>> obtenerDetallesProducto() {
        String sql = "SELECT ID_PRODUCTO, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, DESCRIPCION_PRODUCTO, " +
                "PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, TIPO_PRODUCTO_MARCA, " +
                "ACTIVO, FECHA_ULTIMA_MODIFICACION " +
                "FROM Productos";

        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> producto = new HashMap<>();
                producto.put("Id Producto:", rs.getInt("ID_PRODUCTO"));
                producto.put("Id Categoria Producto:", rs.getInt("ID_CATEGORIA_PRODUCTO"));
                producto.put("Nombre Producto:", rs.getString("NOMBRE_PRODUCTO"));
                producto.put("Descripcion Producto:", rs.getString("DESCRIPCION_PRODUCTO")); // ← AGREGADO
                producto.put("Stock Minímo:", rs.getInt("PRODUCTO_STOCK_MIN"));
                producto.put("Precio:", rs.getBigDecimal("PRECIO_PRODUCTO"));
                producto.put("Fecha Vencimiento:", rs.getDate("FECHA_VENCIMIENTO_PRODUCTO"));
                producto.put("Marca Producto:", rs.getString("TIPO_PRODUCTO_MARCA"));
                producto.put("ACTIVO", rs.getBoolean("ACTIVO")); // ← AGREGADO
                producto.put("Fecha Ultima Modificación:", rs.getTimestamp("FECHA_ULTIMA_MODIFICACION"));
                return producto;
            }
        });

    }

    public Map<String, Object> obtenerProductoPorId(int id) {
        String sql = "SELECT ID_PRODUCTO, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, PRODUCTO_STOCK_MIN, " +
                "PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, TIPO_PRODUCTO_MARCA, FECHA_ULTIMA_MODIFICACION " +
                "FROM Productos WHERE ID_PRODUCTO = ?";

        try {
            return jdbcTemplate.queryForObject(sql, new RowMapper<Map<String, Object>>() {
                @Override
                public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                    Map<String, Object> producto = new HashMap<>();
                    producto.put("Id Producto:", rs.getInt("ID_PRODUCTO"));
                    producto.put("Id Categoria Producto:", rs.getInt("ID_CATEGORIA_PRODUCTO"));
                    producto.put("Nombre Producto:", rs.getString("NOMBRE_PRODUCTO"));
                    producto.put("Stock Minímo:", rs.getString("PRODUCTO_STOCK_MIN"));
                    producto.put("Precio:", rs.getString("PRECIO_PRODUCTO"));
                    producto.put("Fecha Vencimiento:", rs.getString("FECHA_VENCIMIENTO_PRODUCTO"));
                    producto.put("Marca Producto:", rs.getString("TIPO_PRODUCTO_MARCA"));
                    producto.put("Fecha Ultima Modificación:", rs.getString("FECHA_ULTIMA_MODIFICACION"));
                    producto.put("Descripcion Producto:", rs.getString("DESCRIPCION_PRODUCTO"));
                    producto.put("ACTIVO", rs.getBoolean("ACTIVO"));
                    return producto;
                }
            }, id);
        } catch (DataAccessException e) {
            e.printStackTrace();
            return null;
        }
    }

    public boolean crearProducto(PojoProductos pojoProductos) {
        String sql = "INSERT INTO Productos (ID_ADMIN, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, DESCRIPCION_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA, ACTIVO) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            System.out.println("=== DEBUG SERVICIO ===");
            System.out.println("SQL: " + sql);
            
            java.sql.Date fechaVencimiento = null;
            if (pojoProductos.getFechaVencimiento() != null) {
                fechaVencimiento = java.sql.Date.valueOf(pojoProductos.getFechaVencimiento());
                System.out.println("Fecha vencimiento convertida: " + fechaVencimiento);
            } else {
                System.out.println("Fecha vencimiento es null");
            }
            
            java.sql.Date fechaIngreso = null;
            if (pojoProductos.getFechaIngreso() != null) {
                fechaIngreso = java.sql.Date.valueOf(pojoProductos.getFechaIngreso());
            } else {
                // Si no se proporciona, usar fecha actual
                fechaIngreso = new java.sql.Date(System.currentTimeMillis());
            }
            
            // Valores por defecto si no se proporcionan
            int idAdmin = (pojoProductos.getIdAdmin() != 0) ? pojoProductos.getIdAdmin() : 1; // ID de admin por defecto
            int idCategoria = (pojoProductos.getIdCategoriaProducto() != 0) ? pojoProductos.getIdCategoriaProducto() : 1; // Categoría por defecto
            
            // Asegurar que fechaIngreso nunca sea null
            if (fechaIngreso == null) {
                fechaIngreso = new java.sql.Date(System.currentTimeMillis());
            }
            
            System.out.println("Parámetros SQL:");
            System.out.println("1. idAdmin: " + idAdmin + " (original: " + pojoProductos.getIdAdmin() + ")");
            System.out.println("2. idCategoria: " + idCategoria + " (original: " + pojoProductos.getIdCategoriaProducto() + ")");
            System.out.println("3. nombreProducto: " + pojoProductos.getNombreProducto());
            System.out.println("4. descripcion: " + pojoProductos.getDescripcionProducto());
            System.out.println("5. stockMinimo: " + pojoProductos.getStockMinimo());
            System.out.println("6. precio: " + pojoProductos.getPrecio());
            System.out.println("7. fechaVencimiento: " + fechaVencimiento);
            System.out.println("8. fechaIngreso: " + fechaIngreso);
            System.out.println("9. marcaProducto: " + pojoProductos.getMarcaProducto());
            System.out.println("10. activo: " + pojoProductos.isActivo());
            
            int result = jdbcTemplate.update(sql,
                    idAdmin,                                    // 1
                    idCategoria,                               // 2
                    pojoProductos.getNombreProducto(),         // 3
                    pojoProductos.getDescripcionProducto(),    // 4
                    pojoProductos.getStockMinimo(),            // 5
                    pojoProductos.getPrecio(),                 // 6
                    fechaVencimiento,                          // 7
                    fechaIngreso,                              // 8
                    pojoProductos.getMarcaProducto(),          // 9
                    pojoProductos.isActivo()                   // 10
            );
            
            System.out.println("Filas afectadas: " + result);
            return result > 0;
            
        } catch (DataAccessException e) {
            System.out.println("Error de base de datos: " + e.getMessage());
            e.printStackTrace();
            return false;
        } catch (Exception e) {
            System.out.println("Error general: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
    }

    public boolean actualizarProducto(PojoProductos pojoProductos) {
        String sql = "UPDATE Productos SET " +
                "NOMBRE_PRODUCTO = ?, " +
                "ID_CATEGORIA_PRODUCTO = ?, " +
                "DESCRIPCION_PRODUCTO = ?, " +
                "PRODUCTO_STOCK_MIN = ?, " +
                "PRECIO_PRODUCTO = ?, " +
                "FECHA_VENCIMIENTO_PRODUCTO = ?, " +
                "TIPO_PRODUCTO_MARCA = ?, " +
                "ACTIVO = ? " +
                "WHERE ID_PRODUCTO = ?";
        try {
            System.out.println("=== ACTUALIZANDO PRODUCTO ===");
            System.out.println("ID: " + pojoProductos.getId());
            System.out.println("Nombre: " + pojoProductos.getNombreProducto());
            System.out.println("Categoría: " + pojoProductos.getIdCategoriaProducto());
            System.out.println("Descripción: " + pojoProductos.getDescripcionProducto());
            System.out.println("Stock: " + pojoProductos.getStockMinimo());
            System.out.println("Precio: " + pojoProductos.getPrecio());
            System.out.println("Marca: " + pojoProductos.getMarcaProducto());
            System.out.println("Activo: " + pojoProductos.isActivo());
            System.out.println("Imagen: " + pojoProductos.getImagenUrl());

            java.sql.Date fechaVencimiento = null;
            if (pojoProductos.getFechaVencimiento() != null) {
                fechaVencimiento = java.sql.Date.valueOf(pojoProductos.getFechaVencimiento());
            }

            int result = jdbcTemplate.update(sql,
                    pojoProductos.getNombreProducto(),
                    pojoProductos.getIdCategoriaProducto(),
                    pojoProductos.getDescripcionProducto(),
                    pojoProductos.getStockMinimo(),
                    pojoProductos.getPrecio(),
                    fechaVencimiento,
                    pojoProductos.getMarcaProducto(),
                    pojoProductos.isActivo(),
                    pojoProductos.getId()
            );

            System.out.println("Filas actualizadas: " + result);
            return result > 0;

        } catch (DataAccessException e) {
            System.out.println("ERROR SQL: " + e.getMessage());
            e.printStackTrace();
            return false;
        }
    }

    public boolean eliminarProducto(int id) {
        String sql = "DELETE FROM Productos WHERE ID_PRODUCTO = ?";
        try {
            int result = jdbcTemplate.update(sql, id);
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }

    @Transactional
    public int actualizarStockProducto(Long idProducto, BigDecimal cantidadAjuste) {
        String sqlUpdate = "UPDATE PRODUCTOS SET STOCK_ACTUAL = STOCK_ACTUAL + ? WHERE ID_PRODUCTO = ?";

        int rows = jdbcTemplate.update(sqlUpdate, cantidadAjuste, idProducto);

        if (rows == 0) {
            throw new IllegalArgumentException("Producto con ID " + idProducto + " no encontrado para actualizar stock.");
        }
        return rows;
    }

}
