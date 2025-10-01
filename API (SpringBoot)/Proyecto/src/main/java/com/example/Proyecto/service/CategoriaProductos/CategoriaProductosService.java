package com.example.Proyecto.service.CategoriaProductos;

import com.example.Proyecto.model.PojoCategoria_Productos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class CategoriaProductosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    //Todas las categorías
    public List<PojoCategoria_Productos> obtenerCategorias() {
        String sql = "SELECT ID_CATEGORIA_PRODUCTO, NOMBRE_CATEGORIAPRODUCTO FROM Categoria_Productos";
        return jdbcTemplate.query(sql, new RowMapper<PojoCategoria_Productos>() {
            @Override
            public PojoCategoria_Productos mapRow(ResultSet rs, int rowNum) throws SQLException {
                return new PojoCategoria_Productos(
                        rs.getInt("ID_CATEGORIA_PRODUCTO"),
                        rs.getString("NOMBRE_CATEGORIAPRODUCTO")
                );
            }
        });
    }

    // Crear categoría
    public int agregarCategoria(PojoCategoria_Productos categoria) {
        String sql = "INSERT INTO Categoria_Productos (NOMBRE_CATEGORIAPRODUCTO) VALUES (?)";
        return jdbcTemplate.update(sql, categoria.getNombreCategoriaProducto());
    }

    // Actualizar categoría
    public int actualizarCategoria(PojoCategoria_Productos categoria) {
        String sql = "UPDATE Categoria_Productos SET NOMBRE_CATEGORIAPRODUCTO = ? WHERE ID_CATEGORIA_PRODUCTO = ?";
        return jdbcTemplate.update(sql, categoria.getNombreCategoriaProducto(), categoria.getIdCategoriaProducto());
    }

    // Eliminar categoría
    public int eliminarCategoria(int id) {
        String sql = "DELETE FROM Categoria_Productos WHERE ID_CATEGORIA_PRODUCTO = ?";
        return jdbcTemplate.update(sql, id);
    }
}
