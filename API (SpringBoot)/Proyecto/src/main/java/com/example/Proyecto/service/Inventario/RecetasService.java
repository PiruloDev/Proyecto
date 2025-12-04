package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.dto.RecetaRequest;
import com.example.Proyecto.model.RecetaProducto;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import java.util.Objects;

@Service
public class RecetasService {

    @Autowired
    private JdbcTemplate jdbcTemplate;


    private RowMapper<RecetaProducto> recetaRowMapper = new RowMapper<RecetaProducto>() {
        @Override
        public RecetaProducto mapRow(ResultSet rs, int rowNum) throws SQLException {
            RecetaProducto receta = new RecetaProducto();

            receta.setIdReceta(rs.getLong("ID_RECETA"));
            receta.setIdProducto(rs.getLong("ID_PRODUCTO"));
            receta.setIdIngrediente(rs.getLong("ID_INGREDIENTE"));
            receta.setCantidadRequerida(rs.getBigDecimal("CANTIDAD_REQUERIDA"));
            receta.setIdUnidad(rs.getLong("ID_UNIDAD"));

            return receta;
        }
    };

    // =========================================================================
    // MÉTODOS CRUD
    // =========================================================================

    /**
     * Obtiene todos los detalles de una receta a partir del ID del producto.
     */
    public List<RecetaProducto> obtenerRecetaPorIdProducto(Long idProducto) {
        String sql = "SELECT rd.ID_RECETA, r.ID_PRODUCTO, rd.ID_INGREDIENTE, rd.CANTIDAD_REQUERIDA, rd.ID_UNIDAD " +
                "FROM RECETAS_DETALLE rd " +
                "JOIN RECETAS r ON rd.ID_RECETA = r.ID_RECETA " +
                "WHERE r.ID_PRODUCTO = ?";

        return jdbcTemplate.query(sql, recetaRowMapper, idProducto);
    }

    public List<RecetaProducto> obtenerTodasLasRecetas() {
        String sql = "SELECT rd.ID_RECETA, r.ID_PRODUCTO, rd.ID_INGREDIENTE, rd.CANTIDAD_REQUERIDA, rd.ID_UNIDAD " +
                "FROM RECETAS_DETALLE rd " + "JOIN RECETAS r ON rd.ID_RECETA = r.ID_RECETA";

        return jdbcTemplate.query(sql, recetaRowMapper);
    }

    @Transactional
    public void crearReceta(RecetaRequest request) {
        if (request.getIdProducto() == null) {
            throw new IllegalArgumentException("El ID de producto es obligatorio para crear una receta.");
        }

        // 1. Insertar en la tabla RECETAS (ENCABEZADO) y obtener el ID generado
        String sqlInsertEncabezado = "INSERT INTO RECETAS (ID_PRODUCTO, NOMBRE_RECETA) VALUES (?, ?)";
        KeyHolder keyHolder = new GeneratedKeyHolder();

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sqlInsertEncabezado, new String[]{"ID_RECETA"});
            ps.setLong(1, request.getIdProducto());
            // Se usa un nombre por defecto
            ps.setString(2, "Receta para Producto ID " + request.getIdProducto());
            return ps;
        }, keyHolder);

        // Obtener el ID de la receta recién creada
        Long idReceta = Objects.requireNonNull(keyHolder.getKey()).longValue();

        // 2. Insertar detalles
        String sqlInsertDetalle = "INSERT INTO RECETAS_DETALLE (ID_RECETA, ID_INGREDIENTE, CANTIDAD_REQUERIDA, ID_UNIDAD) VALUES (?, ?, ?, ?)";

        if (request.getIngredientes() != null && !request.getIngredientes().isEmpty()) {
            for (RecetaRequest.IngredienteReceta ing : request.getIngredientes()) {
                // Los tipos de datos ya son consistentes (Long y BigDecimal)
                jdbcTemplate.update(sqlInsertDetalle,
                        idReceta,
                        ing.getIdIngrediente(),
                        ing.getCantidadNecesaria(),
                        ing.getIdUnidad()
                );
            }
        }
    }

    /**
     * UPDATE: Reemplaza una receta existente (borra y vuelve a insertar).
     */
    @Transactional
    public void actualizarReceta(Long idProducto, RecetaRequest request) {
        // 1. Obtener el ID_RECETA para el ID_PRODUCTO dado
        String sqlSelectReceta = "SELECT ID_RECETA FROM RECETAS WHERE ID_PRODUCTO = ?";
        Long idReceta;
        try {
            idReceta = jdbcTemplate.queryForObject(sqlSelectReceta, Long.class, idProducto);
        } catch (EmptyResultDataAccessException e) {
            throw new IllegalArgumentException("No se encontró receta para el producto ID " + idProducto);
        }

        // 2. Eliminar los detalles de la receta antigua
        String sqlDelete = "DELETE FROM RECETAS_DETALLE WHERE ID_RECETA = ?";
        jdbcTemplate.update(sqlDelete, idReceta);

        // 3. Insertar los nuevos detalles
        String sqlInsert = "INSERT INTO RECETAS_DETALLE (ID_RECETA, ID_INGREDIENTE, CANTIDAD_REQUERIDA, ID_UNIDAD) VALUES (?, ?, ?, ?)";

        if (request.getIngredientes() != null && !request.getIngredientes().isEmpty()) {
            for (RecetaRequest.IngredienteReceta ing : request.getIngredientes()) {
                jdbcTemplate.update(sqlInsert,
                        idReceta,
                        ing.getIdIngrediente(),
                        ing.getCantidadNecesaria(),
                        ing.getIdUnidad()
                );
            }
        }
    }

    /**
     * DELETE: Elimina la receta (encabezado y detalles).
     */
    @Transactional
    public void eliminarReceta(Long idProducto) {
        // 1. Obtener el ID_RECETA para el ID_PRODUCTO dado
        String sqlSelectReceta = "SELECT ID_RECETA FROM RECETAS WHERE ID_PRODUCTO = ?";
        Long idReceta;
        try {
            idReceta = jdbcTemplate.queryForObject(sqlSelectReceta, Long.class, idProducto);
        } catch (EmptyResultDataAccessException e) {
            throw new IllegalArgumentException("No se encontró receta para el producto ID " + idProducto);
        }

        // 2. Eliminar detalles
        String sqlDeleteDetalle = "DELETE FROM RECETAS_DETALLE WHERE ID_RECETA = ?";
        jdbcTemplate.update(sqlDeleteDetalle, idReceta);

        // 3. Eliminar encabezado
        String sqlDeleteEncabezado = "DELETE FROM RECETAS WHERE ID_RECETA = ?";
        int rows = jdbcTemplate.update(sqlDeleteEncabezado, idReceta);

        if (rows == 0) {
            throw new IllegalArgumentException("No se pudo eliminar la receta con ID de Producto " + idProducto);
        }
    }
}