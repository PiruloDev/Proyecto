package com.example.Proyecto.Services.Empleados;
import com.example.Proyecto.Services.Clientes.PojoCliente;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import org.springframework.jdbc.core.RowMapper;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class ConexionEmpleadoService {
    @Autowired
    private JdbcTemplate jdbcTemplate;

    public List<String> obtenerEmpleados() {
        String sql = "SELECT NOMBRE_EMPLEADO FROM Empleados";
        return jdbcTemplate.query(sql, new RowMapper<String>() {
            @Override
            public String mapRow(ResultSet rs, int rowNum) throws SQLException {
                return rs.getString("NOMBRE_EMPLEADO");
            }
        });
    }

    public boolean crearEmpleado(PojoEmpleado pojoEmpleado) {
        String sql = "INSERT INTO Empleados (NOMBRE_EMPLEADO, EMAIL_EMPLEADO) VALUES (?, ?)";
        try {
            int result = jdbcTemplate.update(sql, pojoEmpleado.getNombre(), pojoEmpleado.getEmail(), pojoEmpleado.getTelefono());
            return result > 0;
        } catch (DataAccessException e) {
            return false;
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