package com.example.demo.Service.Productos;

import com.example.demo.Productos;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import org.springframework.jdbc.core.RowMapper;

@Service
public class ProductosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Método para obtener lista de nombres de productos (solo nombres)
    public List<String> obtenerProductos() {
        String sql = "SELECT NOMBRE_PRODUCTO FROM Productos";
        return jdbcTemplate.query(sql, new RowMapper<String>() {
            @Override
            public String mapRow(ResultSet rs, int rowNum) throws SQLException {
                return rs.getString("NOMBRE_PRODUCTO");
            }
        });
    }

    // Método para obtener todos los productos con información completa
    public List<Productos> obtenerTodosLosProductos() {
        String sql = "SELECT * FROM Productos";
        return jdbcTemplate.query(sql, new RowMapper<Productos>() {
            @Override
            public Productos mapRow(ResultSet rs, int rowNum) throws SQLException {
                Productos producto = new Productos();
                producto.setIdProducto(rs.getInt("ID_PRODUCTO"));
                producto.setIdAdmin(rs.getInt("ID_ADMIN"));
                producto.setIdCategoriaProducto(rs.getInt("ID_CATEGORIA_PRODUCTO"));
                producto.setNombreProducto(rs.getString("NOMBRE_PRODUCTO"));
                producto.setDescripcionProducto(rs.getString("DESCRIPCION_PRODUCTO"));
                producto.setProductoStockMin(rs.getInt("PRODUCTO_STOCK_MIN"));
                producto.setPrecioProducto(rs.getDouble("PRECIO_PRODUCTO"));
                producto.setFechaVencimientoProducto(rs.getDate("FECHA_VENCIMIENTO_PRODUCTO"));
                producto.setFechaIngresoProducto(rs.getDate("FECHA_INGRESO_PRODUCTO"));
                producto.setTipoProductoMarca(rs.getString("TIPO_PRODUCTO_MARCA"));
                producto.setActivo(rs.getBoolean("ACTIVO"));
                producto.setFechaUltimaModificacion(rs.getDate("FECHA_ULTIMA_MODIFICACION"));
                return producto;
            }
        });
    }

    // Método para crear un nuevo producto
    public void crearProducto(Productos producto) {
        String sql = "INSERT INTO Productos (ID_ADMIN, ID_CATEGORIA_PRODUCTO, NOMBRE_PRODUCTO, DESCRIPCION_PRODUCTO, PRODUCTO_STOCK_MIN, PRECIO_PRODUCTO, FECHA_VENCIMIENTO_PRODUCTO, FECHA_INGRESO_PRODUCTO, TIPO_PRODUCTO_MARCA) " +
                "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        jdbcTemplate.update(sql,
                producto.getIdAdmin(),
                producto.getIdCategoriaProducto(),
                producto.getNombreProducto(),
                producto.getDescripcionProducto(),
                producto.getProductoStockMin(),
                producto.getPrecioProducto(),
                producto.getFechaVencimientoProducto(),
                producto.getFechaIngresoProducto(),
                producto.getTipoProductoMarca()
        );
    }

    // Método para eliminar un producto por ID
    public boolean eliminarProducto(int idProducto) {
        String sql = "DELETE FROM Productos WHERE ID_PRODUCTO = ?";
        int filasAfectadas = jdbcTemplate.update(sql, idProducto);
        return filasAfectadas > 0; // true si se eliminó, false si no existía
    }
}
