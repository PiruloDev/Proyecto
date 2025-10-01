package com.example.Proyecto.service.Ordenes_salida;

import com.example.Proyecto.model.Ordenes_salida;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.sql.Timestamp;
import java.time.LocalDateTime;
import java.util.List;

@Service
public class Ordenes_salidaService {

    private final JdbcTemplate jdbcTemplate;

    public Ordenes_salidaService(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public List<Ordenes_salida> obtenerOrdenesSalida() {
        String sql = "SELECT ID_FACTURA, ID_CLIENTE, ID_PEDIDO, FECHA_FACTURACION, TOTAL_FACTURA FROM ordenes_salida";

        return jdbcTemplate.query(sql, (rs, rowNum) -> {

            java.sql.Timestamp timestamp = rs.getTimestamp("FECHA_FACTURACION");
            LocalDateTime fecha = (timestamp != null) ? timestamp.toLocalDateTime() : null;

            Ordenes_salida orden = new Ordenes_salida();
            orden.setIdFactura(rs.getInt("ID_FACTURA"));
            orden.setIdCliente(rs.getInt("ID_CLIENTE"));
            orden.setIdPedido(rs.getInt("ID_PEDIDO"));
            orden.setFechaFacturacion(fecha);
            orden.setTotalFactura(rs.getDouble("TOTAL_FACTURA"));

            return orden;
        });
    }


    public boolean agregarVenta(Ordenes_salida ordenesSalida) {
        String sql = "INSERT INTO ordenes_salida (ID_CLIENTE, ID_PEDIDO, FECHA_FACTURACION, TOTAL_FACTURA) VALUES (?, ?, ?, ?)";
        try {
            Timestamp fecha = (ordenesSalida.getFechaFacturacion() != null)
                    ? Timestamp.valueOf(ordenesSalida.getFechaFacturacion())
                    : null;

            int result = jdbcTemplate.update(sql,
                    ordenesSalida.getIdCliente(),
                    ordenesSalida.getIdPedido(),
                    fecha,
                    ordenesSalida.getTotalFactura()
            );
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }

    public int actualizarVenta(Ordenes_salida ordenesSalida) {
        String sql = "UPDATE ordenes_salida " +
                "SET ID_CLIENTE = ?, ID_PEDIDO = ?, FECHA_FACTURACION = ?, TOTAL_FACTURA = ? " +
                "WHERE ID_FACTURA = ?";

        try {
            int result = jdbcTemplate.update(sql,
                    ordenesSalida.getIdCliente(),
                    ordenesSalida.getIdPedido(),
                    ordenesSalida.getFechaFacturacion(),
                    ordenesSalida.getTotalFactura(),
                    ordenesSalida.getIdFactura());

            return result > 0 ? 1 : 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return 0;
        }
    }

    public int eliminarVenta(int id) {
        String sql = "DELETE FROM ordenes_salida WHERE ID_FACTURA = ?";
        return jdbcTemplate.update(sql, id);
    }
}
