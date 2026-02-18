package com.example.Proyecto.service.DetallePedidos;

import com.example.Proyecto.service.DetallePedidos.DetallePedidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class DetallePedidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Obtener todos los detalles de pedidos
    public List<DetallePedidos> obtenerTodosLosDetallesPedidos() {
        String sql = "SELECT * FROM Detalle_Pedidos";
        return jdbcTemplate.query(sql, new RowMapper<DetallePedidos>() {
            @Override
            public DetallePedidos mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                DetallePedidos detalle = new DetallePedidos();
                detalle.setIdDetalle(rs.getInt("ID_DETALLE"));
                detalle.setIdPedido(rs.getInt("ID_PEDIDO"));
                detalle.setIdProducto(rs.getInt("ID_PRODUCTO"));
                detalle.setCantidadProducto(rs.getInt("CANTIDAD_PRODUCTO"));
                detalle.setPrecioUnitario(rs.getBigDecimal("PRECIO_UNITARIO"));
                detalle.setSubtotal(rs.getBigDecimal("SUBTOTAL"));
                return detalle;
            }
        });
    }

    // Crear un nuevo detalle de pedido (POST)
    public void crearDetallePedido(DetallePedidos detalle) {
        String sql = "INSERT INTO Detalle_Pedidos (ID_PEDIDO, ID_PRODUCTO, CANTIDAD_PRODUCTO, PRECIO_UNITARIO, SUBTOTAL) VALUES (?, ?, ?, ?, ?)";
        jdbcTemplate.update(sql,
                detalle.getIdPedido(),
                detalle.getIdProducto(),
                detalle.getCantidadProducto(),
                detalle.getPrecioUnitario(),
                detalle.getSubtotal()
        );
    }

    // Actualizar un detalle de pedido (PUT)
    public int editarDetallePedido(DetallePedidos detalle) {
        String sql = "UPDATE Detalle_Pedidos SET ID_PEDIDO=?, ID_PRODUCTO=?, CANTIDAD_PRODUCTO=?, PRECIO_UNITARIO=?, SUBTOTAL=? WHERE ID_DETALLE=?";
        return jdbcTemplate.update(sql,
                detalle.getIdPedido(),
                detalle.getIdProducto(),
                detalle.getCantidadProducto(),
                detalle.getPrecioUnitario(),
                detalle.getSubtotal(),
                detalle.getIdDetalle()
        );
    }

    // Eliminar un detalle de pedido (DELETE)
    public int eliminarDetallePedido(int idDetalle) {
        String sql = "DELETE FROM Detalle_Pedidos WHERE ID_DETALLE = ?";
        return jdbcTemplate.update(sql, idDetalle);
    }

    public List<DetallePedidos> obtenerDetallesPorPedido(int idPedido) {
        // Usamos el nombre exacto de la tabla de tu imagen: detalle_pedidos
        String sql = "SELECT d.*, p.NOMBRE_PRODUCTO " +
                "FROM detalle_pedidos d " +
                "INNER JOIN productos p ON d.ID_PRODUCTO = p.ID_PRODUCTO " +
                "WHERE d.ID_PEDIDO = ?";

        return jdbcTemplate.query(sql, (rs, rowNum) -> {
            DetallePedidos detalle = new DetallePedidos();
            // Ajustado a los nombres exactos de tu Pojo y tu imagen de BD
            detalle.setIdDetalle(rs.getInt("ID_DETALLE"));
            detalle.setIdPedido(rs.getInt("ID_PEDIDO"));
            detalle.setIdProducto(rs.getInt("ID_PRODUCTO"));
            detalle.setCantidadProducto(rs.getInt("CANTIDAD_PRODUCTO"));
            detalle.setPrecioUnitario(rs.getBigDecimal("PRECIO_UNITARIO"));
            detalle.setSubtotal(rs.getBigDecimal("SUBTOTAL"));

            // Esto llena el campo nuevo que creamos
            detalle.setNombreProducto(rs.getString("NOMBRE_PRODUCTO"));

            return detalle;
        }, idPedido);
    }
}