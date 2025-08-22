package com.example.Proyecto.Services.Clientes;
import com.example.Proyecto.Services.Administrador.PojoAdmin;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import org.springframework.jdbc.core.RowMapper;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class ConexionClienteService {
    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<String> obtenerClientes() {
        String sql = "SELECT NOMBRE_CLI FROM Clientes";
        return jdbcTemplate.query(sql, new RowMapper<String>() {
            @Override
            public String mapRow(ResultSet rs, int rowNum) throws SQLException {
                return rs.getString("NOMBRE_CLI");
            }
        });
    }
        public boolean crearCliente (PojoCliente pojoCliente){
            String sql = "INSERT INTO Clientes (NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI) VALUES (?, ?, ?)";
            try {
                int result = jdbcTemplate.update(sql, pojoCliente.getNombre(), pojoCliente.getEmail(), pojoCliente.getTelefono());
                return result > 0;
            } catch (DataAccessException e) {
                return false;
            }
    }
}