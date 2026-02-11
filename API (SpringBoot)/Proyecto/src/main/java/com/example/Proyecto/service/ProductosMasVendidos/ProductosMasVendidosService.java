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
                cp.NOMBRE_CATEGORIAPRODUCTO AS CATEGORIA_PRODUCTO,
                p.DESCRIPCION_PRODUCTO,
                p.PRECIO_PRODUCTO,
                p.PRODUCTO_STOCK_MIN,
                COALESCE(SUM(dp.CANTIDAD_PRODUCTO), 0) AS cantidad_vendida
            FROM productos p
            LEFT JOIN detalle_pedidos dp ON p.ID_PRODUCTO = dp.ID_PRODUCTO
            LEFT JOIN categoria_productos cp ON p.ID_CATEGORIA_PRODUCTO = cp.ID_CATEGORIA_PRODUCTO
            WHERE p.ACTIVO = 1
            GROUP BY p.ID_PRODUCTO, p.NOMBRE_PRODUCTO, cp.NOMBRE_CATEGORIAPRODUCTO,
                     p.DESCRIPCION_PRODUCTO, p.PRECIO_PRODUCTO, p.PRODUCTO_STOCK_MIN
            ORDER BY cantidad_vendida DESC, p.NOMBRE_PRODUCTO ASC
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
                    rs.getString("CATEGORIA_PRODUCTO"),
                    rs.getString("DESCRIPCION_PRODUCTO"),
                    rs.getBigDecimal("PRECIO_PRODUCTO"),
                    rs.getInt("PRODUCTO_STOCK_MIN"),
                    rs.getLong("cantidad_vendida")
            );
        }
    }
}


