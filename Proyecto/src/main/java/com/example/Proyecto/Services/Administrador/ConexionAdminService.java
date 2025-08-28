package com.example.Proyecto.Services.Administrador;
import com.example.Proyecto.Services.Empleados.PojoEmpleado;
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
public class ConexionAdminService {
    @Autowired
    private JdbcTemplate jdbcTemplate;
    @Autowired
    private PasswordEncoder passwordEncoder;

    public List<Map<String, Object>> obtenerDetallesAdministrador() {
        String sql = "SELECT ID_ADMIN, NOMBRE_ADMIN, TELEFONO_ADMIN, EMAIL_ADMIN FROM Administradores";
        return jdbcTemplate.query(sql, new RowMapper<Map<String, Object>>() {
            @Override
            public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                Map<String, Object> administrador = new HashMap<>();
                administrador.put("Id:", rs.getInt("ID_ADMIN"));
                administrador.put("Nombre:", rs.getString("NOMBRE_ADMIN"));
                administrador.put("Correo Electronico:", rs.getString("EMAIL_ADMIN"));
                administrador.put("Telefono:", rs.getString("TELEFONO_ADMIN"));
                return administrador;
            }
        });
    }

    public boolean crearAdmin(PojoAdmin pojoAdmin) {
        String sql = "INSERT INTO Administradores (NOMBRE_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN, CONTRASEÑA_ADMIN) VALUES (?, ?, ?, ?)";
        try {
            if (pojoAdmin.getContrasena() == null || pojoAdmin.getContrasena().trim().isEmpty()) {
                System.out.println("Error: La contraseña no puede ser nula o vacía");
                return false;
            }

            String contrasenaHasheada = passwordEncoder.encode(pojoAdmin.getContrasena());
            int result = jdbcTemplate.update(sql, pojoAdmin.getNombre(), pojoAdmin.getEmail(), pojoAdmin.getTelefono(), contrasenaHasheada);
            return result > 0;
        } catch (DataAccessException e) {
            return false;
        }
    }

    public boolean actualizarAdministrador(PojoAdmin pojoAdmin) {
        String sql = "UPDATE Administradores SET NOMBRE_ADMIN = ?, EMAIL_ADMIN = ?, TELEFONO_ADMIN=?, CONTRASENA_ADMIN = ? WHERE ID_ADMIN = ?";
        try {
            String contrasenaHasheada = passwordEncoder.encode(pojoAdmin.getContrasena());
            int value = jdbcTemplate.update(
                    sql, pojoAdmin.getNombre(), pojoAdmin.getEmail(), pojoAdmin.getTelefono(), contrasenaHasheada, pojoAdmin.getId());
            return value > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
}
