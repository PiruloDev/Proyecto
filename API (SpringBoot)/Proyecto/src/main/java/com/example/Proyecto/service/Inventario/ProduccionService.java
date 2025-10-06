package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.dto.ProduccionRequest;
import com.example.Proyecto.model.Produccion;
import com.example.Proyecto.model.RecetaProducto;
import com.example.Proyecto.service.Ingredientes.IngredientesService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper; // Necesario para mapear resultados
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.time.LocalDateTime; // Necesario para la fecha/hora
import java.util.List;
import java.util.Map;

@Service
public class ProduccionService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    @Autowired
    private RecetasService recetasService;

    @Autowired
    private IngredientesService ingredientesService;
    
    /**
     * Mapea una fila de la tabla 'produccion' al objeto Produccion.
     * Asumimos que las columnas de la tabla son: ID_PRODUCCION, ID_PRODUCTO, CANTIDAD_PRODUCIDA, FECHA_PRODUCCION.
     */
    private final RowMapper<Produccion> produccionRowMapper = (rs, rowNum) -> {
        Produccion produccion = new Produccion();

        // Mapeo de la clave primaria
        produccion.setIdProduccion(rs.getLong("ID_PRODUCCION"));

        // Mapeo de la clave foránea
        produccion.setIdProducto(rs.getLong("ID_PRODUCTO"));

        // Mapeo de cantidad (usamos BigDecimal)
        produccion.setCantidadProducida(rs.getBigDecimal("CANTIDAD_PRODUCIDA"));

        // Mapeo de la fecha (Convertir Timestamp de SQL a LocalDateTime de Java)
        produccion.setFechaProduccion(rs.getTimestamp("FECHA_PRODUCCION").toLocalDateTime());

        return produccion;
    };

    // ==========================================================
    // 2. MÉTODO DE LISTADO (CORREGIDO)
    // ==========================================================
    /**
     * Obtiene todos los registros del historial de producción de la tabla 'produccion'.
     */
    public List<Produccion> obtenerTodoElHistorial() {
        String sql = "SELECT * FROM produccion ORDER BY FECHA_PRODUCCION DESC";
        // Aquí se usa el RowMapper para que cada fila se convierta en un objeto Produccion
        return jdbcTemplate.query(sql, produccionRowMapper);
    }

    // === CREATE (MEJORADO Y CORREGIDO) ===
    /**
     * Registra la producción de un producto de forma robusta.
     */
    @Transactional
    public void registrarProduccion(ProduccionRequest request) {
        // CORREGIDO: Se usa el Long directamente desde el request
        Long idProducto = request.getIdProducto();
        int cantidadProducida = request.getCantidadProducida();

        // 1. Obtener la receta para el producto
        // La llamada ya no falla porque obtenerRecetaPorProducto ahora acepta Long
        List<RecetaProducto> receta = recetasService.obtenerRecetaPorProducto(idProducto);

        if (receta.isEmpty()) {
            throw new IllegalArgumentException("No se encontró receta para el producto ID " + idProducto);
        }

        // 2. Descontar cada ingrediente según la cantidad total producida
        for (RecetaProducto item : receta) {
            BigDecimal consumoTotal = item.getCantidadRequerida().multiply(BigDecimal.valueOf(cantidadProducida));

            try {
                // NOTA: asumimos que getIdIngrediente() de RecetaProducto retorna un int
                int updated = ingredientesService.descontarStock(item.getIdIngrediente(), consumoTotal);
                if (updated == 0) {
                    // Esto indica que o el ingrediente no existe o el stock era insuficiente.
                    throw new IllegalStateException("Stock insuficiente o ingrediente no encontrado (ID: " + item.getIdIngrediente() + ")");
                }
            } catch (IllegalStateException e) {
                // Re-lanza con un mensaje más claro para el frontend
                throw new IllegalStateException("Error de inventario: " + e.getMessage());
            }
        }

        // 3. aumentar stock del producto terminado
        String sqlUpdateProducto = "UPDATE productos SET STOCK_ACTUAL = STOCK_ACTUAL + ? WHERE ID_PRODUCTO = ?";
        int updated = jdbcTemplate.update(sqlUpdateProducto, cantidadProducida, idProducto);

        if (updated == 0) {
            throw new IllegalArgumentException("No se encontró producto con ID " + idProducto);
        }

        // 4. registrar en tabla produccion
        // Usamos la constante NOW() de MySQL para la fecha
        String sqlInsert = "INSERT INTO produccion (ID_PRODUCTO, CANTIDAD_PRODUCIDA, FECHA_PRODUCCION) VALUES (?, ?, NOW())";
        jdbcTemplate.update(sqlInsert, idProducto, cantidadProducida);
    }

    // === UPDATE (PUT) ===
    // (Omitidos para brevedad, no se modificaron)
    @Transactional
    public void actualizarProduccion(Long idProduccion, ProduccionRequest request) {
        String sql = "UPDATE produccion SET ID_PRODUCTO = ?, CANTIDAD_PRODUCIDA = ? WHERE ID_PRODUCCION = ?";
        int rows = jdbcTemplate.update(sql, request.getIdProducto(), request.getCantidadProducida(), idProduccion);

        if (rows == 0) {
            throw new IllegalArgumentException("Producción con ID " + idProduccion + " no encontrada.");
        }
    }

    // === PATCH (actualización parcial) ===
    // (Omitidos para brevedad, no se modificaron)
    @Transactional
    public void actualizarParcial(Long idProduccion, Map<String, Object> updates) {
        StringBuilder sql = new StringBuilder("UPDATE produccion SET ");
        boolean first = true;

        if (updates.containsKey("cantidadProducida")) {
            if (!first) sql.append(", ");
            sql.append("CANTIDAD_PRODUCIDA = ").append(updates.get("cantidadProducida"));
            first = false;
        }
        if (updates.containsKey("idProducto")) {
            if (!first) sql.append(", ");
            sql.append("ID_PRODUCTO = ").append(updates.get("idProducto"));
            first = false;
        }

        sql.append(" WHERE ID_PRODUCCION = ").append(idProduccion);

        int rows = jdbcTemplate.update(sql.toString());
        if (rows == 0) {
            throw new IllegalArgumentException("Producción con ID " + idProduccion + " no encontrada.");
        }
    }

    // === DELETE (Lógica de reversión omitida, solo la eliminación) ===
    // En un sistema real, antes de eliminar, deberías revertir el inventario.
    @Transactional
    public void eliminarProduccion(Long idProduccion) {
        String sql = "DELETE FROM produccion WHERE ID_PRODUCCION = ?";
        int rows = jdbcTemplate.update(sql, idProduccion);

        if (rows == 0) {
            throw new IllegalArgumentException("Producción con ID " + idProduccion + " no encontrada.");
        }
    }
}
