package com.example.Proyecto.service.Ingredientes;

import com.example.Proyecto.dto.IngredienteDetalleDTO;
import com.example.Proyecto.dto.IngredienteListadoDTO;
import com.example.Proyecto.model.Ingredientes;
import com.example.Proyecto.dto.IngredientesCantidad;
import jakarta.persistence.EntityManager;
import jakarta.persistence.PersistenceContext;
import jakarta.persistence.Query;
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
import java.util.stream.Collectors;

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
            ingrediente.setReferenciaIngrediente(rs.getString("REFERENCIA_INGREDIENTE"));

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
        String sql = "SELECT NOMBRE_INGREDIENTE FROM ingredientes ORDER BY NOMBRE_INGREDIENTE";
        return jdbcTemplate.queryForList(sql, String.class);
    }

    public List<Ingredientes> obtenerTodosLosIngredientes() {
        String sql = "SELECT * FROM ingredientes";
        return jdbcTemplate.query(sql, ingredienteRowMapper);
    }

    public List<IngredienteListadoDTO> obtenerIngredientesParaListado() {
        String sql = "SELECT i.ID_INGREDIENTE, i.ID_PROVEEDOR, i.ID_CATEGORIA, " +
                "i.NOMBRE_INGREDIENTE, i.REFERENCIA_INGREDIENTE, " +
                "u.ABREVIATURA_UNIDAD " +
                "FROM ingredientes i " +
                "LEFT JOIN unidades_medida u ON i.ID_UNIDAD_MEDIDA = u.ID_UNIDAD";

        return jdbcTemplate.query(sql, (rs, rowNum) -> {
            IngredienteListadoDTO dto = new IngredienteListadoDTO();
            dto.setIdIngrediente(rs.getLong("ID_INGREDIENTE"));
            dto.setIdProveedor(rs.getLong("ID_PROVEEDOR"));
            dto.setIdCategoria(rs.getLong("ID_CATEGORIA"));
            dto.setNombreIngrediente(rs.getString("NOMBRE_INGREDIENTE"));
            dto.setReferenciaIngrediente(rs.getString("REFERENCIA_INGREDIENTE"));
            dto.setAbreviaturaUnidad(rs.getString("ABREVIATURA_UNIDAD")); // ← nuevo
            return dto;
        });
    }

    public void crearIngrediente(Ingredientes ingrediente) {
        // Eliminada la coma después de CANTIDAD_INGREDIENTE y los NULL sobrantes
        String sql = "INSERT INTO ingredientes (" +
                "ID_PROVEEDOR, " +
                "ID_CATEGORIA, " +
                "ID_UNIDAD_MEDIDA, " +
                "NOMBRE_INGREDIENTE, " +
                "REFERENCIA_INGREDIENTE, " +
                "CANTIDAD_INGREDIENTE" +
                ") VALUES (?, ?, ?, ?, ?, ?)";

        BigDecimal cantidadInicial = BigDecimal.ZERO;

        jdbcTemplate.update(sql,
                ingrediente.getIdProveedor(),
                ingrediente.getIdCategoria(),
                ingrediente.getIdUnidadMedida(),
                ingrediente.getNombreIngrediente(),
                ingrediente.getReferenciaIngrediente(),
                cantidadInicial
        );
    }

    public int editarIngrediente(Long id, Ingredientes ingrediente) {
        String sql = "UPDATE ingredientes SET " +
                "ID_PROVEEDOR = ?, " +
                "ID_CATEGORIA = ?, " +
                "ID_UNIDAD_MEDIDA = ?, " +
                "NOMBRE_INGREDIENTE = ?, " +
                "REFERENCIA_INGREDIENTE = ? " +
                "WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sql,
                ingrediente.getIdProveedor(),
                ingrediente.getIdCategoria(),
                ingrediente.getIdUnidadMedida(),
                ingrediente.getNombreIngrediente(),
                ingrediente.getReferenciaIngrediente(),
                id
        );
    }


    public int actualizarCantidad(Long id, BigDecimal cantidad) {
        String sql = "UPDATE ingredientes SET CANTIDAD_INGREDIENTE = ? WHERE ID_INGREDIENTE = ?";
        return jdbcTemplate.update(sql, cantidad, id);
    }

    public int eliminarIngrediente(Long id) {
        String sql = "DELETE FROM ingredientes WHERE ID_INGREDIENTE = ?";
        return jdbcTemplate.update(sql, id);
    }

    public List<IngredientesCantidad> obtenerIngredientesCantidad() {
        String sql = "SELECT ID_INGREDIENTE, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE " +
                "FROM ingredientes " +
                "ORDER BY NOMBRE_INGREDIENTE";

        return jdbcTemplate.query(sql, ingredientesCantidadRowMapper);
    }


    @Transactional
    public int actualizarStock(Long idIngrediente, BigDecimal cantidadAjuste) {
        if (cantidadAjuste.signum() > 0) {
            return reponerStock(idIngrediente, cantidadAjuste);
        } else {
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

        String sqlUpdate = "UPDATE ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE - ? WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sqlUpdate, cantidadConsumida, idIngrediente);
    }

    @Transactional
    public int reponerStock(Long idIngrediente, BigDecimal cantidadRepuesta) {
        String sqlUpdate = "UPDATE ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE + ? WHERE ID_INGREDIENTE = ?";

        return jdbcTemplate.update(sqlUpdate, cantidadRepuesta, idIngrediente);
    }

    @PersistenceContext
    private EntityManager entityManager;

    public List<IngredienteDetalleDTO> obtenerIngredientesParaModal() {
        String sql = "SELECT i.ID_INGREDIENTE, i.NOMBRE_INGREDIENTE, u.ABREVIATURA_UNIDAD, u.ID_UNIDAD " +
                "FROM ingredientes i " +
                "INNER JOIN unidades_medida u ON i.ID_UNIDAD_MEDIDA = u.ID_UNIDAD";

        Query query = entityManager.createNativeQuery(sql);
        List<Object[]> resultados = query.getResultList();

        return resultados.stream().map(row -> new IngredienteDetalleDTO(
                ((Number) row[0]).longValue(),
                (String) row[1],
                (String) row[2],
                ((Number) row[3]).longValue()
        )).collect(Collectors.toList());
    }
}