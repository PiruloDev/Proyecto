package com.example.Proyecto.service.UnidadMedida;

import com.example.Proyecto.model.UnidadMedida;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.stereotype.Service;

import java.util.List;

@Service
public class UnidadMedidaService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    private final RowMapper<UnidadMedida> rowMapper = (rs, rowNum) -> {
        UnidadMedida u = new UnidadMedida();
        u.setIdUnidad(rs.getLong("ID_UNIDAD"));
        u.setNombreUnidad(rs.getString("NOMBRE_UNIDAD"));
        u.setAbreviaturaUnidad(rs.getString("ABREVIATURA_UNIDAD"));
        return u;
    };

    public List<UnidadMedida> obtenerTodas() {
        String sql = "SELECT ID_UNIDAD, NOMBRE_UNIDAD, ABREVIATURA_UNIDAD " +
                "FROM unidades_medida ORDER BY NOMBRE_UNIDAD";
        return jdbcTemplate.query(sql, rowMapper);
    }
}