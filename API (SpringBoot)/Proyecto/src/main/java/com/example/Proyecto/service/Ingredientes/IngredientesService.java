package com.example.Proyecto.service.Ingredientes;

import com.example.Proyecto.model.Ingredientes;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.math.BigDecimal;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import org.springframework.jdbc.core.RowMapper;

@Service
public class IngredientesService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // RowMapper para el modelo Ingredientes
    private RowMapper<Ingredientes> ingredienteRowMapper = new RowMapper<Ingredientes>() {
        @Override
        public Ingredientes mapRow(ResultSet rs, int rowNum) throws SQLException {
            Ingredientes ingrediente = new Ingredientes();
            ingrediente.setIdIngrediente(rs.getInt("ID_INGREDIENTE"));
            ingrediente.setIdProveedor(rs.getInt("ID_PROVEEDOR"));
            ingrediente.setIdCategoria(rs.getInt("ID_CATEGORIA"));
            ingrediente.setNombreIngrediente(rs.getString("NOMBRE_INGREDIENTE"));

            // Asumiendo que CANTIDAD_INGREDIENTE es un tipo que puede ser mapeado a BigDecimal
            // y no necesariamente un Integer.
            ingrediente.setCantidadIngrediente(rs.getBigDecimal("CANTIDAD_INGREDIENTE"));

            ingrediente.setFechaVencimiento(rs.getDate("FECHA_VENCIMIENTO"));
            ingrediente.setReferenciaIngrediente(rs.getString("REFERENCIA_INGREDIENTE"));
            ingrediente.setFechaEntregaIngrediente(rs.getDate("FECHA_ENTREGA_INGREDIENTE"));
            return ingrediente;
        }
    };


    // Metodo para obtener lista de nombres de ingredientes
    public List<String> obtenerIngredientes() {
        String sql = "SELECT NOMBRE_INGREDIENTE FROM Ingredientes";
        return jdbcTemplate.query(sql, new RowMapper<String>() {
            @Override
            public String mapRow(ResultSet rs, int rowNum) throws SQLException {
                return rs.getString("NOMBRE_INGREDIENTE");
            }
        });
    }

    // Metodo para obtener todos los ingredientes
    public List<Ingredientes> obtenerTodosLosIngredientes() {
        String sql = "SELECT * FROM Ingredientes";
        return jdbcTemplate.query(sql, ingredienteRowMapper);
    }


    // Metodo para crear un nuevo ingrediente (POST)
    public void crearIngrediente(Ingredientes ingrediente) {
        String sql = "INSERT INTO Ingredientes (ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE, FECHA_VENCIMIENTO, REFERENCIA_INGREDIENTE, FECHA_ENTREGA_INGREDIENTE) " +
                "VALUES (?, ?, ?, ?, ?, ?, ?)";
        jdbcTemplate.update(sql,
                ingrediente.getIdProveedor(),
                ingrediente.getIdCategoria(),
                ingrediente.getNombreIngrediente(),
                ingrediente.getCantidadIngrediente(),
                ingrediente.getFechaVencimiento(),
                ingrediente.getReferenciaIngrediente(),
                ingrediente.getFechaEntregaIngrediente()
        );
    }

    // Metodo para actualizar un ingrediente (PUT)
    public int editarIngrediente(Ingredientes ingrediente) {
        String sql = "UPDATE Ingredientes SET " +
                "ID_PROVEEDOR = ?, " +
                "ID_CATEGORIA = ?, " +
                "NOMBRE_INGREDIENTE = ?, " +
                "CANTIDAD_INGREDIENTE = ?, " +
                "FECHA_VENCIMIENTO = ?, " +
                "REFERENCIA_INGREDIENTE = ?, " +
                "FECHA_ENTREGA_INGREDIENTE = ? " +
                "WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sql,
                ingrediente.getIdProveedor(),
                ingrediente.getIdCategoria(),
                ingrediente.getNombreIngrediente(),
                ingrediente.getCantidadIngrediente(),
                ingrediente.getFechaVencimiento(),
                ingrediente.getReferenciaIngrediente(),
                ingrediente.getFechaEntregaIngrediente(),
                ingrediente.getIdIngrediente()
        );
    }


    // Metodo para eliminar un ingrediente (DELETE)
    public int eliminarIngrediente(int idIngrediente) {
        String sql = "DELETE FROM Ingredientes WHERE ID_INGREDIENTE = ?";
        return jdbcTemplate.update(sql, idIngrediente);
    }

    // Método para actualizar cantidad de ingrediente (GENÉRICO)
    public int actualizarCantidad(int id, int cantidad) {
        // NOTA: Para uso general, se recomienda usar BigDecimal aquí en lugar de int para consistencia.
        return jdbcTemplate.update(
                "UPDATE Ingredientes SET CANTIDAD_INGREDIENTE=? WHERE ID_INGREDIENTE=?",
                cantidad, id
        );
    }


    // ================== MÉTODOS DE GESTIÓN DE STOCK POR PRODUCCIÓN ==================

    /**
     * Descuenta una cantidad específica del stock de un ingrediente de forma segura.
     *
     * @param idIngrediente ID del ingrediente.
     * @param cantidadConsumida Cantidad a restar del stock.
     * @return Número de filas actualizadas (debería ser 1).
     * @throws IllegalStateException Si el stock es insuficiente.
     */
    public int descontarStock(int idIngrediente, BigDecimal cantidadConsumida) {
        // 1. Obtener la cantidad actual para validar (más seguro)
        String sqlSelect = "SELECT CANTIDAD_INGREDIENTE FROM Ingredientes WHERE ID_INGREDIENTE = ?";
        BigDecimal stockActual = jdbcTemplate.queryForObject(sqlSelect, BigDecimal.class, idIngrediente);

        if (stockActual == null) {
            throw new IllegalArgumentException("Ingrediente con ID " + idIngrediente + " no encontrado.");
        }

        if (stockActual.compareTo(cantidadConsumida) < 0) {
            throw new IllegalStateException("Stock insuficiente para el ingrediente ID " + idIngrediente + ". Stock actual: " + stockActual + ", Consumo requerido: " + cantidadConsumida);
        }

        // 2. Descontar el stock
        String sqlUpdate = "UPDATE Ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE - ? WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sqlUpdate, cantidadConsumida, idIngrediente);
    }

    /**
     * Repone una cantidad específica al stock de un ingrediente.
     * Es CRÍTICO para revertir transacciones (DELETE) o para ajustes a la baja (PUT).
     *
     * @param idIngrediente ID del ingrediente.
     * @param cantidadRepuesta Cantidad a sumar al stock.
     * @return Número de filas actualizadas (debería ser 1).
     */
    public int reponerStock(int idIngrediente, BigDecimal cantidadRepuesta) {
        // Solo se suma al stock, no se necesita validación de stock mínimo.
        String sqlUpdate = "UPDATE Ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE + ? WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sqlUpdate, cantidadRepuesta, idIngrediente);
    }

}