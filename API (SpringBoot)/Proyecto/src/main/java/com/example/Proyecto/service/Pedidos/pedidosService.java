package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.model.Pedidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.stereotype.Service;

import java.sql.*;
import java.math.BigDecimal;
import java.util.List;

@Service
public class pedidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // RowMapper mejorado para usar Timestamp
    private final RowMapper<Pedidos> pedidoRowMapper = new RowMapper<Pedidos>() {
        @Override
        public Pedidos mapRow(ResultSet rs, int rowNum) throws SQLException {
            Pedidos pedido = new Pedidos();
            pedido.setID_PEDIDO(rs.getInt("ID_PEDIDO"));
            pedido.setID_CLIENTE(rs.getLong("ID_CLIENTE"));
            pedido.setID_EMPLEADO(rs.getLong("ID_EMPLEADO"));
            pedido.setID_ESTADO_PEDIDO(rs.getLong("ID_ESTADO_PEDIDO"));

            // ✅ USAR TIMESTAMP para mejor precisión (asume que el modelo Pedidos usa java.util.Date o un equivalente)
            Timestamp tsIngreso = rs.getTimestamp("FECHA_INGRESO");
            pedido.setFECHA_INGRESO(tsIngreso != null ? new java.util.Date(tsIngreso.getTime()) : null);

            Timestamp tsEntrega = rs.getTimestamp("FECHA_ENTREGA");
            pedido.setFECHA_ENTREGA(tsEntrega != null ? new java.util.Date(tsEntrega.getTime()) : null);

            pedido.setTOTAL_PRODUCTO(rs.getBigDecimal("TOTAL_PRODUCTO"));
            return pedido;
        }
    };

    // Método para obtener todos los pedidos (GET)
    public List<Pedidos> obtenerPedidos() {
        String sql = "SELECT * FROM Pedidos";
        return jdbcTemplate.query(sql, pedidoRowMapper);
    }

    // Método para obtener un pedido por su ID (GET)
    public Pedidos obtenerPedidoPorId(Long id) {
        String sql = "SELECT * FROM Pedidos WHERE ID_PEDIDO = ?";
        try {
            return jdbcTemplate.queryForObject(sql, new Object[]{id}, pedidoRowMapper);
        } catch (EmptyResultDataAccessException e) {
            return null; // Devuelve null si no se encuentra el pedido
        }
    }

    // Método para crear un nuevo pedido (POST) - Corregido el manejo de fechas
    public int crearPedido(Pedidos pedido) {
        // ✅ Corregido: Insertamos FECHA_INGRESO al momento actual del servidor.
        // ✅ Corregido: Insertamos FECHA_ENTREGA como NULL (asumiendo que la columna lo permite).
        String sql = "INSERT INTO Pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) VALUES (?, ?, ?, ?, ?, ?)";
        KeyHolder keyHolder = new GeneratedKeyHolder();

        // Asignamos la fecha de ingreso actual del servidor (Backend)
        Timestamp fechaIngresoActual = new Timestamp(System.currentTimeMillis());

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            ps.setLong(1, pedido.getID_CLIENTE());
            ps.setLong(2, pedido.getID_EMPLEADO());
            ps.setLong(3, pedido.getID_ESTADO_PEDIDO());
            ps.setTimestamp(4, fechaIngresoActual);

            // La fecha de entrega debe ser NULL al inicio, a menos que el estado ya sea "Entregado"
            // Por simplicidad, la establecemos como NULL (o manejamos si el objeto entrante es NULL)
            if (pedido.getFECHA_ENTREGA() == null) {
                ps.setNull(5, Types.TIMESTAMP);
            } else {
                ps.setTimestamp(5, new Timestamp(pedido.getFECHA_ENTREGA().getTime()));
            }

            ps.setBigDecimal(6, pedido.getTOTAL_PRODUCTO());
            return ps;
        }, keyHolder);
        return keyHolder.getKey().intValue();
    }

    // Método para actualizar un pedido (PUT) - Corregido el manejo de fechas
    public void actualizarPedido(Long id, Pedidos nuevosDatos) {

        // 1. ✅ PASO CRÍTICO: Obtener el pedido existente para conservar la FECHA_INGRESO.
        Pedidos pedidoExistente = obtenerPedidoPorId(id);

        if (pedidoExistente == null) {
            throw new RuntimeException("Pedido con ID " + id + " no encontrado para actualizar.");
        }

        // 2. Determinar la FECHA_INGRESO y FECHA_ENTREGA a usar.
        // La FECHA_INGRESO NUNCA debe cambiar; usamos la del registro existente.
        Timestamp fechaIngresoAUsar = new Timestamp(pedidoExistente.getFECHA_INGRESO().getTime());

        // La FECHA_ENTREGA se actualiza solo si el nuevo objeto tiene un valor.
        Timestamp fechaEntregaAUsar = nuevosDatos.getFECHA_ENTREGA() != null
                ? new Timestamp(nuevosDatos.getFECHA_ENTREGA().getTime())
                : (pedidoExistente.getFECHA_ENTREGA() != null ? new Timestamp(pedidoExistente.getFECHA_ENTREGA().getTime()) : null);

        String sql = "UPDATE Pedidos SET ID_CLIENTE = ?, ID_EMPLEADO = ?, ID_ESTADO_PEDIDO = ?, FECHA_INGRESO = ?, FECHA_ENTREGA = ?, TOTAL_PRODUCTO = ? WHERE ID_PEDIDO = ?";

        // Parámetros para la actualización
        Object[] params = {
                nuevosDatos.getID_CLIENTE(),
                nuevosDatos.getID_EMPLEADO(),
                nuevosDatos.getID_ESTADO_PEDIDO(),
                fechaIngresoAUsar, // ✅ Usamos la fecha de INGRESO original
                fechaEntregaAUsar, // ✅ Usamos la fecha de ENTREGA determinada por la lógica
                nuevosDatos.getTOTAL_PRODUCTO(),
                id
        };

        // Tipos de SQL para los parámetros (especialmente necesario si las fechas son nulas)
        int[] types = {
                Types.BIGINT,
                Types.BIGINT,
                Types.BIGINT,
                Types.TIMESTAMP,
                Types.TIMESTAMP,
                Types.DECIMAL,
                Types.BIGINT
        };

        jdbcTemplate.update(sql, params, types);
    }

    // Método para eliminar un pedido (DELETE)
    public void eliminarPedido(Long id) {
        String sql = "DELETE FROM Pedidos WHERE ID_PEDIDO = ?";
        jdbcTemplate.update(sql, id);
    }
}