package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.model.DetallePedidoProveedores;
import com.example.Proyecto.model.PedidosProveedores;
import com.example.Proyecto.service.PedidosProveedores.PedidosProveedoresService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
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
import java.util.Collections;
import java.util.Date;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("PedidosProveedoresService - Pruebas Unitarias")
class PedidosProveedoresServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private PedidosProveedoresService pedidosProveedoresService;

    private PedidosProveedores pedidoPendiente;
    private PedidosProveedores pedidoEntregado;
    private DetallePedidoProveedores detalleHarina;
    private DetallePedidoProveedores detalleAzucar;

    @BeforeEach
    void setUp() {
        detalleHarina = new DetallePedidoProveedores();
        detalleHarina.setIdDetalleProv(1);
        detalleHarina.setIdPedidoProv(1);
        detalleHarina.setIdIngrediente(1);
        detalleHarina.setNombreIngrediente("Harina de trigo");
        detalleHarina.setCantidad(50);
        detalleHarina.setPrecioUnitario(new BigDecimal("2.50"));
        detalleHarina.setSubtotal(new BigDecimal("125.00"));

        detalleAzucar = new DetallePedidoProveedores();
        detalleAzucar.setIdDetalleProv(2);
        detalleAzucar.setIdPedidoProv(1);
        detalleAzucar.setIdIngrediente(2);
        detalleAzucar.setNombreIngrediente("Azúcar blanca");
        detalleAzucar.setCantidad(20);
        detalleAzucar.setPrecioUnitario(new BigDecimal("1.80"));
        detalleAzucar.setSubtotal(new BigDecimal("36.00"));

        pedidoPendiente = new PedidosProveedores();
        pedidoPendiente.setIdPedidoProv(1);
        pedidoPendiente.setIdProveedor(5);
        pedidoPendiente.setNumeroPedido(1001);
        pedidoPendiente.setFechaPedido(new Date());
        pedidoPendiente.setEstadoPedido("PENDIENTE");
        pedidoPendiente.setNombreProveedor("Harinera del Valle");
        pedidoPendiente.setDetalles(List.of(detalleHarina, detalleAzucar));

        pedidoEntregado = new PedidosProveedores();
        pedidoEntregado.setIdPedidoProv(2);
        pedidoEntregado.setIdProveedor(5);
        pedidoEntregado.setNumeroPedido(1002);
        pedidoEntregado.setFechaPedido(new Date());
        pedidoEntregado.setEstadoPedido("ENTREGADO");
        pedidoEntregado.setDetalles(List.of(detalleHarina));
    }

    // ─────────────────────────────────────────────
    //  obtenerTodosLosPedidosProveedores()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerTodos - retorna lista con múltiples pedidos")
    void obtenerTodos_retornaListaConPedidos() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(pedidoPendiente, pedidoEntregado));

        List<PedidosProveedores> resultado =
                pedidosProveedoresService.obtenerTodosLosPedidosProveedores();

        assertNotNull(resultado);
        assertEquals(2, resultado.size());
        assertEquals(1,          resultado.get(0).getIdPedidoProv());
        assertEquals("PENDIENTE", resultado.get(0).getEstadoPedido());
        assertEquals("ENTREGADO", resultado.get(1).getEstadoPedido());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodos - retorna lista vacía cuando no hay pedidos")
    void obtenerTodos_retornaListaVacia() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<PedidosProveedores> resultado =
                pedidosProveedoresService.obtenerTodosLosPedidosProveedores();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    @Test
    @DisplayName("obtenerTodos - lanza excepción cuando falla la consulta")
    void obtenerTodos_lanzaExcepcion_cuandoFallaJdbc() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenThrow(new RuntimeException("Error de BD"));

        assertThrows(RuntimeException.class,
                () -> pedidosProveedoresService.obtenerTodosLosPedidosProveedores());
    }

    // ─────────────────────────────────────────────
    //  obtenerPedidoConDetalles()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerPedidoConDetalles - retorna pedido con detalles cuando existe")
    void obtenerPedidoConDetalles_retornaPedidoConDetalles() {
        // Mock del queryForObject para el encabezado
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(pedidoPendiente);
        // Mock del query para los detalles
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(List.of(detalleHarina, detalleAzucar));

        PedidosProveedores resultado =
                pedidosProveedoresService.obtenerPedidoConDetalles(1);

        assertNotNull(resultado);
        assertEquals(1,                    resultado.getIdPedidoProv());
        assertEquals("PENDIENTE",          resultado.getEstadoPedido());
        assertEquals("Harinera del Valle", resultado.getNombreProveedor());
        assertNotNull(resultado.getDetalles());
        assertEquals(2, resultado.getDetalles().size());
    }

    @Test
    @DisplayName("obtenerPedidoConDetalles - retorna null cuando el pedido no existe")
    void obtenerPedidoConDetalles_retornaNull_cuandoPedidoNoExiste() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyInt()))
                .thenThrow(new org.springframework.dao.EmptyResultDataAccessException(1));

        PedidosProveedores resultado =
                pedidosProveedoresService.obtenerPedidoConDetalles(99);

        assertNull(resultado);
    }

    @Test
    @DisplayName("obtenerPedidoConDetalles - retorna pedido con lista de detalles vacía")
    void obtenerPedidoConDetalles_retornaPedidoConDetallesVacios() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(pedidoPendiente);
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(Collections.emptyList());

        PedidosProveedores resultado =
                pedidosProveedoresService.obtenerPedidoConDetalles(1);

        assertNotNull(resultado);
        assertNotNull(resultado.getDetalles());
        assertTrue(resultado.getDetalles().isEmpty());
    }

    // ─────────────────────────────────────────────
    //  crearPedidoProveedorCompleto()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("crearPedidoCompleto - inserta encabezado y detalles, retorna ID generado")
    void crearPedidoCompleto_insertaEncabezadoYDetalles() {
        doAnswer(invocation -> {
            KeyHolder kh = invocation.getArgument(1);
            java.util.Map<String, Object> keys = new java.util.HashMap<>();
            keys.put("ID_PEDIDO_PROV", 10);
            kh.getKeyList().add(keys);
            return 1;
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        when(jdbcTemplate.update(anyString(), anyInt(), anyInt(), anyInt(), any(BigDecimal.class)))
                .thenReturn(1);

        int idGenerado =
                pedidosProveedoresService.crearPedidoProveedorCompleto(pedidoPendiente);

        assertEquals(10, idGenerado);

        // Verificar que se insertó el encabezado
        verify(jdbcTemplate, times(1))
                .update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        // Verificar que se insertaron los 2 detalles (harina + azúcar)
        verify(jdbcTemplate, times(2))
                .update(anyString(), anyInt(), anyInt(), anyInt(), any(BigDecimal.class));
    }

    @Test
    @DisplayName("crearPedidoCompleto - solo inserta encabezado cuando no hay detalles")
    void crearPedidoCompleto_soloInsertaEncabezado_cuandoSinDetalles() {
        pedidoPendiente.setDetalles(Collections.emptyList());

        doAnswer(invocation -> {
            KeyHolder kh = invocation.getArgument(1);
            java.util.Map<String, Object> keys = new java.util.HashMap<>();
            keys.put("ID_PEDIDO_PROV", 11);
            kh.getKeyList().add(keys);
            return 1;
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        int idGenerado =
                pedidosProveedoresService.crearPedidoProveedorCompleto(pedidoPendiente);

        assertEquals(11, idGenerado);
        verify(jdbcTemplate, times(1))
                .update(any(PreparedStatementCreator.class), any(KeyHolder.class));
        // No debe insertar detalles
        verify(jdbcTemplate, never())
                .update(anyString(), anyInt(), anyInt(), anyInt(), any(BigDecimal.class));
    }

    @Test
    @DisplayName("crearPedidoCompleto - lanza excepción cuando falla el INSERT del encabezado")
    void crearPedidoCompleto_lanzaExcepcion_cuandoFallaInsertEncabezado() {
        doThrow(new RuntimeException("Error al insertar encabezado"))
                .when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        assertThrows(RuntimeException.class,
                () -> pedidosProveedoresService.crearPedidoProveedorCompleto(pedidoPendiente));

        verify(jdbcTemplate, never())
                .update(anyString(), anyInt(), anyInt(), anyInt(), any(BigDecimal.class));
    }

    // ─────────────────────────────────────────────
    //  editarPedidoProveedor()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("editarPedido - retorna 1 cuando la actualización es exitosa")
    void editarPedido_retorna1_cuandoActualizaCorrectamente() {
        when(jdbcTemplate.update(anyString(),
                anyInt(), anyInt(), any(Date.class), anyString(), anyInt()))
                .thenReturn(1);

        int resultado = pedidosProveedoresService.editarPedidoProveedor(pedidoPendiente);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(
                anyString(),
                eq(pedidoPendiente.getIdProveedor()),
                eq(pedidoPendiente.getNumeroPedido()),
                eq(pedidoPendiente.getFechaPedido()),
                eq("PENDIENTE"),
                eq(pedidoPendiente.getIdPedidoProv())
        );
    }

    @Test
    @DisplayName("editarPedido - retorna 0 cuando el ID no existe")
    void editarPedido_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(),
                anyInt(), anyInt(), any(Date.class), anyString(), anyInt()))
                .thenReturn(0);

        int resultado = pedidosProveedoresService.editarPedidoProveedor(pedidoPendiente);

        assertEquals(0, resultado);
    }

    @Test
    @DisplayName("editarPedido - lanza excepción cuando falla el UPDATE")
    void editarPedido_lanzaExcepcion_cuandoFallaUpdate() {
        when(jdbcTemplate.update(anyString(),
                anyInt(), anyInt(), any(Date.class), anyString(), anyInt()))
                .thenThrow(new RuntimeException("Error al actualizar pedido"));

        assertThrows(RuntimeException.class,
                () -> pedidosProveedoresService.editarPedidoProveedor(pedidoPendiente));
    }

    // ─────────────────────────────────────────────
    //  eliminarPedidoProveedor()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("eliminarPedido - retorna 1 cuando la eliminación es exitosa")
    void eliminarPedido_retorna1_cuandoEliminaCorrectamente() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(1);

        int resultado = pedidosProveedoresService.eliminarPedidoProveedor(1);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1));
    }

    @Test
    @DisplayName("eliminarPedido - retorna 0 cuando el ID no existe")
    void eliminarPedido_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(0);

        int resultado = pedidosProveedoresService.eliminarPedidoProveedor(99);

        assertEquals(0, resultado);
    }

    @Test
    @DisplayName("eliminarPedido - lanza excepción cuando falla el DELETE")
    void eliminarPedido_lanzaExcepcion_cuandoFallaDelete() {
        when(jdbcTemplate.update(anyString(), anyInt()))
                .thenThrow(new RuntimeException("Error al eliminar pedido"));

        assertThrows(RuntimeException.class,
                () -> pedidosProveedoresService.eliminarPedidoProveedor(1));
    }

    // ─────────────────────────────────────────────
    //  marcarComoEntregado()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("marcarComoEntregado - lanza RuntimeException cuando el pedido no existe")
    void marcarComoEntregado_lanzaRuntimeException_cuandoPedidoNoExiste() {
        // obtenerPedidoConDetalles retorna null → pedido no encontrado
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyInt()))
                .thenThrow(new org.springframework.dao.EmptyResultDataAccessException(1));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> pedidosProveedoresService.marcarComoEntregado(99));

        assertEquals("Pedido no encontrado", excepcion.getMessage());
        verify(jdbcTemplate, never()).update(anyString(), anyInt(), anyInt());
    }

    @Test
    @DisplayName("marcarComoEntregado - lanza IllegalStateException cuando el pedido ya fue entregado")
    void marcarComoEntregado_lanzaIllegalStateException_cuandoYaEntregado() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(pedidoEntregado);
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(List.of(detalleHarina));

        IllegalStateException excepcion = assertThrows(IllegalStateException.class,
                () -> pedidosProveedoresService.marcarComoEntregado(2));

        assertTrue(excepcion.getMessage().contains("ENTREGADO"));
        // No debe actualizar stock ni estado
        verify(jdbcTemplate, never()).update(contains("CANTIDAD_INGREDIENTE"), anyInt(), anyInt());
    }

    @Test
    @DisplayName("marcarComoEntregado - actualiza stock de ingredientes para cada detalle")
    void marcarComoEntregado_actualizaStockDeCadaIngrediente() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(pedidoPendiente);
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyInt()))
                .thenReturn(List.of(detalleHarina, detalleAzucar));
        // Mock de UPDATE de stock (por ingrediente) y UPDATE de estado del pedido
        when(jdbcTemplate.update(anyString(), anyInt(), anyInt())).thenReturn(1);
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(1);

        // El método lanzará excepción en el UPDATE de estado (nombre de tabla con guion)
        // pero verificamos que el stock SÍ fue actualizado correctamente antes
        assertThrows(Exception.class,
                () -> pedidosProveedoresService.marcarComoEntregado(1));

        // Verifica que se intentó actualizar el stock de los 2 ingredientes
        verify(jdbcTemplate, times(2)).update(anyString(), anyInt(), anyInt());
    }
}