package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.model.Pedidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.stereotype.Service;

import java.sql.*;
import java.util.List;

@Service
public class pedidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    /* =====================================================
       VALIDAR EXISTENCIA DEL CLIENTE (NUEVO - OBLIGATORIO)
       ===================================================== */
    private boolean clienteExiste(long idCliente) {
        String sql = "SELECT COUNT(*) FROM clientes WHERE ID_CLIENTE = ?";
        Integer count = jdbcTemplate.queryForObject(sql, Integer.class, idCliente);
        return count != null && count > 0;
    }

    /* =========================
       ROW MAPPER
       ========================= */
    private final RowMapper<Pedidos> pedidoRowMapper = (rs, rowNum) -> {
        Pedidos pedido = new Pedidos();
        pedido.setID_PEDIDO(rs.getInt("ID_PEDIDO"));
        pedido.setID_CLIENTE(rs.getLong("ID_CLIENTE"));
        pedido.setID_EMPLEADO(rs.getLong("ID_EMPLEADO"));
        pedido.setID_ESTADO_PEDIDO(rs.getLong("ID_ESTADO_PEDIDO"));

        Timestamp tsIngreso = rs.getTimestamp("FECHA_INGRESO");
        pedido.setFECHA_INGRESO(tsIngreso != null ? tsIngreso.toLocalDateTime() : null);

        Timestamp tsEntrega = rs.getTimestamp("FECHA_ENTREGA");
        pedido.setFECHA_ENTREGA(tsEntrega != null ? tsEntrega.toLocalDateTime() : null);

        pedido.setTOTAL_PRODUCTO(rs.getBigDecimal("TOTAL_PRODUCTO"));
        return pedido;
    };

    /* =========================
       CONSULTAS
       ========================= */

    public List<Pedidos> obtenerPedidos() {
        String sql = "SELECT * FROM Pedidos";
        return jdbcTemplate.query(sql, pedidoRowMapper);
    }

    public List<Pedidos> obtenerPedidosPorCliente(Long clienteId) {
        String sql = "SELECT * FROM Pedidos WHERE ID_CLIENTE = ?";

        try {
            return jdbcTemplate.query(sql, new Object[]{clienteId}, pedidoRowMapper);
        } catch (DataAccessException e) {
            System.err.println("Error al obtener pedidos para el cliente " + clienteId + ": " + e.getMessage());
            return List.of();
        }
    }

    public Pedidos obtenerPedidoPorId(Long id) {
        String sql = "SELECT * FROM Pedidos WHERE ID_PEDIDO = ?";
        try {
            return jdbcTemplate.queryForObject(sql, new Object[]{id}, pedidoRowMapper);
        } catch (EmptyResultDataAccessException e) {
            return null;
        }
    }

    /* =========================
       CREAR PEDIDO (CORREGIDO)
       ========================= */
    public int crearPedido(Pedidos pedido) {

        // 🔥 VALIDACIÓN CLAVE
        if (!clienteExiste(pedido.getID_CLIENTE())) {
            throw new RuntimeException("El cliente con ID " + pedido.getID_CLIENTE() + " no existe");
        }

        String sql = "INSERT INTO Pedidos " +
                "(ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) " +
                "VALUES (?, ?, ?, ?, ?, ?)";

        KeyHolder keyHolder = new GeneratedKeyHolder();
        Timestamp fechaIngresoActual = new Timestamp(System.currentTimeMillis());

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            ps.setLong(1, pedido.getID_CLIENTE());
            ps.setLong(2, pedido.getID_EMPLEADO());
            ps.setLong(3, pedido.getID_ESTADO_PEDIDO());
            ps.setTimestamp(4, fechaIngresoActual);

            if (pedido.getFECHA_ENTREGA() == null) {
                ps.setNull(5, Types.TIMESTAMP);
            } else {
                ps.setTimestamp(5, java.sql.Timestamp.valueOf(pedido.getFECHA_ENTREGA()));
            }

            ps.setBigDecimal(6, pedido.getTOTAL_PRODUCTO());
            return ps;
        }, keyHolder);

        return keyHolder.getKey().intValue();
    }

    /* =========================
       ACTUALIZAR PEDIDO
       ========================= */
    public void actualizarPedido(Long id, Pedidos nuevosDatos) {

        Pedidos pedidoExistente = obtenerPedidoPorId(id);

        if (pedidoExistente == null) {
            throw new RuntimeException("Pedido con ID " + id + " no encontrado para actualizar.");
        }

        Timestamp fechaIngresoAUsar = Timestamp.valueOf(pedidoExistente.getFECHA_INGRESO());

        Timestamp fechaEntregaAUsar = nuevosDatos.getFECHA_ENTREGA() != null
                ? Timestamp.valueOf(nuevosDatos.getFECHA_ENTREGA())
                : (pedidoExistente.getFECHA_ENTREGA() != null
                ? Timestamp.valueOf(pedidoExistente.getFECHA_ENTREGA())
                : null);

        String sql = "UPDATE Pedidos SET ID_CLIENTE = ?, ID_EMPLEADO = ?, ID_ESTADO_PEDIDO = ?, " +
                "FECHA_INGRESO = ?, FECHA_ENTREGA = ?, TOTAL_PRODUCTO = ? WHERE ID_PEDIDO = ?";

        Object[] params = {
                nuevosDatos.getID_CLIENTE(),
                nuevosDatos.getID_EMPLEADO(),
                nuevosDatos.getID_ESTADO_PEDIDO(),
                fechaIngresoAUsar,
                fechaEntregaAUsar,
                nuevosDatos.getTOTAL_PRODUCTO(),
                id
        };

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

    /* =========================
       ELIMINAR PEDIDO
       ========================= */
    public void eliminarPedido(Long id) {
        String sql = "DELETE FROM Pedidos WHERE ID_PEDIDO = ?";
        jdbcTemplate.update(sql, id);
    }
}
