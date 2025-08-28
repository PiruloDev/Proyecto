package com.example.demoJava1.Ordenes_salida.Service;

import com.example.demoJava1.Ordenes_salida.Ordenes_salida;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import java.util.List;

@Service
public class Ordenes_salidaService {

    private final JdbcTemplate jdbcTemplate;

    public Ordenes_salidaService(JdbcTemplate jdbcTemplate) {
        this.jdbcTemplate = jdbcTemplate;
    }

    public List<Ordenes_salida> obtenerOrdenesSalida() {
        String sql = "SELECT ID_FACTURA, ID_CLIENTE, ID_PEDIDO, FECHA_FACTURACION, TOTAL_FACTURA FROM ordenes_salida";

        return jdbcTemplate.query(sql, (rs, rowNum) ->
                new Ordenes_salida(
                        rs.getInt("ID_FACTURA"),
                        rs.getString("ID_CLIENTE"),
                        rs.getString("ID_PEDIDO"),
                        rs.getDate("FECHA_FACTURACION").toLocalDate(),
                        rs.getDouble("TOTAL_FACTURA")
                )
        );
    }
}
