// Archivo: PedidosProveedoresService.java
package com.example.Proyecto.service.PedidosProveedores;

import com.example.Proyecto.service.PedidosProveedores.PedidosProveedores;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class PedidosProveedoresService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Archivo: PedidosProveedoresService.java

// ...

    // Obtener todos los pedidos de proveedores
    public List<PedidosProveedores> obtenerTodosLosPedidosProveedores() {
        String sql = "SELECT * FROM Pedidos_Proveedores";
        return jdbcTemplate.query(sql, new RowMapper<PedidosProveedores>() {
            @Override
            public PedidosProveedores mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                PedidosProveedores pedido = new PedidosProveedores();
                pedido.setIdPedidoProv(rs.getInt("ID_PEDIDO_PROV"));
                pedido.setIdProveedor(rs.getInt("ID_PROVEEDOR"));
                pedido.setNumeroPedido(rs.getInt("NUMERO_PEDIDO")); // <-- CORRECCIÓN: Usar getInt
                pedido.setFechaPedido(rs.getDate("FECHA_PEDIDO"));
                pedido.setEstadoPedido(rs.getString("ESTADO_PEDIDO"));
                return pedido;
            }
        });
    }

    // Crear un nuevo pedido de proveedor (POST)
    public void crearPedidoProveedor(PedidosProveedores pedido) {
        String sql = "INSERT INTO Pedidos_Proveedores (ID_PROVEEDOR, NUMERO_PEDIDO, FECHA_PEDIDO, ESTADO_PEDIDO) VALUES (?, ?, ?, ?)";
        jdbcTemplate.update(sql,
                pedido.getIdProveedor(),
                pedido.getNumeroPedido(),
                pedido.getFechaPedido(),
                pedido.getEstadoPedido()
        );
    }

    // Actualizar un pedido de proveedor (PUT)
    public int editarPedidoProveedor(PedidosProveedores pedido) {
        String sql = "UPDATE Pedidos_Proveedores SET ID_PROVEEDOR=?, NUMERO_PEDIDO=?, FECHA_PEDIDO=?, ESTADO_PEDIDO=? WHERE ID_PEDIDO_PROV=?";
        return jdbcTemplate.update(sql,
                pedido.getIdProveedor(),
                pedido.getNumeroPedido(),
                pedido.getFechaPedido(),
                pedido.getEstadoPedido(),
                pedido.getIdPedidoProv()
        );
    }

    // Eliminar un pedido de proveedor (DELETE)
    public int eliminarPedidoProveedor(int idPedidoProv) {
        String sql = "DELETE FROM Pedidos_Proveedores WHERE ID_PEDIDO_PROV = ?";
        return jdbcTemplate.update(sql, idPedidoProv);
    }
}