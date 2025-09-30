package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.model.Pedidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;

import java.sql.*;
import java.util.List;

@Service
public class pedidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Método para obtener todos los pedidos (GET)
    public List<Pedidos> obtenerPedidos() {
        String sql = "SELECT * FROM Pedidos";
        return jdbcTemplate.query(sql, new RowMapper<Pedidos>() {
            @Override
            public Pedidos mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                Pedidos pedido = new Pedidos();
                // Utiliza los métodos get correspondientes
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

    // Método para crear un nuevo pedido (POST)
    public int crearPedido(Pedidos pedido) {
        String sql = "INSERT INTO Pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) VALUES (?, ?, ?, ?, ?, ?)";

        KeyHolder keyHolder = new GeneratedKeyHolder();

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            ps.setLong(1, pedido.getID_CLIENTE());
            ps.setLong(2, pedido.getID_EMPLEADO());
            ps.setLong(3, pedido.getID_ESTADO_PEDIDO());
            ps.setDate(4, new java.sql.Date(pedido.getFECHA_INGRESO().getTime()));
            ps.setDate(5, new java.sql.Date(pedido.getFECHA_ENTREGA().getTime()));
            ps.setBigDecimal(6, pedido.getTOTAL_PRODUCTO());
            return ps;
        }, keyHolder);

        Number key = keyHolder.getKey();
        return key != null ? key.intValue() : -1;
    }
    // Método para actualizar un pedido (PUT)
    public void actualizarPedido(Long id, Pedidos pedido) {
        String sql = "UPDATE Pedidos SET ID_CLIENTE = ?, ID_EMPLEADO = ?, ID_ESTADO_PEDIDO = ?, FECHA_INGRESO = ?, FECHA_ENTREGA = ?, TOTAL_PRODUCTO = ? WHERE ID_PEDIDO = ?";

        jdbcTemplate.update(sql,
                pedido.getID_CLIENTE(),
                pedido.getID_EMPLEADO(),
                pedido.getID_ESTADO_PEDIDO(),
                new java.sql.Date(pedido.getFECHA_INGRESO().getTime()),
                new java.sql.Date(pedido.getFECHA_ENTREGA().getTime()),
                pedido.getTOTAL_PRODUCTO(),
                id
        );
    }
    // Método para eliminar un pedido (DELETE)
    public void eliminarPedido(Long id) {
        String sql = "DELETE FROM Pedidos WHERE ID_PEDIDO = ?";
        jdbcTemplate.update(sql, id);
    }
}
