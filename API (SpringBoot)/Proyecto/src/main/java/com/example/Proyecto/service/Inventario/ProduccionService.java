package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.dto.ProduccionRequest;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.Map;

@Service
public class ProduccionService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // === CREATE ===
    @Transactional
    public void registrarProduccion(ProduccionRequest request) {
        // En un ambiente real, necesitarías una función validarStockIngredientes(request) aquí.

        // 1. aumentar stock del producto terminado
        String sqlUpdateProducto = "UPDATE productos SET STOCK_ACTUAL = STOCK_ACTUAL + ? WHERE ID_PRODUCTO = ?";
        int updated = jdbcTemplate.update(sqlUpdateProducto, request.getCantidadProducida(), request.getIdProducto());

        if (updated == 0) {
            throw new IllegalArgumentException("No se encontró producto con ID " + request.getIdProducto());
        }

        // 2. descontar ingredientes
        // Ojo: Se asume que request.getIngredientesDescontados() tiene los datos correctos para el descuento.
        String sqlUpdateIng = "UPDATE ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE - ? WHERE ID_INGREDIENTE = ?";
        for (ProduccionRequest.IngredienteDescontado ing : request.getIngredientesDescontados()) {
            jdbcTemplate.update(sqlUpdateIng, ing.getCantidadUsada().intValue(), ing.getIdIngrediente());
        }

        // 3. registrar en tabla produccion (si tienes una)
        String sqlInsert = "INSERT INTO produccion (ID_PRODUCTO, CANTIDAD_PRODUCIDA, FECHA) VALUES (?, ?, NOW())";
        jdbcTemplate.update(sqlInsert, request.getIdProducto(), request.getCantidadProducida());
    }

    // === READ lo tienes con obtenerReceta en RecetasService ===

    // === UPDATE (PUT) ===
    @Transactional
    public void actualizarProduccion(Long idProduccion, ProduccionRequest request) {
        String sql = "UPDATE produccion SET ID_PRODUCTO = ?, CANTIDAD_PRODUCIDA = ? WHERE ID_PRODUCCION = ?";
        int rows = jdbcTemplate.update(sql, request.getIdProducto(), request.getCantidadProducida(), idProduccion);

        if (rows == 0) {
            throw new IllegalArgumentException("Producción con ID " + idProduccion + " no encontrada.");
        }
    }

    // === PATCH (actualización parcial) ===
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

    // === DELETE (MÉTODO FALTANTE) ===
    /**
     * Elimina un registro de producción por su ID.
     * NOTA: Una implementación completa debería también revertir los cambios de inventario
     * (restar STOCK_ACTUAL al producto y re-sumar los ingredientes).
     * @param idProduccion El ID del registro de producción a eliminar.
     */
    @Transactional
    public void eliminarProduccion(Long idProduccion) {
        // En una aplicación de inventario robusta, aquí se debe:
        // 1. Consultar la cantidad producida y la receta asociada a idProduccion.
        // 2. Revertir el stock: restar la cantidad producida del producto.
        // 3. Revertir los ingredientes: sumar los ingredientes usados de nuevo al stock.

        // Lógica de eliminación directa del registro (mínimo necesario para compilar)
        String sql = "DELETE FROM produccion WHERE ID_PRODUCCION = ?";
        int rows = jdbcTemplate.update(sql, idProduccion);

        if (rows == 0) {
            throw new IllegalArgumentException("Producción con ID " + idProduccion + " no encontrada.");
        }
    }
}