package com.example.Proyecto.service.Ingredientes;

import com.example.Proyecto.model.Ingredientes;
import com.example.Proyecto.dto.IngredientesCantidad;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.dao.EmptyResultDataAccessException;


import java.math.BigDecimal;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class IngredientesService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    private RowMapper<Ingredientes> ingredienteRowMapper = new RowMapper<Ingredientes>() {
        @Override
        public Ingredientes mapRow(ResultSet rs, int rowNum) throws SQLException {
            Ingredientes ingrediente = new Ingredientes();

            ingrediente.setIdIngrediente(rs.getLong("ID_INGREDIENTE"));
            ingrediente.setIdProveedor(rs.getLong("ID_PROVEEDOR"));
            ingrediente.setIdCategoria(rs.getLong("ID_CATEGORIA"));

            ingrediente.setNombreIngrediente(rs.getString("NOMBRE_INGREDIENTE"));
            ingrediente.setCantidadIngrediente(rs.getBigDecimal("CANTIDAD_INGREDIENTE"));
            ingrediente.setFechaVencimiento(rs.getDate("FECHA_VENCIMIENTO"));
            ingrediente.setReferenciaIngrediente(rs.getString("REFERENCIA_INGREDIENTE"));
            ingrediente.setFechaEntregaIngrediente(rs.getDate("FECHA_ENTREGA_INGREDIENTE"));

            return ingrediente;
        }
    };

    private RowMapper<Ingredientes> ingredienteListadoRowMapper = new RowMapper<Ingredientes>() {
        @Override
        public Ingredientes mapRow(ResultSet rs, int rowNum) throws SQLException {
            Ingredientes ingrediente = new Ingredientes();

            // Solo mapea los campos que vamos a seleccionar en el SQL.
            ingrediente.setIdIngrediente(rs.getLong("ID_INGREDIENTE"));
            ingrediente.setIdProveedor(rs.getLong("ID_PROVEEDOR"));
            ingrediente.setIdCategoria(rs.getLong("ID_CATEGORIA"));
            ingrediente.setNombreIngrediente(rs.getString("NOMBRE_INGREDIENTE"));
            ingrediente.setReferenciaIngrediente(rs.getString("REFERENCIA_INGREDIENTE"));

            return ingrediente;
        }
    };
    //Row Mapper para cantidad de ingredientes
    private RowMapper<IngredientesCantidad> ingredientesCantidadRowMapper = (rs, rowNum) -> {
        IngredientesCantidad dto = new IngredientesCantidad();
        dto.setIdIngrediente(rs.getLong("ID_INGREDIENTE"));
        dto.setNombreIngrediente(rs.getString("NOMBRE_INGREDIENTE"));
        dto.setCantidadIngrediente(rs.getBigDecimal("CANTIDAD_INGREDIENTE"));
        return dto;
    };


    public List<String> obtenerIngredientes() {
        String sql = "SELECT NOMBRE_INGREDIENTE FROM Ingredientes ORDER BY NOMBRE_INGREDIENTE";
        return jdbcTemplate.queryForList(sql, String.class);
    }

    public List<Ingredientes> obtenerTodosLosIngredientes() {
        String sql = "SELECT * FROM Ingredientes";
        return jdbcTemplate.query(sql, ingredienteRowMapper);
    }

    public List<Ingredientes> obtenerIngredientesParaListado() {
        String sql = "SELECT ID_INGREDIENTE, ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, REFERENCIA_INGREDIENTE FROM Ingredientes";
        return jdbcTemplate.query(sql, ingredienteListadoRowMapper);
    }

    public void crearIngrediente(Ingredientes ingrediente) {
        String sql = "INSERT INTO Ingredientes (ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, REFERENCIA_INGREDIENTE, CANTIDAD_INGREDIENTE, FECHA_VENCIMIENTO, FECHA_ENTREGA_INGREDIENTE) " +
                "VALUES (?, ?, ?, ?, ?, NULL, NULL)";

        BigDecimal cantidadInicial = BigDecimal.ZERO;

        jdbcTemplate.update(sql,
                ingrediente.getIdProveedor(),
                ingrediente.getIdCategoria(),
                ingrediente.getNombreIngrediente(),
                ingrediente.getReferenciaIngrediente(),
                cantidadInicial
        );
    }

    public int editarIngrediente(Long id, Ingredientes ingrediente) {
        String sql = "UPDATE Ingredientes SET " +
                "ID_PROVEEDOR = ?, " +
                "ID_CATEGORIA = ?, " +
                "NOMBRE_INGREDIENTE = ?, " +
                "REFERENCIA_INGREDIENTE = ? " +
                "WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sql,
                ingrediente.getIdProveedor(),
                ingrediente.getIdCategoria(),
                ingrediente.getNombreIngrediente(),
                ingrediente.getReferenciaIngrediente(),
                id
        );
    }


    public int actualizarCantidad(Long id, BigDecimal cantidad) {
        String sql = "UPDATE Ingredientes SET CANTIDAD_INGREDIENTE = ? WHERE ID_INGREDIENTE = ?";
        return jdbcTemplate.update(sql, cantidad, id);
    }

    public int eliminarIngrediente(Long id) {
        String sql = "DELETE FROM Ingredientes WHERE ID_INGREDIENTE = ?";
        return jdbcTemplate.update(sql, id);
    }

    public List<IngredientesCantidad> obtenerIngredientesCantidad() {
        String sql = "SELECT ID_INGREDIENTE, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE " +
                "FROM Ingredientes " +
                "ORDER BY NOMBRE_INGREDIENTE";

        return jdbcTemplate.query(sql, ingredientesCantidadRowMapper);
    }


    @Transactional
    public int actualizarStock(Long idIngrediente, BigDecimal cantidadAjuste) {
        if (cantidadAjuste.signum() > 0) {
            // Si es positivo, repone (se usa en reversión)
            return reponerStock(idIngrediente, cantidadAjuste);
        } else {
            // Si es negativo, descuenta (se usa en producción).
            // Se usa abs() porque descontarStock espera la cantidad positiva a restar.
            return descontarStock(idIngrediente, cantidadAjuste.abs());
        }
    }

    @Transactional
    public int descontarStock(Long idIngrediente, BigDecimal cantidadConsumida) {
        // 1. Obtener la cantidad actual para validar (más seguro)
        String sqlSelect = "SELECT CANTIDAD_INGREDIENTE FROM Ingredientes WHERE ID_INGREDIENTE = ?";
        BigDecimal stockActual;
        try {
            stockActual = jdbcTemplate.queryForObject(sqlSelect, BigDecimal.class, idIngrediente);
        } catch (EmptyResultDataAccessException e) {
            throw new IllegalArgumentException("Ingrediente con ID " + idIngrediente + " no encontrado.", e);
        }

        if (stockActual.compareTo(cantidadConsumida) < 0) {
            throw new IllegalStateException("Stock insuficiente para el ingrediente ID " + idIngrediente + ". Stock actual: " + stockActual + ", Consumo requerido: " + cantidadConsumida);
        }

        String sqlUpdate = "UPDATE Ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE - ? WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sqlUpdate, cantidadConsumida, idIngrediente);
    }

    @Transactional
    public int reponerStock(Long idIngrediente, BigDecimal cantidadRepuesta) {
        String sqlUpdate = "UPDATE Ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE + ? WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sqlUpdate, cantidadRepuesta, idIngrediente);
    }
}