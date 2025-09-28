package com.example.demoJava1.Productos.Services.Controllers;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import org.springframework.jdbc.core.RowMapper;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service
public class ProductosServices {
    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<Map<String, Object>> obtenerDetallesProducto() {
        String sql = "SELECT ID_PRODUCTO, NOMBRE_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, TIPO_PRODUCTO_MARCA, FECHA_ULTIMA_MODIFICACION FROM Productos";
        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> producto = new HashMap<>();
                producto.put("Id Producto:", rs.getInt("ID_PRODUCTO"));
                producto.put("Nombre Producto:", rs.getString("NOMBRE_PRODUCTO"));
                producto.put("Stock Minímo:", rs.getString("PRODUCTO_STOCK_MIN"));
                producto.put("Precio:", rs.getString("PRECIO_PRODUCTO"));
                producto.put("Fecha Vencimiento:", rs.getString("FECHA_VENCIMIENTO_PRODUCTO"));
                producto.put("Marca Producto:", rs.getString("TIPO_PRODUCTO_MARCA"));
                producto.put("Fecha Ultima Modificación:", rs.getString("FECHA_ULTIMA_MODIFICACION"));
                return producto;
            }
        });
    }

    public boolean crearProducto(PojoProductos pojoProductos) {
        String sql = "INSERT INTO Productos (NOMBRE_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, TIPO_PRODUCTO_MARCA) VALUES (?, ?, ?, ?, ?)";
        try {
            int result = jdbcTemplate.update(sql, pojoProductos.getNombreProducto(), pojoProductos.getStockMinimo(), pojoProductos.getPrecio(), java.sql.Date.valueOf(pojoProductos.getFechaVencimiento()), pojoProductos.getMarcaProducto()
            );
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }

    public boolean actualizarProducto(PojoProductos pojoProductos) {
        String sql = "UPDATE productos SET NOMBRE_PRODUCTO = ?, PRODUCTO_STOCK_MIN = ?, PRECIO_PRODUCTO = ?, FECHA_VENCIMIENTO_PRODUCTO = ?, TIPO_PRODUCTO_MARCA = ? WHERE ID_PRODUCTO = ?";
        try {
            int result = jdbcTemplate.update(sql, pojoProductos.getNombreProducto(), pojoProductos.getStockMinimo(), pojoProductos.getPrecio(), java.sql.Date.valueOf(pojoProductos.getFechaVencimiento()), pojoProductos.getMarcaProducto(), pojoProductos.getId());
            return result > 0;
        } catch (DataAccessException e) {
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
}