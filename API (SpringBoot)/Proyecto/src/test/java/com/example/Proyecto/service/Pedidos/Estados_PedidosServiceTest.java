package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.model.Estado_Pedidos;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class Estados_PedidosServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private Estados_PedidosService service;

    private Estado_Pedidos estado;

    @BeforeEach
    void setUp() {
        estado = new Estado_Pedidos();
        estado.setID_ESTADO_PEDIDO(1L);
        estado.setNOMBRE_ESTADO("Entregado");
    }

    @Test
    @DisplayName("Debe listar todos los estados de pedidos")
    void obtenerEstado_Pedidos_exito() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(estado));

        List<Estado_Pedidos> resultado = service.obtenerEstado_Pedidos();

        assertThat(resultado).hasSize(1);
        assertThat(resultado.get(0).getNOMBRE_ESTADO()).isEqualTo("Entregado");
    }

    @Test
    @DisplayName("Debe ejecutar el update para actualizar un estado")
    void actualizarEstado_exito() {
        service.actualizarEstado(1L, estado);
        verify(jdbcTemplate).update(anyString(), eq("Entregado"), eq(1L));
    }

    @Test
    @DisplayName("Debe ejecutar el delete para eliminar un estado")
    void eliminarEstado_exito() {
        service.eliminarestadoPedido(1L);
        verify(jdbcTemplate).update(contains("DELETE"), eq(1L));
    }
}
