package com.example.Proyecto.Services.Empleados;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.jdbc.core.RowMapper;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

@Service

public class ConexionEmpleadoService {
    @Autowired
    private JdbcTemplate jdbcTemplate;
    @Autowired
    private PasswordEncoder passwordEncoder;

    public List<Map<String, Object>> obtenerDetallesEmpleado() {
        String sql = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO, EMAIL_EMPLEADO FROM Empleados";
        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> cliente = new HashMap<>();
                cliente.put("Id:", rs.getInt("ID_EMPLEADO"));
                cliente.put("Nombre:", rs.getString("NOMBRE_EMPLEADO"));
                cliente.put("Correo Electronico:", rs.getString("EMAIL_EMPLEADO"));
                return cliente;
            }
        });
    }

    public boolean crearEmpleado(PojoEmpleado pojoEmpleado) {
        String sql = "INSERT INTO Empleados (ID_EMPLEADO, NOMBRE_EMPLEADO, EMAIL_EMPLEADO, CONTRASEÑA_EMPLEADO) VALUES (?, ?, ?, ?)";
        try {
            if (pojoEmpleado.getContrasena() == null || pojoEmpleado.getContrasena().trim().isEmpty()) {
                System.out.println("Error: La contraseña no puede ser nula o vacía");
                return false;
            }
            String contrasenaHasheada = passwordEncoder.encode(pojoEmpleado.getContrasena());
            int result = jdbcTemplate.update(sql, pojoEmpleado.getId(), pojoEmpleado.getNombre(), pojoEmpleado.getEmail(), contrasenaHasheada);
            return result > 0;
        } finally {
        }
    }
    public boolean eliminarEmpleado(Long id) {
        String sql = "DELETE FROM Empleados WHERE ID_EMPLEADO = ?";
        try {
            int value = jdbcTemplate.update(sql, id);
            return value > 0;
        } catch (DataAccessException e) {
            return false;
        }
    }
}