package com.example.Proyecto.service.UsuariosRegistradosService;

import com.example.Proyecto.model.UsuariosRegistradosDTO;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;

@Service
public class UsuariosRegistradosService {

        @Autowired
        private JdbcTemplate jdbcTemplate;

        public List<UsuariosRegistradosDTO> obtenerUsuariosRegistrados() {
                List<UsuariosRegistradosDTO> usuariosRegistrados = new ArrayList<>();

                // Clientes
                String sqlClientes = "SELECT NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI FROM clientes";
                usuariosRegistrados.addAll(jdbcTemplate.query(sqlClientes, (rs, rowNum) ->
                        new UsuariosRegistradosDTO(
                                rs.getString("NOMBRE_CLI"),
                                rs.getString("EMAIL_CLI"),
                                rs.getString("TELEFONO_CLI"),
                                "Cliente"
                        )
                ));

                // Empleados
                String sqlEmpleados = "SELECT NOMBRE_EMPLEADO, EMAIL_EMPLEADO FROM empleados";
                usuariosRegistrados.addAll(jdbcTemplate.query(sqlEmpleados, (rs, rowNum) ->
                        new UsuariosRegistradosDTO(
                                rs.getString("NOMBRE_EMPLEADO"),
                                rs.getString("EMAIL_EMPLEADO"),
                                null,
                                "Empleado"
                        )
                ));

                // Administradores
                String sqlAdmins = "SELECT NOMBRE_ADMIN, EMAIL_ADMIN FROM administradores";
                usuariosRegistrados.addAll(jdbcTemplate.query(sqlAdmins, (rs, rowNum) ->
                        new UsuariosRegistradosDTO(
                                rs.getString("NOMBRE_ADMIN"),
                                rs.getString("EMAIL_ADMIN"),
                                null,
                                "Administrador"
                        )
                ));

                return usuariosRegistrados;
        }

}

