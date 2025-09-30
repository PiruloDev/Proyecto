package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.dto.PedidoRequest;
import com.example.Proyecto.model.Pedidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.BatchPreparedStatementSetter;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.math.BigInteger;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Date;
import java.util.List;
import java.math.BigDecimal;

@Service
public class pedidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    @Transactional
    public Pedidos crearPedidoConDetalle(PedidoRequest request) {
        // 1. Validar Stock
        validarStockProductos(request.getDetalles());

        // 2. Insertar Cabecera con TOTAL = 0 (el trigger lo actualizará)
        Long idPedidoGenerado = insertarCabeceraPedido(request);

        // 3. Insertar Detalles (esto dispara el trigger que actualiza TOTAL_PRODUCTO)
        insertarDetallesPedido(idPedidoGenerado, request.getDetalles());

        // 4. Descontar Stock
        descontarStockProductos(request.getDetalles());

        // 5. Obtener el pedido actualizado por el trigger
        Pedidos pedidoFinal = obtenerPedidoPorId(idPedidoGenerado);

        return pedidoFinal;
    }

    private void validarStockProductos(List<PedidoRequest.DetallePedidoRequest> detalles) {
        String sql = "SELECT STOCK_ACTUAL FROM productos WHERE ID_PRODUCTO = ?";

        for (PedidoRequest.DetallePedidoRequest detalle : detalles) {
            Long idProducto = detalle.getIdProducto();
            Long stockActual;

            try {
                Integer stockInt = jdbcTemplate.queryForObject(sql, Integer.class, idProducto);
                stockActual = stockInt != null ? stockInt.longValue() : null;

            } catch (EmptyResultDataAccessException e) {
                throw new RuntimeException("Error de Negocio: El Producto ID " + idProducto + " no se encontró en la tabla 'productos'.", e);
            } catch (Exception e) {
                throw new RuntimeException("Error de Base de Datos al consultar stock del Producto ID " + idProducto + ". Causa: " + e.getMessage(), e);
            }

            if (stockActual == null || stockActual < detalle.getCantidad()) {
                throw new RuntimeException("Stock Insuficiente para el Producto ID " + idProducto +
                        ". Stock disponible: " + (stockActual != null ? stockActual : 0) +
                        ", Cantidad requerida: " + detalle.getCantidad());
            }
        }
    }

    private void descontarStockProductos(List<PedidoRequest.DetallePedidoRequest> detalles) {
        String sql = "UPDATE productos SET STOCK_ACTUAL = STOCK_ACTUAL - ? WHERE ID_PRODUCTO = ?";

        jdbcTemplate.batchUpdate(sql, new BatchPreparedStatementSetter() {
            @Override
            public void setValues(PreparedStatement ps, int i) throws SQLException {
                PedidoRequest.DetallePedidoRequest detalleRequest = detalles.get(i);
                ps.setInt(1, detalleRequest.getCantidad());
                ps.setLong(2, detalleRequest.getIdProducto());
            }

            @Override
            public int getBatchSize() {
                return detalles.size();
            }
        });
    }

    private Long insertarCabeceraPedido(PedidoRequest request) {
        // El trigger actualizará TOTAL_PRODUCTO, así que lo iniciamos en 0
        String sql = "INSERT INTO Pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) VALUES (?, ?, ?, ?, ?, ?)";
        KeyHolder keyHolder = new GeneratedKeyHolder();
        java.sql.Date fechaIngreso = new java.sql.Date(System.currentTimeMillis());

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            ps.setObject(1, request.getIdUsuario());
            ps.setObject(2, 1L);
            ps.setObject(3, 1L);
            ps.setDate(4, fechaIngreso);
            ps.setDate(5, null);
            ps.setBigDecimal(6, BigDecimal.ZERO); // Inicia en 0, el trigger lo actualiza
            return ps;
        }, keyHolder);

        Number key = keyHolder.getKey();
        Long idGenerado;

        if (key == null) {
            throw new RuntimeException("No se pudo obtener el ID_PEDIDO generado.");
        } else if (key instanceof java.math.BigInteger) {
            idGenerado = ((java.math.BigInteger) key).longValue();
        } else {
            idGenerado = key.longValue();
        }

        return idGenerado;
    }

    private void insertarDetallesPedido(Long idPedido, List<PedidoRequest.DetallePedidoRequest> detalles) {
        String sql = "INSERT INTO detalle_pedidos (ID_PEDIDO, ID_PRODUCTO, CANTIDAD_PRODUCTO, PRECIO_UNITARIO, SUBTOTAL) VALUES (?, ?, ?, ?, ?)";
        jdbcTemplate.batchUpdate(sql, new BatchPreparedStatementSetter() {
            @Override
            public void setValues(PreparedStatement ps, int i) throws SQLException {
                PedidoRequest.DetallePedidoRequest detalleRequest = detalles.get(i);
                ps.setLong(1, idPedido);
                ps.setObject(2, detalleRequest.getIdProducto());
                ps.setInt(3, detalleRequest.getCantidad());
                ps.setBigDecimal(4, detalleRequest.getPrecioUnitario());

                BigDecimal subtotal = detalleRequest.getPrecioUnitario()
                        .multiply(BigDecimal.valueOf(detalleRequest.getCantidad()));
                ps.setBigDecimal(5, subtotal);
            }
            @Override
            public int getBatchSize() {
                return detalles.size();
            }
        });
    }

    // Nuevo método para obtener el pedido con el total actualizado por el trigger
    private Pedidos obtenerPedidoPorId(Long idPedido) {
        String sql = "SELECT * FROM Pedidos WHERE ID_PEDIDO = ?";
        return jdbcTemplate.queryForObject(sql, new RowMapper<Pedidos>() {
            @Override
            public Pedidos mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                Pedidos pedido = new Pedidos();
                pedido.setID_PEDIDO(rs.getLong("ID_PEDIDO"));
                pedido.setID_CLIENTE(rs.getLong("ID_CLIENTE"));
                pedido.setID_EMPLEADO(rs.getLong("ID_EMPLEADO"));
                pedido.setID_ESTADO_PEDIDO(rs.getLong("ID_ESTADO_PEDIDO"));
                pedido.setFECHA_INGRESO(rs.getDate("FECHA_INGRESO"));
                pedido.setFECHA_ENTREGA(rs.getDate("FECHA_ENTREGA"));
                pedido.setTOTAL_PRODUCTO(rs.getBigDecimal("TOTAL_PRODUCTO"));
                return pedido;
            }
        }, idPedido);
    }

    public List<Pedidos> obtenerPedidos() {
        String sql = "SELECT * FROM Pedidos";
        return jdbcTemplate.query(sql, new RowMapper<Pedidos>() {
            @Override
            public Pedidos mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                Pedidos pedido = new Pedidos();
                pedido.setID_PEDIDO(rs.getLong("ID_PEDIDO"));
                pedido.setID_CLIENTE(rs.getLong("ID_CLIENTE"));
                pedido.setID_EMPLEADO(rs.getLong("ID_EMPLEADO"));
                pedido.setID_ESTADO_PEDIDO(rs.getLong("ID_ESTADO_PEDIDO"));
                pedido.setFECHA_INGRESO(rs.getDate("FECHA_INGRESO"));
                pedido.setFECHA_ENTREGA(rs.getDate("FECHA_ENTREGA"));
                pedido.setTOTAL_PRODUCTO(rs.getBigDecimal("TOTAL_PRODUCTO"));
                return pedido;
            }
        });
    }

    public Long crearPedido(Pedidos pedido) {
        String sql = "INSERT INTO Pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) VALUES (?, ?, ?, ?, ?, ?)";
        KeyHolder keyHolder = new GeneratedKeyHolder();
        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            ps.setObject(1, pedido.getID_CLIENTE());
            ps.setObject(2, pedido.getID_EMPLEADO());
            ps.setObject(3, pedido.getID_ESTADO_PEDIDO());
            ps.setDate(4, new java.sql.Date(pedido.getFECHA_INGRESO().getTime()));
            Date fechaEntrega = pedido.getFECHA_ENTREGA();
            ps.setDate(5, fechaEntrega != null ? new java.sql.Date(fechaEntrega.getTime()) : null);
            ps.setBigDecimal(6, pedido.getTOTAL_PRODUCTO());
            return ps;
        }, keyHolder);
        Number key = keyHolder.getKey();
        return key != null ? key.longValue() : -1L;
    }

    public void actualizarPedido(Long id, Pedidos pedido) {
        String sql = "UPDATE Pedidos SET ID_CLIENTE = ?, ID_EMPLEADO = ?, ID_ESTADO_PEDIDO = ?, FECHA_INGRESO = ?, FECHA_ENTREGA = ?, TOTAL_PRODUCTO = ? WHERE ID_PEDIDO = ?";
        jdbcTemplate.update(sql,
                pedido.getID_CLIENTE(),
                pedido.getID_EMPLEADO(),
                pedido.getID_ESTADO_PEDIDO(),
                new java.sql.Date(pedido.getFECHA_INGRESO().getTime()),
                pedido.getFECHA_ENTREGA() != null ? new java.sql.Date(pedido.getFECHA_ENTREGA().getTime()) : null,
                pedido.getTOTAL_PRODUCTO(),
                id
        );
    }

    public void eliminarPedido(Long id) {
        String sql = "DELETE FROM Pedidos WHERE ID_PEDIDO = ?";
        jdbcTemplate.update(sql, id);
    }
}