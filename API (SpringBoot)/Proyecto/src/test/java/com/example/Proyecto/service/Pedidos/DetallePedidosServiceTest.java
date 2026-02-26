package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.service.DetallePedidos.DetallePedidos;
import com.example.Proyecto.service.DetallePedidos.DetallePedidosService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.math.BigDecimal;
import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class DetallePedidosServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private DetallePedidosService service;

    private DetallePedidos detalleEjemplo;

    @BeforeEach
    void setUp() {
        detalleEjemplo = new DetallePedidos();
        detalleEjemplo.setIdDetalle(1);
        detalleEjemplo.setIdPedido(100);
        detalleEjemplo.setIdProducto(5);
        detalleEjemplo.setCantidadProducto(2);
        detalleEjemplo.setPrecioUnitario(new BigDecimal("10.00"));
        detalleEjemplo.setSubtotal(new BigDecimal("20.00"));
        detalleEjemplo.setNombreProducto("Pan Artesanal");
    }

    @Test
    @DisplayName("Debe retornar todos los detalles de pedidos")
    void obtenerTodosLosDetallesPedidos_exito() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(detalleEjemplo));

        List<DetallePedidos> resultado = service.obtenerTodosLosDetallesPedidos();

        assertThat(resultado).hasSize(1);
        assertThat(resultado.get(0).getNombreProducto()).isEqualTo("Pan Artesanal");
    }

    @Test
    @DisplayName("Debe obtener detalles específicos de un pedido con JOIN de productos")
    void obtenerDetallesPorPedido_exito() {
        when(jdbcTemplate.query(contains("INNER JOIN productos"), any(RowMapper.class), eq(100)))
                .thenReturn(List.of(detalleEjemplo));

        List<DetallePedidos> resultado = service.obtenerDetallesPorPedido(100);

        assertThat(resultado).isNotEmpty();
        verify(jdbcTemplate).query(anyString(), any(RowMapper.class), eq(100));
    }

    @Test
    @DisplayName("Debe ejecutar el insert de un nuevo detalle")
    void crearDetallePedido_exito() {
        service.crearDetallePedido(detalleEjemplo);
        verify(jdbcTemplate, times(1)).update(anyString(), any(), any(), any(), any(), any());
    }
}
