package com.example.Proyecto.service.Auth;
import com.example.Proyecto.model.PojoAdmin;
import com.example.Proyecto.model.PojoEmpleado;
import com.example.Proyecto.model.PojoCliente;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;

@Service
public class AuthService {
    
    @Autowired
    private JdbcTemplate jdbcTemplate;
    
    /**
     * Método helper para hashear contraseñas usando SHA-256
     */
    private String hashPassword(String password) {
        try {
            MessageDigest digest = MessageDigest.getInstance("SHA-256");
            byte[] hash = digest.digest(password.getBytes());
            StringBuilder hexString = new StringBuilder();
            for (byte b : hash) {
                String hex = Integer.toHexString(0xff & b);
                if (hex.length() == 1) {
                    hexString.append('0');
                }
                hexString.append(hex);
            }
            return hexString.toString();
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException("Error al hashear contraseña", e);
        }
    }

    public Map<String, Object> autenticarUsuario(String email, String contrasena) {
        System.out.println("=== AUTENTICACIÓN ===");
        System.out.println("Email: " + email);
        
        // Intentar autenticar como administrador
        Map<String, Object> admin = autenticarAdmin(email, contrasena);
        if (admin != null) {
            admin.put("tipoUsuario", "ADMIN");
            admin.put("rol", "ADMINISTRADOR");
            System.out.println("Usuario autenticado como ADMINISTRADOR");
            return admin;
        }
        
        // Intentar autenticar como empleado
        Map<String, Object> empleado = autenticarEmpleado(email, contrasena);
        if (empleado != null) {
            empleado.put("tipoUsuario", "EMPLEADO");
            empleado.put("rol", "EMPLEADO");
            System.out.println("Usuario autenticado como EMPLEADO");
            return empleado;
        }
        
        // Intentar autenticar como cliente
        Map<String, Object> cliente = autenticarCliente(email, contrasena);
        if (cliente != null) {
            cliente.put("tipoUsuario", "CLIENTE");
            cliente.put("rol", "CLIENTE");
            System.out.println("Usuario autenticado como CLIENTE");
            return cliente;
        }
        
        System.out.println("No se encontró usuario con esas credenciales");
        return null;
    }
    
    private Map<String, Object> autenticarAdmin(String email, String contrasena) {
        String sql = "SELECT ID_ADMIN, NOMBRE_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN FROM Administradores WHERE EMAIL_ADMIN = ? AND CONTRASENA_ADMIN = ?";
        try {
            String contrasenaHasheada = hashPassword(contrasena);
            return jdbcTemplate.queryForObject(sql, new RowMapper<Map<String, Object>>() {
                @Override
                public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                    Map<String, Object> admin = new HashMap<>();
                    admin.put("id", rs.getInt("ID_ADMIN"));
                    admin.put("nombre", rs.getString("NOMBRE_ADMIN"));
                    admin.put("email", rs.getString("EMAIL_ADMIN"));
                    admin.put("telefono", rs.getString("TELEFONO_ADMIN"));
                    return admin;
                }
            }, email, contrasenaHasheada);
        } catch (DataAccessException e) {
            return null;
        }
    }
    
    private Map<String, Object> autenticarEmpleado(String email, String contrasena) {
        String sql = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO, EMAIL_EMPLEADO FROM Empleados WHERE EMAIL_EMPLEADO = ? AND CONTRASENA_EMPLEADO = ? AND ACTIVO_EMPLEADO = TRUE";
        try {
            String contrasenaHasheada = hashPassword(contrasena);
            return jdbcTemplate.queryForObject(sql, new RowMapper<Map<String, Object>>() {
                @Override
                public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                    Map<String, Object> empleado = new HashMap<>();
                    empleado.put("id", rs.getInt("ID_EMPLEADO"));
                    empleado.put("nombre", rs.getString("NOMBRE_EMPLEADO"));
                    empleado.put("email", rs.getString("EMAIL_EMPLEADO"));
                    return empleado;
                }
            }, email, contrasenaHasheada);
        } catch (DataAccessException e) {
            return null;
        }
    }
    
    private Map<String, Object> autenticarCliente(String email, String contrasena) {
        String sql = "SELECT ID_CLIENTE, NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI FROM Clientes WHERE EMAIL_CLI = ? AND CONTRASENA_CLI = ? AND ACTIVO_CLI = TRUE";
        try {
            String contrasenaHasheada = hashPassword(contrasena);
            return jdbcTemplate.queryForObject(sql, new RowMapper<Map<String, Object>>() {
                @Override
                public Map<String, Object> mapRow(ResultSet rs, int rowNum) throws SQLException {
                    Map<String, Object> cliente = new HashMap<>();
                    cliente.put("id", rs.getInt("ID_CLIENTE"));
                    cliente.put("nombre", rs.getString("NOMBRE_CLI"));
                    cliente.put("email", rs.getString("EMAIL_CLI"));
                    cliente.put("telefono", rs.getString("TELEFONO_CLI"));
                    return cliente;
                }
            }, email, contrasenaHasheada);
        } catch (DataAccessException e) {
            return null;
        }
    }
    
    /**
     * Registra un nuevo administrador
     */
    public boolean registrarAdmin(PojoAdmin admin) {
        String sql = "INSERT INTO Administradores (NOMBRE_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN, CONTRASENA_ADMIN) VALUES (?, ?, ?, ?)";
        try {
            String contrasenaHasheada = hashPassword(admin.getContrasena());
            int result = jdbcTemplate.update(sql, admin.getNombre(), admin.getEmail(), admin.getTelefono(), contrasenaHasheada);
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
    
    /**
     * Registra un nuevo empleado
     */
    public boolean registrarEmpleado(PojoEmpleado empleado) {
        String sql = "INSERT INTO Empleados (NOMBRE_EMPLEADO, EMAIL_EMPLEADO, CONTRASENA_EMPLEADO) VALUES (?, ?, ?)";
        try {
            String contrasenaHasheada = hashPassword(empleado.getContrasena());
            int result = jdbcTemplate.update(sql, empleado.getNombre(), empleado.getEmail(), contrasenaHasheada);
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
    
    /**
     * Registra un nuevo cliente
     */
    public boolean registrarCliente(PojoCliente cliente) {
        String sql = "INSERT INTO Clientes (NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI, CONTRASENA_CLI) VALUES (?, ?, ?, ?)";
        try {
            String contrasenaHasheada = hashPassword(cliente.getContrasena());
            int result = jdbcTemplate.update(sql, cliente.getNombre(), cliente.getEmail(), cliente.getTelefono(), contrasenaHasheada);
            return result > 0;
        } catch (DataAccessException e) {
            e.printStackTrace();
            return false;
        }
    }
}