// Archivo: ProveedoresService.java
package com.example.demo.Service.Proveedores;

import com.example.demo.Service.Proveedores.Proveedores;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

@Service
public class ProveedoresService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Obtener todos los proveedores
    public List<Proveedores> obtenerTodosLosProveedores() {
        String sql = "SELECT * FROM Proveedores";
        return jdbcTemplate.query(sql, new RowMapper<Proveedores>() {
            @Override
            public Proveedores mapRow(ResultSet rs, int rowNum) throws SQLException {
                Proveedores proveedor = new Proveedores();
                proveedor.setIdProveedor(rs.getInt("ID_PROVEEDOR"));
                proveedor.setNombreProv(rs.getString("NOMBRE_PROV"));
                proveedor.setTelefonoProv(rs.getString("TELEFONO_PROV"));
                proveedor.setActivoProv(rs.getBoolean("ACTIVO_PROV"));
                proveedor.setEmailProv(rs.getString("EMAIL_PROV"));
                proveedor.setDireccionProv(rs.getString("DIRECCION_PROV"));
                return proveedor;
            }
        });
    }

    // Crear un nuevo proveedor (POST)
    public void crearProveedor(Proveedores proveedor) {
        String sql = "INSERT INTO Proveedores (NOMBRE_PROV, TELEFONO_PROV, ACTIVO_PROV, EMAIL_PROV, DIRECCION_PROV) " +
                "VALUES (?, ?, ?, ?, ?)";
        jdbcTemplate.update(sql,
                proveedor.getNombreProv(),
                proveedor.getTelefonoProv(),
                proveedor.getActivoProv(),
                proveedor.getEmailProv(),
                proveedor.getDireccionProv()
        );
    }

    // Actualizar un proveedor (PUT)
    public int editarProveedor(Proveedores proveedor) {
        String sql = "UPDATE Proveedores SET NOMBRE_PROV=?, TELEFONO_PROV=?, ACTIVO_PROV=?, EMAIL_PROV=?, DIRECCION_PROV=? WHERE ID_PROVEEDOR=?";
        return jdbcTemplate.update(sql,
                proveedor.getNombreProv(),
                proveedor.getTelefonoProv(),
                proveedor.getActivoProv(),
                proveedor.getEmailProv(),
                proveedor.getDireccionProv(),
                proveedor.getIdProveedor()
        );
    }

    // Eliminar un proveedor (DELETE)
    public int eliminarProveedor(int idProveedor) {
        String sql = "DELETE FROM Proveedores WHERE ID_PROVEEDOR = ?";
        return jdbcTemplate.update(sql, idProveedor);
    }
}