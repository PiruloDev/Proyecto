package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.model.Pedidos;
import com.example.Proyecto.service.DetallePedidos.DetallePedidos;
import com.example.Proyecto.service.DetallePedidos.DetallePedidosService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.PreparedStatementCreator;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.KeyHolder;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class PedidosServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @Mock
    private DetallePedidosService detallePedidosService;

    @InjectMocks
    private pedidosService service;

    private Pedidos pedidoEjemplo;
    private DetallePedidos detalleEjemplo;

    @BeforeEach
    void setUp() {
        pedidoEjemplo = new Pedidos();
        pedidoEjemplo.setID_PEDIDO(100);
        pedidoEjemplo.setID_CLIENTE(1L);
        pedidoEjemplo.setID_EMPLEADO(2L);
        pedidoEjemplo.setID_ESTADO_PEDIDO(1L);
        pedidoEjemplo.setTOTAL_PRODUCTO(new BigDecimal("50.00"));

        detalleEjemplo = new DetallePedidos();
        detalleEjemplo.setIdProducto(5);
        detalleEjemplo.setCantidadProducto(2);

        pedidoEjemplo.setDetalles(new ArrayList<>(List.of(detalleEjemplo)));
    }

    @Nested
    @DisplayName("Pruebas de Consulta de Pedidos")
    class ConsultasTests {
        @Test
        @SuppressWarnings("unchecked")
        @DisplayName("Debe retornar lista de pedidos con sus detalles cargados")
        void obtenerPedidos_retornaListaConDetalles() {
            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenReturn(List.of(pedidoEjemplo));
            when(detallePedidosService.obtenerDetallesPorPedido(100))
                    .thenReturn(List.of(detalleEjemplo));

            List<Pedidos> resultado = service.obtenerPedidos();

            assertThat(resultado).isNotEmpty();
            assertThat(resultado.get(0).getDetalles()).hasSize(1);
        }
    }

    @Nested
    @DisplayName("Pruebas de Creación de Pedidos")
    class CrearPedidoTests {

        @Test
        @DisplayName("Debe lanzar excepción si el cliente no existe")
        void crearPedido_clienteNoExiste_lanzaExcepcion() {
            // Usamos any() para el tercer parámetro para evitar conflictos de Long vs long
            when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any()))
                    .thenReturn(0);

            assertThatThrownBy(() -> service.crearPedido(pedidoEjemplo))
                    .isInstanceOf(RuntimeException.class)
                    .hasMessageContaining("El cliente con ID 1 no existe");
        }

        @Test
        @DisplayName("Debe lanzar excepción si no hay stock suficiente")
        void crearPedido_sinStock_lanzaExcepcion() {
            // 1. Mock para cliente existe
            when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any()))
                    .thenReturn(1);

            // 2. Mock para stock insuficiente (retorna 1, pedimos 2)
            // CAMBIO: Usamos any() en lugar de anyLong() para evitar el error de "Strict stubbing"
            when(jdbcTemplate.queryForObject(contains("productos"), eq(Integer.class), any()))
                    .thenReturn(1);

            assertThatThrownBy(() -> service.crearPedido(pedidoEjemplo))
                    .isInstanceOf(RuntimeException.class)
                    .hasMessageContaining("No hay suficiente pan");
        }

        @Test
        @DisplayName("Debe crear pedido, guardar detalles y descontar stock")
        void crearPedido_flujoExitoso() {
            // 1. Setup (Asegúrate de que esto esté así)
            DetallePedidos detalle = new DetallePedidos();
            detalle.setIdProducto(5);
            detalle.setCantidadProducto(2);
            pedidoEjemplo.setDetalles(List.of(detalle));

            // 2. Mocks de consulta
            when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), anyLong()))
                    .thenReturn(1);
            when(jdbcTemplate.queryForObject(contains("SELECT PRODUCTO_STOCK_MIN"), eq(Integer.class), eq(5)))
                    .thenReturn(10);

            // 3. Simular que el KeyHolder devuelve un ID (ID = 1)
            // Esto es lo que evita que 'idPedidoGenerado > 0' sea falso
            doAnswer(invocation -> {
                KeyHolder kh = invocation.getArgument(1);
                kh.getKeyList().add(java.util.Map.of("ID_PEDIDO", 1)); // Simulamos ID 1
                return 1;
            }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

            // 4. Act
            service.crearPedido(pedidoEjemplo);

            // 5. Assert
            // Verificamos el update de stock con any() para evitar conflictos de tipos
            verify(jdbcTemplate).update(
                    contains("UPDATE productos SET PRODUCTO_STOCK_MIN"),
                    eq(2),
                    eq(5)
            );
        }

        @Nested
        @DisplayName("Pruebas de Eliminación")
        class EliminarPedidoTests {
            @Test
            @DisplayName("Debe devolver stock y eliminar pedido")
            void eliminarPedido_devuelveStockYElimina() {
                when(detallePedidosService.obtenerDetallesPorPedido(100))
                        .thenReturn(List.of(detalleEjemplo));

                service.eliminarPedido(100L);

                // 1. Verificar restauración de stock
                verify(jdbcTemplate).update(contains("PRODUCTO_STOCK_MIN + ?"), anyInt(), anyInt());

                // 2. SER ESPECÍFICOS: Una llamada para detalle y otra para la cabecera
                verify(jdbcTemplate).update(eq("DELETE FROM detalle_pedidos WHERE ID_PEDIDO = ?"), eq(100L));
                verify(jdbcTemplate).update(eq("DELETE FROM pedidos WHERE ID_PEDIDO = ?"), eq(100L));
            }
        }
    }
}