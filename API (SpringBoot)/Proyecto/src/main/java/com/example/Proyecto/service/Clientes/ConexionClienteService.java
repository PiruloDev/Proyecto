package com.example.Proyecto.service.Clientes;

import com.example.Proyecto.model.PojoCliente;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.lang.NonNull;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service
public class ConexionClienteService {
    @Autowired
    private JdbcTemplate jdbcTemplate;
    @Autowired
    private PasswordEncoder passwordEncoder;

    public List<Map<String, Object>> obtenerDetallesCliente() {
        String sql = "SELECT TELEFONO_CLI, EMAIL_CLI, NOMBRE_CLI FROM Clientes";
        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> cliente = new HashMap<>();
                cliente.put("Nombre:", rs.getString("NOMBRE_CLI"));
                cliente.put("Correo Electronico:", rs.getString("EMAIL_CLI"));
                cliente.put("Telefono:", rs.getString("TELEFONO_CLI"));
                return cliente;
            }
        });
    }

    public boolean crearCliente(PojoCliente pojoCliente) {
        String sql = "INSERT INTO Clientes (NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI, CONTRASENA_CLI) VALUES (?, ?, ?, ?)";
        try {
            if (pojoCliente.getContrasena() == null || pojoCliente.getContrasena().trim().isEmpty()) {
                System.out.println("Error: La contraseña no puede ser nula o vacía");
                return false;
            }
            String contrasenaHasheada = passwordEncoder.encode(pojoCliente.getContrasena());
            int result = jdbcTemplate.update(sql, pojoCliente.getNombre(), pojoCliente.getEmail(), pojoCliente.getTelefono(), contrasenaHasheada);
            return result > 0;
        } catch (DataAccessException e) {
            return false;
        }
    }

    public boolean actualizarCliente(int id, Map<String, Object> campos) {
        if (campos.isEmpty()) {
            return false;
        }
        StringBuilder sql = new StringBuilder("UPDATE Clientes SET ");
        List<Object> parametros = new ArrayList<>();
        boolean primero = true;
        if (campos.containsKey("nombre")) {
            if (!primero) sql.append(", ");
            sql.append("NOMBRE_CLI = ?");
            parametros.add(campos.get("nombre"));
            primero = false;
        }
        if (campos.containsKey("email")) {
            if (!primero) sql.append(", ");
            sql.append("EMAIL_CLI = ?");
            parametros.add(campos.get("email"));
            primero = false;
        }
        if (campos.containsKey("telefono")) {
            if (!primero) sql.append(", ");
            sql.append("TELEFONO_CLI = ?");
            parametros.add(campos.get("telefono"));
            primero = false;
        }
        if (campos.containsKey("contrasena")) {
            if (!primero) sql.append(", ");
            sql.append("CONTRASENA_CLI = ?");
            String contrasenaHasheada = passwordEncoder.encode((String) campos.get("contrasena"));
            parametros.add(contrasenaHasheada);
            primero = false;
        }
        sql.append(" WHERE ID_CLIENTE = ?");
        parametros.add(id);
        try {
            int value = jdbcTemplate.update(sql.toString(), parametros.toArray());
            return value > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
}



