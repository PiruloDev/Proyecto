package com.example.Proyecto.service.PasswordReset;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.util.HashMap;
import java.util.Map;

@Service
public class PasswordResetService {
    
    @Autowired
    private JdbcTemplate jdbcTemplate;
    
    private static final int TEMP_PASSWORD_LENGTH = 10;
    private static final String UPPERCASE = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private static final String LOWERCASE = "abcdefghijklmnopqrstuvwxyz";
    private static final String NUMBERS = "0123456789";
    private static final String ALL_CHARS = UPPERCASE + LOWERCASE + NUMBERS;
    
    public Map<String, Object> procesarRestablecimiento(String email) {
        Map<String, Object> resultado = new HashMap<>();
        
        if (email == null || email.trim().isEmpty()) {
            resultado.put("success", false);
            resultado.put("mensaje", "El email es obligatorio");
            return resultado;
        }
        
        Map<String, Object> usuario = buscarUsuarioPorEmail(email.trim());
        
        if (usuario == null) {
            resultado.put("success", false);
            resultado.put("mensaje", "No existe un usuario registrado con ese email");
            return resultado;
        }
        
        String nuevaContrasena = generarContrasenaTemporal();
        
        boolean actualizado = actualizarContrasena(
            (String) usuario.get("tipoUsuario"),
            (Integer) usuario.get("id"),
            nuevaContrasena
        );
        
        if (!actualizado) {
            resultado.put("success", false);
            resultado.put("mensaje", "Error al actualizar la contraseña");
            return resultado;
        }
        
        resultado.put("success", true);
        resultado.put("mensaje", "Contraseña actualizada exitosamente");
        resultado.put("email", email);
        resultado.put("nuevaContrasena", nuevaContrasena);
        resultado.put("tipoUsuario", usuario.get("tipoUsuario"));
        
        return resultado;
    }
    
    private Map<String, Object> buscarUsuarioPorEmail(String email) {
        try {
            String sqlAdmin = "SELECT ID_ADMIN as id, NOMBRE_ADMIN as nombre, EMAIL_ADMIN as email FROM Administradores WHERE EMAIL_ADMIN = ?";
            Map<String, Object> admin = jdbcTemplate.queryForMap(sqlAdmin, email);
            admin.put("tipoUsuario", "ADMINISTRADOR");
            return admin;
        } catch (EmptyResultDataAccessException e) {}
        
        try {
            String sqlEmpleado = "SELECT ID_EMPLEADO as id, NOMBRE_EMPLEADO as nombre, EMAIL_EMPLEADO as email FROM Empleados WHERE EMAIL_EMPLEADO = ? AND ACTIVO_EMPLEADO = TRUE";
            Map<String, Object> empleado = jdbcTemplate.queryForMap(sqlEmpleado, email);
            empleado.put("tipoUsuario", "EMPLEADO");
            return empleado;
        } catch (EmptyResultDataAccessException e) {}
        
        try {
            String sqlCliente = "SELECT ID_CLIENTE as id, NOMBRE_CLI as nombre, EMAIL_CLI as email FROM Clientes WHERE EMAIL_CLI = ? AND ACTIVO_CLI = TRUE";
            Map<String, Object> cliente = jdbcTemplate.queryForMap(sqlCliente, email);
            cliente.put("tipoUsuario", "CLIENTE");
            return cliente;
        } catch (EmptyResultDataAccessException e) {}
        
        return null;
    }
    
    private boolean actualizarContrasena(String tipoUsuario, Integer id, String nuevaContrasena) {
        try {
            String contrasenaHasheada = hashPassword(nuevaContrasena);
            String sql = "";
            
            switch (tipoUsuario) {
                case "ADMINISTRADOR":
                    sql = "UPDATE Administradores SET CONTRASENA_ADMIN = ? WHERE ID_ADMIN = ?";
                    break;
                case "EMPLEADO":
                    sql = "UPDATE Empleados SET CONTRASENA_EMPLEADO = ? WHERE ID_EMPLEADO = ?";
                    break;
                case "CLIENTE":
                    sql = "UPDATE Clientes SET CONTRASENA_CLI = ? WHERE ID_CLIENTE = ?";
                    break;
                default:
                    return false;
            }
            
            int filasAfectadas = jdbcTemplate.update(sql, contrasenaHasheada, id);
            return filasAfectadas > 0;
            
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }
    
    private String generarContrasenaTemporal() {
        SecureRandom random = new SecureRandom();
        StringBuilder password = new StringBuilder(TEMP_PASSWORD_LENGTH);
        
        password.append(UPPERCASE.charAt(random.nextInt(UPPERCASE.length())));
        password.append(LOWERCASE.charAt(random.nextInt(LOWERCASE.length())));
        password.append(NUMBERS.charAt(random.nextInt(NUMBERS.length())));
        
        for (int i = 3; i < TEMP_PASSWORD_LENGTH; i++) {
            password.append(ALL_CHARS.charAt(random.nextInt(ALL_CHARS.length())));
        }
        
        return password.toString();
    }
    
    public Map<String, Object> cambiarContrasena(String email, String nuevaContrasena) {
        Map<String, Object> resultado = new HashMap<>();
        
        if (email == null || email.trim().isEmpty()) {
            resultado.put("success", false);
            resultado.put("mensaje", "El email es obligatorio");
            return resultado;
        }
        
        if (nuevaContrasena == null || nuevaContrasena.trim().isEmpty()) {
            resultado.put("success", false);
            resultado.put("mensaje", "La nueva contraseña es obligatoria");
            return resultado;
        }
        
        Map<String, Object> usuario = buscarUsuarioPorEmail(email.trim());
        
        if (usuario == null) {
            resultado.put("success", false);
            resultado.put("mensaje", "No existe un usuario registrado con ese email");
            return resultado;
        }
        
        boolean actualizado = actualizarContrasena(
            (String) usuario.get("tipoUsuario"),
            (Integer) usuario.get("id"),
            nuevaContrasena
        );
        
        if (!actualizado) {
            resultado.put("success", false);
            resultado.put("mensaje", "Error al actualizar la contraseña");
            return resultado;
        }
        
        resultado.put("success", true);
        resultado.put("mensaje", "Contraseña actualizada exitosamente");
        resultado.put("email", email);
        
        return resultado;
    }
    
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
}
