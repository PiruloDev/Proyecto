
package com.example.Proyecto.service.PedidosProveedores;

import com.example.Proyecto.model.Pedidos_Proveedores;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class Pedidos_ProveedoresService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Método para obtener todos los pedidos de proveedores (GET)
    public List<Pedidos_Proveedores> obtenerPedidosProveedores() {
        String sql = "SELECT * FROM Pedidos_Proveedores";

        return jdbcTemplate.query(sql, new RowMapper<Pedidos_Proveedores>() {
            @Override
            public Pedidos_Proveedores mapRow(ResultSet rs, int rowNum) throws SQLException {
                Pedidos_Proveedores pedido = new Pedidos_Proveedores();
                // Mapeamos todas las columnas de la tabla.
                // Es crucial que los nombres de las columnas coincidan con la base de datos.
                pedido.setID_PEDIDO_PROV(rs.getInt("ID_PEDIDO_PROV"));
                pedido.setID_PROVEEDOR(rs.getLong("ID_PROVEEDOR"));
                pedido.setNUMERO_PEDIDO(rs.getLong("NUMERO_PEDIDO"));
                pedido.setFECHA_PEDIDO(rs.getDate("FECHA_PEDIDO"));
                pedido.setESTADO_PEDIDO(rs.getString("ESTADO_PEDIDO"));
                return pedido;
            }
        });
    }

    // Método para crear un nuevo pedido de proveedor (POST)
    // Se insertan los datos del nuevo pedido.
    public void crearPedidoProveedor(Pedidos_Proveedores pedidosProveedores) {
        String sql = "INSERT INTO Pedidos_Proveedores (ID_PROVEEDOR, NUMERO_PEDIDO, FECHA_PEDIDO, ESTADO_PEDIDO) VALUES (?, ?, ?, ?)";
        jdbcTemplate.update(sql, pedidosProveedores.getID_PROVEEDOR(), pedidosProveedores.getNUMERO_PEDIDO(), pedidosProveedores.getFECHA_PEDIDO(), pedidosProveedores.getESTADO_PEDIDO());
    }

    // Método para actualizar un pedido de proveedor (PUT)
    // Se usa el ID_PEDIDO_PROV como clave para la actualización.
    public void actualizarPedidosProveedores(int id, Pedidos_Proveedores pedidosProveedores) {
        String sql = "UPDATE Pedidos_Proveedores SET ID_PROVEEDOR = ?, NUMERO_PEDIDO = ?, FECHA_PEDIDO = ?, ESTADO_PEDIDO = ? WHERE ID_PEDIDO_PROV = ?";
        jdbcTemplate.update(sql, pedidosProveedores.getID_PROVEEDOR(), pedidosProveedores.getNUMERO_PEDIDO(), pedidosProveedores.getFECHA_PEDIDO(), pedidosProveedores.getESTADO_PEDIDO(), id);
    }

    // Método para eliminar un pedido de proveedor por ID (DELETE)
    // Se usa el ID_PEDIDO_PROV como clave para la eliminación.
    public void eliminarPedidosProveedores(int id) {
        String sql = "DELETE FROM Pedidos_Proveedores WHERE ID_PEDIDO_PROV = ?";
        jdbcTemplate.update(sql, id);
    }
}