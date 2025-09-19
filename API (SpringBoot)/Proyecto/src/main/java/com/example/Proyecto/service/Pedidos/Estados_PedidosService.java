package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.model.Estado_Pedidos;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;

import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.List;

@Service
public class Estados_PedidosService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // Método para obtener todos los Estados (GET)
    public List<Estado_Pedidos> obtenerEstado_Pedidos() {
        String sql = "SELECT * FROM Estado_Pedidos";

        return jdbcTemplate.query(sql, new RowMapper<Estado_Pedidos>() {
            @Override
            public Estado_Pedidos mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
                Estado_Pedidos estadoPedidos = new Estado_Pedidos();
                // Se agrega el mapeo para el ID
                estadoPedidos.setID_ESTADO_PEDIDO(rs.getLong("ID_ESTADO_PEDIDO"));
                estadoPedidos.setNOMBRE_ESTADO(rs.getString("NOMBRE_ESTADO"));
                return estadoPedidos;
            }
        });
    }

    // Metodo para crear Estados Pedido (POST)
    public Long crearEstadoPedido(Estado_Pedidos estado) {
        String sql = "INSERT INTO Estado_Pedidos (NOMBRE_ESTADO) VALUES (?)";
        KeyHolder keyHolder = new GeneratedKeyHolder();

        jdbcTemplate.update(connection -> {
            PreparedStatement ps = connection.prepareStatement(sql, Statement.RETURN_GENERATED_KEYS);
            ps.setString(1, estado.getNOMBRE_ESTADO());
            return ps;
        }, keyHolder);

        // Se devuelve un Long para evitar posibles desbordamientos
        Number key = keyHolder.getKey();
        return key != null ? key.longValue() : -1L;
    }

    // Metodo para Actualizar Estados Pedido (PUT)
    public void actualizarEstado(Long id, Estado_Pedidos estado) {
        String sql = "UPDATE Estado_Pedidos SET NOMBRE_ESTADO = ? WHERE ID_ESTADO_PEDIDO = ?";
        jdbcTemplate.update(sql, estado.getNOMBRE_ESTADO(), id);
    }

    // Metodo para Eliminar Estados Pedido (DELETE)
    public void eliminarestadoPedido(Long id) {
        // Se corrige el nombre de la columna para que coincida con la tabla
        String sql = "DELETE FROM Estado_Pedidos WHERE ID_ESTADO_PEDIDO = ?";
        jdbcTemplate.update(sql, id);
    }
}

