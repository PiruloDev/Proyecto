package com.example.Proyecto.service.Productos;

import com.example.Proyecto.model.PojoProductos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service
public class ProductosService {
    
    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<Map<String, Object>> obtenerDetallesProducto() {
        String sql = "SELECT ID_PRODUCTO, NOMBRE_PRODUCTO, PRECIO_PRODUCTO FROM Productos";
        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> producto = new HashMap<>();
                producto.put("Id:", rs.getInt("ID_PRODUCTO"));
                producto.put("Nombre:", rs.getString("NOMBRE_PRODUCTO"));
                producto.put("Precio:", rs.getDouble("PRECIO_PRODUCTO"));
                return producto;
            }
        });
    }

    public boolean crearProducto(PojoProductos pojoProductos) {
        String sql = "INSERT INTO Productos (NOMBRE_PRODUCTO, PRECIO_PRODUCTO) VALUES (?, ?)";
        try {
            int result = jdbcTemplate.update(sql, pojoProductos.getNombreProducto(), pojoProductos.getPrecio());
            return result > 0;
        } catch (DataAccessException e) {
            return false;
        }
    }

    public boolean actualizarProducto(PojoProductos pojoProductos) {
        String sql = "UPDATE Productos SET NOMBRE_PRODUCTO = ?, PRECIO_PRODUCTO = ? WHERE ID_PRODUCTO = ?";
        try {
            int value = jdbcTemplate.update(sql, pojoProductos.getNombreProducto(), pojoProductos.getPrecio(), pojoProductos.getId());
            return value > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
}
