package com.example.Proyecto.Services.Administrador;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import org.springframework.jdbc.core.RowMapper;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class ConexionAdminService {
    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<String> obtenerAdministradores() {
        String sql = "SELECT NOMBRE_ADMIN FROM Administradores";
        return jdbcTemplate.query(sql, new RowMapper<String>() {
            @Override
            public String mapRow(ResultSet rs, int rowNum) throws SQLException {
                return rs.getString("NOMBRE_ADMIN");
            }
        });
    }
        public boolean crearAdmin (PojoAdmin pojoAdmin){
            String sql = "INSERT INTO Administradores (NOMBRE_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN, CONTRASEÑA_ADMIN) VALUES (?, ?, ?, ?)";
            try {
                int result = jdbcTemplate.update(sql, pojoAdmin.getNombre(), pojoAdmin.getEmail(), pojoAdmin.getTelefono(), pojoAdmin.getContraseña());
                return result > 0;
            } catch (DataAccessException e) {
                return false;
        }
    }
}