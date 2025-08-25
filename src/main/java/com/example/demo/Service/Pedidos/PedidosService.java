package com.example.demo.Service.Pedidos;

import com.example.demo.Pedidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class PedidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // GET → obtener todos los pedidos
    public List<Pedidos> obtenerTodosLosPedidos() {
        String sql = "SELECT * FROM Pedidos";

        return jdbcTemplate.query(sql, (rs, rowNum) -> mapRowToPedido(rs));
    }

    // POST → crear un nuevo pedido
    public void crearPedido(Pedidos pedido) {
        String sql = "INSERT INTO Pedidos (ID_CLIENTE, ID_EMPLEADO, ID_ESTADO_PEDIDO, FECHA_INGRESO, FECHA_ENTREGA, TOTAL_PRODUCTO) " +
                "VALUES (?, ?, ?, ?, ?, ?)";

        jdbcTemplate.update(sql,
                pedido.getIdCliente(),
                pedido.getIdEmpleado(),
                pedido.getIdEstadoPedido(),
                pedido.getFechaIngreso(),
                pedido.getFechaEntrega(),
                pedido.getTotalProducto()
        );
    }

    // Mapper auxiliar
    private Pedidos mapRowToPedido(ResultSet rs) throws SQLException {
        Pedidos pedido = new Pedidos();
        pedido.setIdPedido(rs.getInt("ID_PEDIDO"));
        pedido.setIdCliente(rs.getInt("ID_CLIENTE"));
        pedido.setIdEmpleado(rs.getInt("ID_EMPLEADO"));
        pedido.setIdEstadoPedido(rs.getInt("ID_ESTADO_PEDIDO"));
        pedido.setFechaIngreso(rs.getTimestamp("FECHA_INGRESO"));
        pedido.setFechaEntrega(rs.getTimestamp("FECHA_ENTREGA"));
        pedido.setTotalProducto(rs.getDouble("TOTAL_PRODUCTO"));
        return pedido;
    }

    // PATCH → actualizar parcialmente un pedido
    public boolean actualizarParcialPedido(int idPedido, Pedidos cambios) {
        StringBuilder sql = new StringBuilder("UPDATE Pedidos SET ");
        boolean first = true;

        // Armamos dinámicamente según los campos que no vengan nulos
        if (cambios.getFechaEntrega() != null) {
            sql.append("FECHA_ENTREGA = ?");
            first = false;
        }
        if (cambios.getTotalProducto() > 0) {
            if (!first) sql.append(", ");
            sql.append("TOTAL_PRODUCTO = ?");
            first = false;
        }
        if (cambios.getIdEstadoPedido() > 0) {
            if (!first) sql.append(", ");
            sql.append("ID_ESTADO_PEDIDO = ?");
        }

        sql.append(" WHERE ID_PEDIDO = ?");

        // Armamos los valores dinámicos
        java.util.List<Object> params = new java.util.ArrayList<>();
        if (cambios.getFechaEntrega() != null) params.add(cambios.getFechaEntrega());
        if (cambios.getTotalProducto() > 0) params.add(cambios.getTotalProducto());
        if (cambios.getIdEstadoPedido() > 0) params.add(cambios.getIdEstadoPedido());

        params.add(idPedido); // condición WHERE

        int filas = jdbcTemplate.update(sql.toString(), params.toArray());
        return filas > 0;
    }

}
