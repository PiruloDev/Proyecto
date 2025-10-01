package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.dto.RecetaRequest;
import com.example.Proyecto.model.RecetaProducto;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

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
            // Según el SQL, la columna es ID_UNIDAD_MEDIDA, pero si en tu modelo usaste String,
            // ajustamos la lectura para que sea compatible o la omitimos si no existe en la tabla.
            // ASUMIMOS que la columna en la BD se llama 'UNIDAD_MEDIDA' como en el DTO/Model
            try {
                receta.setUnidadMedida(rs.getString("UNIDAD_MEDIDA"));
            } catch (SQLException e) {
                // Si la columna no existe (ej. solo usas ID_UNIDAD_MEDIDA), se ignora
            }
            return receta;
        }
    };

    /**
     * READ: Obtiene todos los ingredientes requeridos para un producto específico.
     */
    public List<RecetaProducto> obtenerRecetaPorProducto(int idProducto) {
        String sql = "SELECT * FROM recetas_productos WHERE ID_PRODUCTO = ?";
        List<RecetaProducto> receta = jdbcTemplate.query(sql, recetaRowMapper, idProducto);

        if (receta.isEmpty()) {
            // Este mensaje es el que arroja el error en tu InventarioController.
            throw new IllegalArgumentException("El producto con ID " + idProducto + " no tiene receta configurada.");
        }
        return receta;
    }

    // ================= CRUD ADICIONAL PARA RECETAS =================

    /**
     * CREATE: Registra una nueva receta. Implica insertar múltiples filas en recetas_productos.
     */
    @Transactional
    public void crearReceta(RecetaRequest request) {
        // Validación de existencia del producto
        Integer productoCount = jdbcTemplate.queryForObject("SELECT COUNT(*) FROM productos WHERE ID_PRODUCTO = ?",
                Integer.class, request.getIdProducto());
        if (productoCount == null || productoCount == 0) {
            throw new IllegalArgumentException("No se encontró producto con ID " + request.getIdProducto());
        }

        // 1. Verificar si ya existe alguna receta para este producto (opcional, pero buena práctica)
        int existingCount = jdbcTemplate.queryForObject("SELECT COUNT(*) FROM recetas_productos WHERE ID_PRODUCTO = ?",
                Integer.class, request.getIdProducto());
        if (existingCount > 0) {
            throw new IllegalArgumentException("El producto ID " + request.getIdProducto() + " ya tiene una receta registrada. Use PUT para actualizar.");
        }

        // 2. Insertar los nuevos ingredientes de la receta
        String sqlInsert = "INSERT INTO recetas_productos (ID_PRODUCTO, ID_INGREDIENTE, CANTIDAD_REQUERIDA) VALUES (?, ?, ?)";

        for (RecetaRequest.IngredienteReceta ing : request.getIngredientes()) {
            jdbcTemplate.update(sqlInsert,
                    request.getIdProducto(),
                    ing.getIdIngrediente(),
                    ing.getCantidadNecesaria());
            // Nota: Aquí se omiten UNIDAD_MEDIDA y otros campos si no se incluyen en el INSERT
        }
    }

    /**
     * READ: Obtiene todas las filas de la tabla recetas_productos.
     */
    public List<RecetaProducto> obtenerTodasLasRecetas() {
        String sql = "SELECT * FROM recetas_productos";
        return jdbcTemplate.query(sql, recetaRowMapper);
    }

    /**
     * DELETE: Elimina todas las filas de la receta de un producto.
     */
    @Transactional
    public void eliminarReceta(Long idProducto) {
        String sqlDelete = "DELETE FROM recetas_productos WHERE ID_PRODUCTO = ?";
        int rows = jdbcTemplate.update(sqlDelete, idProducto);

        if (rows == 0) {
            throw new IllegalArgumentException("No se encontró receta para el producto ID " + idProducto);
        }
    }

    /**
     * UPDATE: Reemplaza una receta existente (elimina la vieja y crea la nueva).
     */
    @Transactional
    public void actualizarReceta(Long idProducto, RecetaRequest request) {
        // 1. Validar que la request corresponde al ID del Path
        if (!idProducto.equals(request.getIdProducto())) {
            throw new IllegalArgumentException("El ID de producto en la URL (" + idProducto + ") no coincide con el ID en el cuerpo de la petición (" + request.getIdProducto() + ").");
        }

        // 2. Eliminar la receta antigua (si existe)
        String sqlDelete = "DELETE FROM recetas_productos WHERE ID_PRODUCTO = ?";
        jdbcTemplate.update(sqlDelete, idProducto);

        // 3. Insertar la nueva receta
        String sqlInsert = "INSERT INTO recetas_productos (ID_PRODUCTO, ID_INGREDIENTE, CANTIDAD_REQUERIDA) VALUES (?, ?, ?)";

        if (request.getIngredientes() == null || request.getIngredientes().isEmpty()) {
            // Se asume que el usuario quiere borrar la receta (ya se hizo en el paso 2), pero se puede lanzar una excepción si no se permite.
            return;
        }

        for (RecetaRequest.IngredienteReceta ing : request.getIngredientes()) {
            jdbcTemplate.update(sqlInsert,
                    request.getIdProducto(),
                    ing.getIdIngrediente(),
                    ing.getCantidadNecesaria());
        }
    }
}