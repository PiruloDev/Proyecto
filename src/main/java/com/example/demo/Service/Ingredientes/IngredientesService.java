package com.example.demo.Service.Ingredientes;

import com.example.demo.Ingredientes;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import org.springframework.jdbc.core.RowMapper;

@Service
public class IngredientesService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Método para obtener lista de nombres de ingredientes
    public List<String> obtenerIngredientes() {
        String sql = "SELECT NOMBRE_INGREDIENTE FROM Ingredientes";
        return jdbcTemplate.query(sql, new RowMapper<String>() {
            @Override
            public String mapRow(ResultSet rs, int rowNum) throws SQLException {
                return rs.getString("NOMBRE_INGREDIENTE");
            }
        });
    }

    // Método para obtener todos los ingredientes
    public List<Ingredientes> obtenerTodosLosIngredientes() {
        String sql = "SELECT * FROM Ingredientes";

        return jdbcTemplate.query(sql, new RowMapper<Ingredientes>() {
            @Override
            public Ingredientes mapRow(ResultSet rs, int rowNum) throws SQLException {
                Ingredientes ingrediente = new Ingredientes();
                ingrediente.setIdIngrediente(rs.getInt("ID_INGREDIENTE"));
                ingrediente.setIdProveedor(rs.getInt("ID_PROVEEDOR"));
                ingrediente.setIdCategoria(rs.getInt("ID_CATEGORIA"));
                ingrediente.setNombreIngrediente(rs.getString("NOMBRE_INGREDIENTE"));
                ingrediente.setCantidadIngrediente(rs.getInt("CANTIDAD_INGREDIENTE"));
                ingrediente.setFechaVencimiento(rs.getDate("FECHA_VENCIMIENTO"));
                ingrediente.setReferenciaIngrediente(rs.getString("REFERENCIA_INGREDIENTE"));
                ingrediente.setFechaEntregaIngrediente(rs.getDate("FECHA_ENTREGA_INGREDIENTE"));
                return ingrediente;
            }
        });
    }


    // Método para crear un nuevo ingrediente (POST)
    public void crearIngrediente(Ingredientes ingrediente) {
        String sql = "INSERT INTO Ingredientes (ID_PROVEEDOR, ID_CATEGORIA, NOMBRE_INGREDIENTE, CANTIDAD_INGREDIENTE, FECHA_VENCIMIENTO, REFERENCIA_INGREDIENTE, FECHA_ENTREGA_INGREDIENTE) " +
                "VALUES (?, ?, ?, ?, ?, ?, ?)";
        jdbcTemplate.update(sql,
                ingrediente.getIdProveedor(),           // ID_PROVEEDOR
                ingrediente.getIdCategoria(),           // ID_CATEGORIA
                ingrediente.getNombreIngrediente(),     // NOMBRE_INGREDIENTE
                ingrediente.getCantidadIngrediente(),   // CANTIDAD_INGREDIENTE
                ingrediente.getFechaVencimiento(),      // FECHA_VENCIMIENTO
                ingrediente.getReferenciaIngrediente(), // REFERENCIA_INGREDIENTE
                ingrediente.getFechaEntregaIngrediente()// FECHA_ENTREGA_INGREDIENTE
        );
    }

}
