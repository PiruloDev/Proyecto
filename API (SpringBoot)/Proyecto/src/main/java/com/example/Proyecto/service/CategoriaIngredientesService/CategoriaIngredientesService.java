package com.example.Proyecto.service.CategoriaIngredientesService;

import com.example.Proyecto.service.CategoriaIngredientesService.CategoriaIngredientes;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class CategoriaIngredientesService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Obtener todas las categorías de ingredientes
    public List<CategoriaIngredientes> obtenerTodasLasCategoriasIngredientes() {
        String sql = "SELECT * FROM Categoria_Ingredientes";
        return jdbcTemplate.query(sql, new RowMapper<CategoriaIngredientes>() {
            @Override
            public CategoriaIngredientes mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                CategoriaIngredientes categoria = new CategoriaIngredientes();
                categoria.setIdCategoriaIngrediente(rs.getInt("ID_CATEGORIA"));
                categoria.setNombreCategoria(rs.getString("NOMBRE_CATEGORIA_INGREDIENTE"));
                return categoria;
            }
        });
    }

    // Crear una nueva categoría (POST)
    public void crearCategoriaIngrediente(CategoriaIngredientes categoria) {
        String sql = "INSERT INTO Categoria_Ingredientes (NOMBRE_CATEGORIA_INGREDIENTE) VALUES (?)";
        jdbcTemplate.update(sql, categoria.getNombreCategoria());
    }

    // Actualizar una categoría existente (PUT)
    public int editarCategoriaIngrediente(CategoriaIngredientes categoria) {
        String sql = "UPDATE Categoria_Ingredientes SET NOMBRE_CATEGORIA_INGREDIENTE=? WHERE ID_CATEGORIA=?";
        return jdbcTemplate.update(sql, categoria.getNombreCategoria(), categoria.getIdCategoriaIngrediente());
    }

    // Eliminar una categoría por ID (DELETE)
    public int eliminarCategoriaIngrediente(int idCategoria) {
        String sql = "DELETE FROM Categoria_Ingredientes WHERE ID_CATEGORIA = ?";
        return jdbcTemplate.update(sql, idCategoria);
    }
}