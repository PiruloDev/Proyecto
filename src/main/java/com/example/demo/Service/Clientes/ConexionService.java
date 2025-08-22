package com.example.demo.Service.Clientes;

import com.example.demo.Clientes;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;
import org.springframework.jdbc.core.RowMapper;

@Service
public class ConexionService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Método para obtener una lista de nombres de clientes GET
    public List<String> obtenerUsuarios() {
        String sql = "SELECT NOMBRE_CLI FROM Clientes";
        return jdbcTemplate.query(sql, new RowMapper<String>() {
            @Override
            public String mapRow(ResultSet rs, int rowNum) throws SQLException {
                return rs.getString("NOMBRE_CLI");
            }
        });
    }

    // Método para crear un nuevo usuario POST
    public void crearUsuario(Clientes cliente) {
        String sql = "INSERT INTO Clientes (ID_CLIENTE, NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI) VALUES (?, ?, ?, ?)"; //Crear un nuevo registro con los datos proporcionados
        jdbcTemplate.update(sql,
                cliente.getID_CLIENTE(),
                cliente.getNOMBRE_CLI(),
                cliente.getTELEFONO_CLI(),
                cliente.getEMAIL_CLI()
        );
    }

    // Método para actualizar un usuario PUT
    public Clientes actualizarUsuario(String ID_CLIENTE, Clientes clienteActualizado) {
        // 1. Sentencia SQL con la clausula WHERE
        String sql = "UPDATE Clientes SET NOMBRE_CLI=?, TELEFONO_CLI=?, EMAIL_CLI=? WHERE ID_CLIENTE=?";

        // 2. Ejecuta la actualización en la base de datos
        int filasAfectadas = jdbcTemplate.update(sql,
                clienteActualizado.getNOMBRE_CLI(),
                clienteActualizado.getTELEFONO_CLI(),
                clienteActualizado.getEMAIL_CLI(),
                ID_CLIENTE // <-- Aquí se pasa el ID para el WHERE
        );

        // 3. Valida si la actualización fue exitosa
        if (filasAfectadas == 0) {
            // No se encontró y actualizó ninguna fila, devuelve null para manejar el error
            return null;
        }

        // 4. Devuelve el objeto cliente actualizado
        return clienteActualizado;
    }

}