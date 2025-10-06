package com.example.Proyecto.service.ProductosMasVendidos;

import com.example.Proyecto.model.ProductosMasVendidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class ProductosMasVendidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<ProductosMasVendidos> obtenerProductosMasVendidos(int limite) {
        String sql = """
            SELECT 
                p.ID_PRODUCTO,
                p.NOMBRE_PRODUCTO,
                p.DESCRIPCION_PRODUCTO,
                p.PRECIO_PRODUCTO,
                p.PRODUCTO_STOCK_MIN,
                0 as cantidad_vendida
            FROM productos p
            ORDER BY p.PRECIO_PRODUCTO DESC, p.NOMBRE_PRODUCTO ASC
            LIMIT ?
        """;

        return jdbcTemplate.query(sql, new ProductosMasVendidosRowMapper(), limite);
    }

    // RowMapper para mapear los resultados
    private static class ProductosMasVendidosRowMapper implements RowMapper<ProductosMasVendidos> {
        @Override
        public ProductosMasVendidos mapRow(ResultSet rs, int rowNum) throws SQLException {
            return new ProductosMasVendidos(
                    rs.getLong("ID_PRODUCTO"),
                    rs.getString("NOMBRE_PRODUCTO"),
                    rs.getString("DESCRIPCION_PRODUCTO"),
                    rs.getBigDecimal("PRECIO_PRODUCTO"),
                    rs.getInt("PRODUCTO_STOCK_MIN"),
                    rs.getLong("cantidad_vendida")
            );
        }
    }
}


