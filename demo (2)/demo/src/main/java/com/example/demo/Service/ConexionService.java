package com.example.demo.Service;

import com.example.demo.Model.Clientes;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class ConexionService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Método para obtener lista clientes GET
    public List<Clientes> obtenerUsuarios() {
        String sql = "SELECT * FROM Clientes";
        return jdbcTemplate.query(sql, new RowMapper<Clientes>() {
            @Override
            public Clientes mapRow(ResultSet rs, int rowNum) throws SQLException {
                Clientes cliente = new Clientes();
                cliente.setID_CLIENTE(rs.getString("ID_CLIENTE"));
                cliente.setNOMBRE_CLI(rs.getString("NOMBRE_CLI"));
                cliente.setTELEFONO_CLI(rs.getString("TELEFONO_CLI"));
                cliente.setEMAIL_CLI(rs.getString("EMAIL_CLI"));
                return cliente;
            }
        });
    }

    // Método para crear un nuevo usuario POST
    public void crearUsuario(Clientes cliente) {
        String sql = "INSERT INTO Clientes (ID_CLIENTE, NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI) VALUES (?, ?, ?, ?)"; //Crear un nuevo registro con los datos que se le pasen
        jdbcTemplate.update(sql,
                cliente.getID_CLIENTE(),
                cliente.getNOMBRE_CLI(),
                cliente.getTELEFONO_CLI(),
                cliente.getEMAIL_CLI()
        );
    }

    // Método para actualizar un usuario PUT
    public Clientes actualizarUsuario(String ID_CLIENTE, Clientes clienteActualizado) {

        //  Busca si el cliente existe en la base de datos
        String sqlSelect = "SELECT * FROM Clientes WHERE ID_CLIENTE = ?";
        List<Clientes> clientesEncontrados = jdbcTemplate.query(sqlSelect, new Object[]{ID_CLIENTE}, (rs, rowNum) -> {
            Clientes cliente = new Clientes();
            cliente.setID_CLIENTE(rs.getString("ID_CLIENTE"));
            cliente.setNOMBRE_CLI(rs.getString("NOMBRE_CLI"));
            cliente.setTELEFONO_CLI(rs.getString("TELEFONO_CLI"));
            cliente.setEMAIL_CLI(rs.getString("EMAIL_CLI"));
            return cliente;
        });

        // Si la lista está vacía, el cliente no existe
        if (clientesEncontrados.isEmpty()) {
            return null;
        }
        // Si el cliente existe, realiza la actualización
        String sqlUpdate = "UPDATE Clientes SET NOMBRE_CLI=?, TELEFONO_CLI=?, EMAIL_CLI=? WHERE ID_CLIENTE=?";

        jdbcTemplate.update(sqlUpdate,
                clienteActualizado.getNOMBRE_CLI(),
                clienteActualizado.getTELEFONO_CLI(),
                clienteActualizado.getEMAIL_CLI(),
                ID_CLIENTE);

        return clienteActualizado;
    }

    public int elimiarUsuario(String ID_CLIENTE) {
        String sql = "DELETE FROM Clientes WHERE ID_CLIENTE = ?";
        return jdbcTemplate.update(sql, ID_CLIENTE);
    }
}
