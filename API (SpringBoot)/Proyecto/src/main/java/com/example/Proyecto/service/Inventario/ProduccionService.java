package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.dto.ProduccionRequest;
import com.example.Proyecto.model.Produccion;
import com.example.Proyecto.model.RecetaProducto;
import com.example.Proyecto.service.Ingredientes.IngredientesService;
import com.example.Proyecto.service.Productos.ProductosService; // <--- INYECCIÓN CLAVE
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.sql.PreparedStatement;
import java.sql.Timestamp;
import java.time.LocalDateTime;
import java.util.List;
import java.util.Map;
import java.util.Objects;

@Service
public class ProduccionService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    @Autowired
    private RecetasService recetasService;

    @Autowired
    private IngredientesService ingredientesService;

    @Autowired
    private ProductosService productosService;

    private final RowMapper<Produccion> produccionRowMapper = (rs, rowNum) -> {
        Produccion produccion = new Produccion();
        produccion.setIdProduccion(rs.getLong("ID_PRODUCCION"));
        produccion.setIdProducto(rs.getLong("ID_PRODUCTO"));
        produccion.setCantidadProducida(rs.getBigDecimal("CANTIDAD_PRODUCIDA"));
        produccion.setFechaProduccion(rs.getTimestamp("FECHA_PRODUCCION").toLocalDateTime());
        return produccion;
    };


    public List<Produccion> obtenerTodoElHistorial() {
        String sql = "SELECT * FROM produccion ORDER BY FECHA_PRODUCCION DESC";
        return jdbcTemplate.query(sql, produccionRowMapper);
    }


    @Transactional
    public Long registrarProduccion(ProduccionRequest request) {
        Long idProducto = request.getIdProducto();
        BigDecimal cantidadProducida = request.getCantidadProducida();

        List<RecetaProducto> receta = recetasService.obtenerRecetaPorIdProducto(idProducto);

        if (receta.isEmpty()) {
            throw new IllegalArgumentException("No se encontró receta para el producto ID " + idProducto);
        }

        KeyHolder keyHolder = new GeneratedKeyHolder();
        String sqlInsert = "INSERT INTO produccion (ID_PRODUCTO, CANTIDAD_PRODUCIDA, FECHA_PRODUCCION) VALUES (?, ?, ?)";

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sqlInsert, new String[] {"ID_PRODUCCION"});
            ps.setLong(1, idProducto);
            ps.setBigDecimal(2, cantidadProducida);
            ps.setTimestamp(3, Timestamp.valueOf(LocalDateTime.now()));
            return ps;
        }, keyHolder);

        Long idProduccion = Objects.requireNonNull(keyHolder.getKey()).longValue();


        for (RecetaProducto detalle : receta) {
            BigDecimal consumoTotal = detalle.getCantidadRequerida().multiply(cantidadProducida);

            ingredientesService.actualizarStock(detalle.getIdIngrediente(), consumoTotal.negate());
        }

        productosService.actualizarStockProducto(idProducto, cantidadProducida);

        return idProduccion;
    }

    @Transactional
    public void eliminarProduccion(Long idProduccion) {
        // 1. Obtener el registro de producción (para saber qué revertir)
        String sqlSelect = "SELECT * FROM produccion WHERE ID_PRODUCCION = ?";
        Produccion produccion;
        try {
            produccion = jdbcTemplate.queryForObject(sqlSelect, produccionRowMapper, idProduccion);
        } catch (org.springframework.dao.EmptyResultDataAccessException e) {
            throw new IllegalArgumentException("Producción con ID " + idProduccion + " no encontrada.");
        }

        Long idProducto = produccion.getIdProducto();
        BigDecimal cantidadProducida = produccion.getCantidadProducida();

        List<RecetaProducto> receta = recetasService.obtenerRecetaPorIdProducto(idProducto);
        if (receta.isEmpty()) {
            throw new IllegalStateException("No se encontró receta para el producto ID " + idProducto + ". No se puede revertir el inventario.");
        }


        // 3a. Reponer ingredientes
        for (RecetaProducto detalle : receta) {
            // Cantidad total a reponer = Cantidad_Requerida_por_Receta * Cantidad_Producida
            BigDecimal reposicionTotal = detalle.getCantidadRequerida().multiply(cantidadProducida);

            // Llamada al servicio de ingredientes (usa cantidad positiva para reponer)
            ingredientesService.actualizarStock(detalle.getIdIngrediente(), reposicionTotal);
        }

        // 3b. Disminuir stock del producto terminado
        productosService.actualizarStockProducto(idProducto, cantidadProducida.negate());

        // 4. Eliminar el registro de producción
        String sqlDelete = "DELETE FROM produccion WHERE ID_PRODUCCION = ?";
        int rows = jdbcTemplate.update(sqlDelete, idProduccion);

        if (rows == 0) {
            throw new IllegalArgumentException("Error al eliminar el registro de producción ID " + idProduccion);
        }
    }

    @Transactional
    public void actualizarParcial(Long idProduccion, Map<String, Object> updates) {
        // Implementación simplificada (se asume que existe)
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
}