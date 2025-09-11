package com.example.demoJava1.Clientes.Service;

import com.example.demoJava1.Clientes.clientes;
import com.example.demoJava1.Productos.Services.Controllers.PojoProductos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class ConexionService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Devuelve solo nombres
    public List<String> obtenerClientes() {
        String sql = "SELECT NOMBRE_CLI FROM clientes";
        return jdbcTemplate.query(sql, (rs, rowNum) ->
                rs.getString("NOMBRE_CLI")
        );
    }

    // Devuelve ID, Nombre, Telefono, Email
    public List<clientes> obtenerDetalles() {
        String sql = "SELECT ID_CLIENTE, NOMBRE_CLI, TELEFONO_CLI, EMAIL_CLI FROM clientes";
        return jdbcTemplate.query(sql, (rs, rowNum) ->
                new clientes(
                        rs.getInt("ID_CLIENTE"),
                        rs.getString("NOMBRE_CLI"),
                        rs.getString("TELEFONO_CLI"),
                        rs.getString("EMAIL_CLI")
                )
        );
    }

    public int actualizarCliente(clientes clientes) {
        String sql = "UPDATE clientes SET NOMBRE_CLI = ?, TELEFONO_CLI = ?, EMAIL_CLI = ? WHERE ID_CLIENTE = ?";

        try {
            int result = jdbcTemplate.update(sql,
                    clientes.getNombre(),
                    clientes.getTelefono(),
                    clientes.getEmail(),
                    clientes.getId());

            return result > 0 ? 1 : 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return 0;
        }
    }

    public int eliminarCliente(int id) {
        String sql = "DELETE FROM clientes WHERE ID_CLIENTE = ?";
        return jdbcTemplate.update(sql, id);
    }
}
