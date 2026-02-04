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
       VALIDAR EXISTENCIA (Asegúrate que la tabla sea 'clientes')
       ===================================================== */
    private boolean clienteExiste(long idCliente) {
        try {
            String sql = "SELECT COUNT(*) FROM clientes WHERE ID_CLIENTE = ?";
            Integer count = jdbcTemplate.queryForObject(sql, Integer.class, idCliente);
            return count != null && count > 0;
        } catch (Exception e) {
            return false;
        }
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

        // Estos son los nombres que ya logramos que funcionaran
        pedido.setNombreCliente(rs.getString("CLI_NOMBRE"));
        pedido.setNombreEmpleado(rs.getString("EMP_NOMBRE"));
        pedido.setNombreEstado(rs.getString("EST_NOMBRE"));

        Timestamp tsIngreso = rs.getTimestamp("FECHA_INGRESO");
        pedido.setFECHA_INGRESO(tsIngreso != null ? tsIngreso.toLocalDateTime() : null);
        Timestamp tsEntrega = rs.getTimestamp("FECHA_ENTREGA");
        pedido.setFECHA_ENTREGA(tsEntrega != null ? tsEntrega.toLocalDateTime() : null);

        pedido.setTOTAL_PRODUCTO(rs.getBigDecimal("TOTAL_PRODUCTO"));
        return pedido;
    };

    /* =========================
       SQL DE CONSULTA
       ========================= */
    private final String SQL_SELECT_CON_NOMBRES =
            "SELECT p.*, c.NOMBRE_CLI AS CLI_NOMBRE, e.NOMBRE_EMPLEADO AS EMP_NOMBRE, s.NOMBRE_ESTADO AS EST_NOMBRE " +
                    "FROM pedidos p " +
                    "LEFT JOIN clientes c ON p.ID_CLIENTE = c.ID_CLIENTE " +
                    "LEFT JOIN empleados e ON p.ID_EMPLEADO = e.ID_EMPLEADO " +
                    "LEFT JOIN estado_pedidos s ON p.ID_ESTADO_PEDIDO = s.ID_ESTADO_PEDIDO ";

    public List<Pedidos> obtenerPedidos() {
        return jdbcTemplate.query(SQL_SELECT_CON_NOMBRES + " ORDER BY p.ID_PEDIDO DESC", pedidoRowMapper);
    }

    public Pedidos obtenerPedidoPorId(Long id) {
        try {
            return jdbcTemplate.queryForObject(SQL_SELECT_CON_NOMBRES + " WHERE p.ID_PEDIDO = ?", pedidoRowMapper, id);
        } catch (EmptyResultDataAccessException e) {
            return null;
        }
    }

    public List<Pedidos> obtenerPedidosPorCliente(long clienteId) {
        return jdbcTemplate.query(SQL_SELECT_CON_NOMBRES + " WHERE p.ID_CLIENTE = ?", pedidoRowMapper, clienteId);
    }

    /* =========================
       CREAR PEDIDO (REVISADO)
       ========================= */
    public int crearPedido(Pedidos pedido) {
        // Si la validación falla, revisa si el ID del cliente que envías desde Laravel existe en la DB
        if (!clienteExiste(pedido.getID_CLIENTE())) {
            throw new RuntimeException("Error: El cliente con ID " + pedido.getID_CLIENTE() + " no existe.");
        }

        // Usamos minúsculas 'pedidos' para evitar errores de case-sensitivity en Linux/Windows
        String sql = "INSERT INTO pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) " +
                "VALUES (?, ?, ?, ?, ?, ?)";

        KeyHolder keyHolder = new GeneratedKeyHolder();

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            ps.setLong(1, pedido.getID_CLIENTE());
            ps.setLong(2, pedido.getID_EMPLEADO());
            ps.setLong(3, pedido.getID_ESTADO_PEDIDO());

            // Fecha de ingreso automática si viene nula
            Timestamp fechaIn = (pedido.getFECHA_INGRESO() != null)
                    ? Timestamp.valueOf(pedido.getFECHA_INGRESO())
                    : new Timestamp(System.currentTimeMillis());
            ps.setTimestamp(4, fechaIn);

            if (pedido.getFECHA_ENTREGA() == null) {
                ps.setNull(5, Types.TIMESTAMP);
            } else {
                ps.setTimestamp(5, Timestamp.valueOf(pedido.getFECHA_ENTREGA()));
            }

            ps.setBigDecimal(6, pedido.getTOTAL_PRODUCTO());
            return ps;
        }, keyHolder);

        return (keyHolder.getKey() != null) ? keyHolder.getKey().intValue() : 0;
    }

    /* =========================
       ACTUALIZAR Y ELIMINAR
       ========================= */
    public void actualizarPedido(Long id, Pedidos nuevosDatos) {
        String sql = "UPDATE pedidos SET ID_CLIENTE = ?, ID_EMPLEADO = ?, ID_ESTADO_PEDIDO = ?, FECHA_ENTREGA = ?, TOTAL_PRODUCTO = ? WHERE ID_PEDIDO = ?";
        jdbcTemplate.update(sql, nuevosDatos.getID_CLIENTE(), nuevosDatos.getID_EMPLEADO(), nuevosDatos.getID_ESTADO_PEDIDO(),
                nuevosDatos.getFECHA_ENTREGA() != null ? Timestamp.valueOf(nuevosDatos.getFECHA_ENTREGA()) : null,
                nuevosDatos.getTOTAL_PRODUCTO(), id);
    }

    public void eliminarPedido(Long id) {
        jdbcTemplate.update("DELETE FROM pedidos WHERE ID_PEDIDO = ?", id);
    }
}