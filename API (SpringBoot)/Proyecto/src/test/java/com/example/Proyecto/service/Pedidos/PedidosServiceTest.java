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
        @DisplayName("Debe lanzar excepción si no hay stock (Solución NullPointerException)")
        void crearPedido_sinStock_lanzaExcepcion() {
            when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any())).thenReturn(1);

            // IMPORTANTE: Retornamos 1 como stockActual (Integer), para que no sea null
            when(jdbcTemplate.queryForObject(contains("productos"), eq(Integer.class), any())).thenReturn(1);

            assertThatThrownBy(() -> service.crearPedido(pedidoEjemplo))
                    .isInstanceOf(RuntimeException.class)
                    .hasMessageContaining("No hay suficiente pan");
        }

        @Test
        @DisplayName("Debe crear pedido, guardar detalles y descontar stock")
        void crearPedido_flujoExitoso() {
            // 1. Setup local
            DetallePedidos detalle = new DetallePedidos();
            detalle.setIdProducto(5);
            detalle.setCantidadProducto(2);
            pedidoEjemplo.setDetalles(List.of(detalle));

            // 2. Mocks de validación
            when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any()))
                    .thenReturn(1);
            when(jdbcTemplate.queryForObject(contains("PRODUCTO_STOCK_MIN"), eq(Integer.class), any()))
                    .thenReturn(10);

            // 3. Simulación del KeyHolder para generar el ID del pedido
            doAnswer(invocation -> {
                KeyHolder kh = invocation.getArgument(1);
                kh.getKeyList().add(java.util.Map.of("ID_PEDIDO", 1L));
                return 1;
            }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

            // 4. Act (Ejecución)
            service.crearPedido(pedidoEjemplo);

            // 5. Verificaciones (Coverage al máximo)
            // Verifica que se descontó el stock
            verify(jdbcTemplate).update(contains("UPDATE productos"), eq(2), eq(5));

            // Verifica la llamada al método exacto que me mostraste en DetallePedidosService
            verify(detallePedidosService, times(1)).crearDetallePedido(any(DetallePedidos.class));
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

    @Nested
    @DisplayName("Pruebas de Edición desde el Dashboard")
    class EdicionDetallesTests {
        @Test
        @DisplayName("Debe actualizar el pedido completo y recalcular stock")
        void actualizarPedido_exito() {
            // 1. Setup: Necesitamos el ID y el objeto con los nuevos datos
            Long idPedido = 100L;

            Pedidos nuevosDatos = new Pedidos();
            nuevosDatos.setID_CLIENTE(1L);
            nuevosDatos.setID_EMPLEADO(2L);
            nuevosDatos.setID_ESTADO_PEDIDO(1L);
            nuevosDatos.setTOTAL_PRODUCTO(new BigDecimal("150.00"));

            DetallePedidos nuevoDetalle = new DetallePedidos();
            nuevoDetalle.setIdProducto(5);
            nuevoDetalle.setCantidadProducto(10);
            nuevosDatos.setDetalles(List.of(nuevoDetalle));

            // 2. Mocks necesarios para el flujo de 'actualizarPedido'
            // Mock para obtener detalles antiguos (para devolver stock)
            when(detallePedidosService.obtenerDetallesPorPedido(anyInt()))
                    .thenReturn(new ArrayList<>()); // Simulamos que no había detalles previos para simplificar

            // 3. ACT: Llamamos al método REAL de tu service
            // Esto es lo que "pintará de verde" las líneas en tu reporte
            service.actualizarPedido(idPedido, nuevosDatos);

            // 4. VERIFY: Verificamos que se ejecutó la actualización en la BD
            verify(jdbcTemplate, atLeastOnce()).update(contains("UPDATE pedidos SET"), any(), any(), any(), any(), any(), eq(idPedido));
        }
    }

    @Test
    @DisplayName("Debe retornar un pedido individual con sus detalles cargados")
    void obtenerPedidoPorId_flujoCompleto() {
        // 1. Mock: Simulamos que JdbcTemplate encuentra el encabezado
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), eq(100L)))
                .thenReturn(pedidoEjemplo);

        // 2. Mock: Simulamos que el servicio de detalles trae la lista
        when(detallePedidosService.obtenerDetallesPorPedido(100))
                .thenReturn(List.of(detalleEjemplo));

        // 3. Act
        Pedidos resultado = service.obtenerPedidoPorId(100L);

        // 4. Assert
        assertThat(resultado).isNotNull();
        assertThat(resultado.getID_PEDIDO()).isEqualTo(100);
        // Esta verificación es clave para asegurar que se intentó cargar el detalle
        verify(detallePedidosService).obtenerDetallesPorPedido(100);
    }

    @Test
    @DisplayName("Debe lanzar excepción si el producto no existe en inventario")
    void crearPedido_productoNoExiste_lanzaExcepcion() {
        when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any())).thenReturn(1);

        // Simulamos que el producto NO existe lanzando la excepción esperada
        when(jdbcTemplate.queryForObject(contains("productos"), eq(Integer.class), any()))
                .thenThrow(new org.springframework.dao.EmptyResultDataAccessException(1));

        assertThatThrownBy(() -> service.crearPedido(pedidoEjemplo))
                .isInstanceOf(RuntimeException.class)
                .hasMessageContaining("no existe en el inventario");
    }
    @Test
    @DisplayName("Debe cargar lista de pedidos aunque fallen los detalles de uno")
    void obtenerPedidos_errorEnDetalles_noRompeElFlujo() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(pedidoEjemplo));

        // Simulamos que el servicio de detalles falla
        when(detallePedidosService.obtenerDetallesPorPedido(anyInt()))
                .thenThrow(new RuntimeException("Error de conexión"));

        List<Pedidos> resultado = service.obtenerPedidos();

        assertThat(resultado).isNotEmpty();
        // Verificamos que se inicializó la lista vacía según tu catch
        assertThat(resultado.get(0).getDetalles()).isEmpty();
    }

    @Test
    @DisplayName("Debe retornar null si el pedido no existe")
    void obtenerPedidoPorId_noExiste_retornaNull() {
        // Simulamos que Spring no encuentra el pedido
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), eq(999L)))
                .thenThrow(new org.springframework.dao.EmptyResultDataAccessException(1));

        Pedidos resultado = service.obtenerPedidoPorId(999L);

        assertThat(resultado).isNull();
    }

    @Test
    @DisplayName("Debe lanzar error si el producto del detalle no existe en la DB")
    void crearPedido_productoInexistente_lanzaExcepcion() {
        when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any())).thenReturn(1);

        // Simulamos que el producto no existe en la tabla de productos
        when(jdbcTemplate.queryForObject(contains("productos"), eq(Integer.class), any()))
                .thenThrow(new org.springframework.dao.EmptyResultDataAccessException(1));

        assertThatThrownBy(() -> service.crearPedido(pedidoEjemplo))
                .isInstanceOf(RuntimeException.class)
                .hasMessageContaining("no existe en el inventario");
    }

    @Test
    @DisplayName("Debe retornar el pedido completo cuando el ID existe")
    void obtenerPedidoPorId_encontrado() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), eq(100L)))
                .thenReturn(pedidoEjemplo);
        when(detallePedidosService.obtenerDetallesPorPedido(100))
                .thenReturn(List.of(detalleEjemplo));

        Pedidos resultado = service.obtenerPedidoPorId(100L);

        assertThat(resultado).isNotNull();
        assertThat(resultado.getID_PEDIDO()).isEqualTo(100);
    }
///
@Test
@DisplayName("Debe listar pedidos filtrados por un cliente específico")
void obtenerPedidosPorCliente_exito() {
    // 1. Arrange
    when(jdbcTemplate.query(contains("WHERE p.ID_CLIENTE = ?"), any(RowMapper.class), eq(1L)))
            .thenReturn(List.of(pedidoEjemplo));

    // 2. Act
    List<Pedidos> resultado = service.obtenerPedidosPorCliente(1L);

    // 3. Assert (Combinamos las verificaciones de ambas)
    assertThat(resultado).hasSize(1);
    assertThat(resultado.get(0).getID_CLIENTE()).isEqualTo(1L);

    // Verificamos que se llamó al JdbcTemplate con el parámetro correcto
    verify(jdbcTemplate).query(anyString(), any(RowMapper.class), eq(1L));
}

    @Test
    @DisplayName("Debe cargar la lista de pedidos aunque uno falle al cargar detalles")
    void obtenerPedidos_errorEnDetalles_noFallaDashboard() {
        // 1. Simulamos una lista con un pedido
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(pedidoEjemplo));

        // 2. Simulamos que al pedir los detalles de ese pedido, ocurre un error
        when(detallePedidosService.obtenerDetallesPorPedido(anyInt()))
                .thenThrow(new RuntimeException("Error de base de datos"));

        // 3. Act
        List<Pedidos> resultado = service.obtenerPedidos();

        // 4. Assert: El pedido debe existir pero con lista de detalles vacía (según tu catch)
        assertThat(resultado).isNotEmpty();
        assertThat(resultado.get(0).getDetalles()).isEmpty();
    }

    @Test
    @DisplayName("No debe procesar detalles ni stock si el ID de pedido generado es 0")
    void crearPedido_idGeneradoCero_noProcesaDetalles() {
        // 1. Mock para cliente existe
        when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any())).thenReturn(1);

        // 2. IMPORTANTE: Mock para la validación de stock (para evitar el NullPointerException)
        // Simulamos que hay stock suficiente (10) para que pase la validación inicial
        when(jdbcTemplate.queryForObject(contains("productos"), eq(Integer.class), any())).thenReturn(10);

        // 3. Simulación del KeyHolder devolviendo ID 0
        doAnswer(invocation -> {
            // No añadimos ninguna clave al KeyHolder para que el ID sea 0 o null
            return 1; // El update se ejecutó, pero no generó ID
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        // 4. Act
        service.crearPedido(pedidoEjemplo);

        // 5. Verify: Verificamos que al ser ID 0, NUNCA se llamó al update de stock final
        verify(jdbcTemplate, never()).update(contains("UPDATE productos SET PRODUCTO_STOCK_MIN = PRODUCTO_STOCK_MIN - ?"), anyInt(), anyInt());
        verify(detallePedidosService, never()).crearDetallePedido(any());
    }

    @Test
    @DisplayName("Debe actualizar encabezado aunque no haya detalles que procesar")
    void actualizarPedido_sinDetalles_soloActualizaEncabezado() {
        pedidoEjemplo.setDetalles(null); // Caso sin detalles
        when(detallePedidosService.obtenerDetallesPorPedido(anyInt())).thenReturn(null);

        service.actualizarPedido(100L, pedidoEjemplo);

        // Verifica que se hizo el UPDATE del pedido pero no se tocaron los stocks
        verify(jdbcTemplate).update(contains("UPDATE pedidos SET"), any(), any(), any(), any(), any(), eq(100L));
        verify(jdbcTemplate, never()).update(contains("PRODUCTO_STOCK_MIN - ?"), anyInt(), anyInt());
    }

    @Test
    @DisplayName("Debe manejar error si fallan los detalles de un pedido individual")
    void obtenerPedidoPorId_errorDetalles_manejaExcepcion() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyLong())).thenReturn(pedidoEjemplo);
        when(detallePedidosService.obtenerDetallesPorPedido(anyInt())).thenThrow(new RuntimeException("Error"));

        Pedidos resultado = service.obtenerPedidoPorId(100L);

        assertThat(resultado.getDetalles()).isEmpty();
    }

    @Test
    @DisplayName("Debe retornar false si ocurre un error al validar cliente")
    void crearPedido_errorAlValidarCliente_retornaExcepcion() {
        when(jdbcTemplate.queryForObject(contains("clientes"), eq(Integer.class), any()))
                .thenThrow(new RuntimeException("DB Offline"));

        assertThatThrownBy(() -> service.crearPedido(pedidoEjemplo))
                .isInstanceOf(RuntimeException.class)
                .hasMessageContaining("El cliente con ID 1 no existe");
    }

    @Test
    @DisplayName("actualizarPedido: Debe funcionar correctamente cuando no hay detalles previos que restaurar")
    void actualizarPedido_sinDetallesPrevios_exito() {
        // Simulamos que el pedido no tenía detalles antes de la actualización
        when(detallePedidosService.obtenerDetallesPorPedido(anyInt())).thenReturn(new java.util.ArrayList<>());

        // Act
        service.actualizarPedido(100L, pedidoEjemplo);

        // Verify: Verificamos que se actualizó el encabezado
        verify(jdbcTemplate).update(contains("UPDATE pedidos SET"), any(), any(), any(), any(), any(), eq(100L));
    }

    @Test
    @DisplayName("Debe retornar el pedido completo con sus detalles cuando el ID existe")
    void obtenerPedidoPorId_exito() {
        // 1. Mock del encabezado
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), eq(100L)))
                .thenReturn(pedidoEjemplo);

        // 2. Mock de los detalles (usamos la lista que ya tiene pedidoEjemplo)
        when(detallePedidosService.obtenerDetallesPorPedido(100))
                .thenReturn(pedidoEjemplo.getDetalles());

        // 3. Act
        Pedidos resultado = service.obtenerPedidoPorId(100L);

        // 4. Assert
        assertThat(resultado).isNotNull();
        assertThat(resultado.getID_PEDIDO()).isEqualTo(100);
        verify(detallePedidosService).obtenerDetallesPorPedido(100);
    }

    @Test
    @DisplayName("clienteExiste: Debe retornar false cuando ocurre una excepción en la base de datos")
    void clienteExiste_manejaExcepcion_retornaFalse() {
        // Este test entra en el bloque CATCH del método privado clienteExiste
        // Para probar métodos privados que fallan, forzamos un error en el JdbcTemplate
        when(jdbcTemplate.queryForObject(contains("SELECT COUNT(*) FROM clientes"), eq(Integer.class), anyLong()))
                .thenThrow(new RuntimeException("Error de conexión"));

        // Ejecutamos a través de crearPedido para que llame al método privado
        assertThatThrownBy(() -> service.crearPedido(pedidoEjemplo))
                .isInstanceOf(RuntimeException.class)
                .hasMessageContaining("El cliente con ID 1 no existe");
    }

}



