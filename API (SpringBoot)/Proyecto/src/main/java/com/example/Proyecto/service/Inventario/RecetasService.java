package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.model.RecetaProducto;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import org.springframework.jdbc.core.RowMapper;

@Service
public class RecetasService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // RowMapper para mapear la tabla recetas_productos al modelo RecetaProducto
    private RowMapper<RecetaProducto> recetaRowMapper = new RowMapper<RecetaProducto>() {
        @Override
        public RecetaProducto mapRow(ResultSet rs, int rowNum) throws SQLException {
            RecetaProducto receta = new RecetaProducto();
            receta.setIdReceta(rs.getInt("ID_RECETA"));
            receta.setIdProducto(rs.getInt("ID_PRODUCTO"));
            receta.setIdIngrediente(rs.getInt("ID_INGREDIENTE"));
            receta.setCantidadRequerida(rs.getBigDecimal("CANTIDAD_REQUERIDA"));
            receta.setUnidadMedida(rs.getString("UNIDAD_MEDIDA"));
            return receta;
        }
    };

    /**
     * Obtiene todos los ingredientes requeridos para un producto específico,
     * permitiendo al frontend cargar el "desplegable".
     */
    public List<RecetaProducto> obtenerRecetaPorProducto(int idProducto) {
        String sql = "SELECT * FROM recetas_productos WHERE ID_PRODUCTO = ?";
        return jdbcTemplate.query(sql, recetaRowMapper, idProducto);
    }
}