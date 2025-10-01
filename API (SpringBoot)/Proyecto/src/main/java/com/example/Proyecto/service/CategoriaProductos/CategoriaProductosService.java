package com.example.Proyecto.service.CategoriaProductos;

import com.example.Proyecto.model.PojoCategoria_Productos;
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
public class CategoriaProductosService {
    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Obtener todas las categorías
    public List<Map<String, Object>> obtenerCategorias() {
        String sql = "SELECT ID_CATEGORIA, NOMBRE_CATEGORIA, DESCRIPCION_CATEGORIA FROM Categorias";
        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> categoria = new HashMap<>();
                categoria.put("Id Categoria:", rs.getInt("ID_CATEGORIA"));
                categoria.put("Nombre Categoria:", rs.getString("NOMBRE_CATEGORIA"));
                categoria.put("Descripcion:", rs.getString("DESCRIPCION_CATEGORIA"));
                return categoria;
            }
        });
    }

    // Crear nueva categoría
    public boolean crearCategoria(PojoCategoria_Productos categoria) {
        String sql = "INSERT INTO Categorias (NOMBRE_CATEGORIA, DESCRIPCION_CATEGORIA) VALUES (?, ?)";
        try {
            int result = jdbcTemplate.update(sql,
                    categoria.getNombreCategoria(),
                    categoria.getDescripcionCategoria()
            );
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }

    // Actualizar categoría
    public boolean actualizarCategoria(PojoCategoria_Productos categoria) {
        String sql = "UPDATE Categorias SET NOMBRE_CATEGORIA = ?, DESCRIPCION_CATEGORIA = ? WHERE ID_CATEGORIA = ?";
        try {
            int result = jdbcTemplate.update(sql,
                    categoria.getNombreCategoria(),
                    categoria.getDescripcionCategoria(),
                    categoria.getIdCategoria()
            );
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }

    // Eliminar categoría
    public boolean eliminarCategoria(int id) {
        String sql = "DELETE FROM Categorias WHERE ID_CATEGORIA = ?";
        try {
            int result = jdbcTemplate.update(sql, id);
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
}
