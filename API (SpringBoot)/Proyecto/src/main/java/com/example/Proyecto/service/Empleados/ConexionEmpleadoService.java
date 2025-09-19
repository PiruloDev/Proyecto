package com.example.Proyecto.service.Empleados;
import com.example.Proyecto.model.PojoEmpleado;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.lang.NonNull;
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
        String sql = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO, EMAIL_EMPLEADO, ACTIVO_EMPLEADO FROM Empleados";
        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> empleado = new HashMap<>();
                empleado.put("Id:", rs.getInt("ID_EMPLEADO"));
                empleado.put("Nombre:", rs.getString("NOMBRE_EMPLEADO"));
                empleado.put("Correo Electronico:", rs.getString("EMAIL_EMPLEADO"));

                int estado = rs.getInt("ACTIVO_EMPLEADO");
                String estadoTexto = (estado == 1) ? "ACTIVO" : "INACTIVO";
                empleado.put("Estado del Empleado", estadoTexto);
                return empleado;
            }
        });
    }

    public boolean crearEmpleado(PojoEmpleado pojoEmpleado) {
        String sql = "INSERT INTO Empleados (ID_EMPLEADO, NOMBRE_EMPLEADO, EMAIL_EMPLEADO, CONTRASENA_EMPLEADO) VALUES (?, ?, ?, ?)";
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

    public boolean actualizarEmpleado(PojoEmpleado pojoEmpleado) {
        String sql = "UPDATE Empleados SET NOMBRE_EMPLEADO = ?, EMAIL_EMPLEADO = ?, CONTRASENA_EMPLEADO = ? WHERE ID_EMPLEADO = ?";
        try {
            String contrasenaHasheada = passwordEncoder.encode(pojoEmpleado.getContrasena());
            int value = jdbcTemplate.update(
                    sql, pojoEmpleado.getNombre(), pojoEmpleado.getEmail(), contrasenaHasheada, pojoEmpleado.getId());
            return value > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
}
