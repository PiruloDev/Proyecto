package com.example.Proyecto.service.Inventario.Produccion;

import com.example.Proyecto.dto.ProduccionRequest;
import com.example.Proyecto.model.Produccion;
import com.example.Proyecto.model.RecetaProducto;
import com.example.Proyecto.service.Ingredientes.IngredientesService;
import com.example.Proyecto.service.Inventario.ProduccionService;
import com.example.Proyecto.service.Inventario.RecetasService;
import com.example.Proyecto.service.Productos.ProductosService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.PreparedStatementCreator;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.KeyHolder;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("ProduccionService - Pruebas Unitarias")
class ProduccionServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @Mock
    private RecetasService recetasService;

    @Mock
    private IngredientesService ingredientesService;

    @Mock
    private ProductosService productosService;

    @InjectMocks
    private ProduccionService produccionService;

    private Produccion produccionPanIntegral;
    private ProduccionRequest requestValido;
    private RecetaProducto detalleHarina;
    private RecetaProducto detalleAzucar;

    @BeforeEach
    void setUp() {
        produccionPanIntegral = new Produccion(
                1L, 10L,
                new BigDecimal("5.00"),
                LocalDateTime.of(2025, 1, 15, 8, 0)
        );
        produccionPanIntegral.setNombreProducto("Pan Integral");

        requestValido = new ProduccionRequest();
        requestValido.setIdProducto(10L);
        requestValido.setCantidadProducida(new BigDecimal("5.00"));

        // Receta: 2 kg de harina + 0.5 kg de azúcar por unidad producida
        detalleHarina = new RecetaProducto();
        detalleHarina.setIdReceta(1L);
        detalleHarina.setIdProducto(10L);
        detalleHarina.setIdIngrediente(1L);
        detalleHarina.setCantidadRequerida(new BigDecimal("2.00"));
        detalleHarina.setIdUnidad(1L);

        detalleAzucar = new RecetaProducto();
        detalleAzucar.setIdReceta(1L);
        detalleAzucar.setIdProducto(10L);
        detalleAzucar.setIdIngrediente(2L);
        detalleAzucar.setCantidadRequerida(new BigDecimal("0.50"));
        detalleAzucar.setIdUnidad(1L);
    }

    // ─────────────────────────────────────────────
    //  obtenerTodoElHistorial()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerTodoElHistorial - retorna lista con registros de producción")
    void obtenerTodoElHistorial_retornaListaConRegistros() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(produccionPanIntegral));

        List<Produccion> resultado = produccionService.obtenerTodoElHistorial();

        assertNotNull(resultado);
        assertEquals(1, resultado.size());
        assertEquals(1L,             resultado.get(0).getIdProduccion());
        assertEquals(10L,            resultado.get(0).getIdProducto());
        assertEquals("Pan Integral", resultado.get(0).getNombreProducto());
        assertEquals(new BigDecimal("5.00"), resultado.get(0).getCantidadProducida());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodoElHistorial - retorna lista vacía cuando no hay registros")
    void obtenerTodoElHistorial_retornaListaVacia() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<Produccion> resultado = produccionService.obtenerTodoElHistorial();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    @Test
    @DisplayName("obtenerTodoElHistorial - lanza excepción cuando falla la consulta")
    void obtenerTodoElHistorial_lanzaExcepcion_cuandoFallaJdbc() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenThrow(new RuntimeException("Error de BD"));

        assertThrows(RuntimeException.class,
                () -> produccionService.obtenerTodoElHistorial());
    }

    // ─────────────────────────────────────────────
    //  registrarProduccion()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("registrarProduccion - registra producción, descuenta ingredientes y actualiza stock de producto")
    void registrarProduccion_exitoso_descuentaIngredientesYActualizaStockProducto() {
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(List.of(detalleHarina, detalleAzucar));

        // Simula KeyHolder devolviendo ID_PRODUCCION = 42
        doAnswer(invocation -> {
            KeyHolder kh = invocation.getArgument(1);
            java.util.Map<String, Object> keys = new java.util.HashMap<>();
            keys.put("ID_PRODUCCION", 42L);
            kh.getKeyList().add(keys);
            return 1;
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        when(ingredientesService.actualizarStock(anyLong(), any(BigDecimal.class))).thenReturn(1);
        doNothing().when(productosService).actualizarStockProducto(anyLong(), any(BigDecimal.class));

        Long idProduccion = produccionService.registrarProduccion(requestValido);

        assertEquals(42L, idProduccion);

        // Consumo harina: 2.00 * 5.00 = 10.00 (negado → -10.00)
        verify(ingredientesService, times(1))
                .actualizarStock(eq(1L), eq(new BigDecimal("10.00").negate()));

        // Consumo azúcar: 0.50 * 5.00 = 2.50 (negado → -2.50)
        verify(ingredientesService, times(1))
                .actualizarStock(eq(2L), eq(new BigDecimal("2.50").negate()));

        // Stock de producto incrementado en 5.00
        verify(productosService, times(1))
                .actualizarStockProducto(eq(10L), eq(new BigDecimal("5.00")));
    }

    @Test
    @DisplayName("registrarProduccion - lanza IllegalArgumentException cuando el producto no tiene receta")
    void registrarProduccion_lanzaIllegalArgumentException_cuandoSinReceta() {
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(Collections.emptyList());

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> produccionService.registrarProduccion(requestValido));

        assertTrue(excepcion.getMessage().contains("10"));
        verify(jdbcTemplate, never())
                .update(any(PreparedStatementCreator.class), any(KeyHolder.class));
        verifyNoInteractions(ingredientesService);
        verifyNoInteractions(productosService);
    }

    @Test
    @DisplayName("registrarProduccion - lanza excepción cuando falla el INSERT en BD")
    void registrarProduccion_lanzaExcepcion_cuandoFallaInsert() {
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(List.of(detalleHarina));

        doThrow(new RuntimeException("Error al insertar producción"))
                .when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        assertThrows(RuntimeException.class,
                () -> produccionService.registrarProduccion(requestValido));

        verifyNoInteractions(ingredientesService);
        verifyNoInteractions(productosService);
    }

    @Test
    @DisplayName("registrarProduccion - lanza excepción cuando falla el descuento de stock de ingrediente")
    void registrarProduccion_lanzaExcepcion_cuandoFallaDescuentoIngrediente() {
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(List.of(detalleHarina));

        doAnswer(invocation -> {
            KeyHolder kh = invocation.getArgument(1);
            java.util.Map<String, Object> keys = new java.util.HashMap<>();
            keys.put("ID_PRODUCCION", 42L);
            kh.getKeyList().add(keys);
            return 1;
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        when(ingredientesService.actualizarStock(anyLong(), any(BigDecimal.class)))
                .thenThrow(new IllegalStateException("Stock insuficiente para el ingrediente ID 1"));

        IllegalStateException excepcion = assertThrows(IllegalStateException.class,
                () -> produccionService.registrarProduccion(requestValido));

        assertTrue(excepcion.getMessage().contains("Stock insuficiente"));
        verify(productosService, never()).actualizarStockProducto(anyLong(), any(BigDecimal.class));
    }

    @Test
    @DisplayName("registrarProduccion - calcula correctamente el consumo total de cada ingrediente")
    void registrarProduccion_calculaConsumoTotalCorrectamente() {
        // Producir 3 unidades con 1.5 kg de harina cada una → consumo = 4.5 kg
        requestValido.setCantidadProducida(new BigDecimal("3.00"));
        detalleHarina.setCantidadRequerida(new BigDecimal("1.50"));

        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(List.of(detalleHarina));

        doAnswer(invocation -> {
            KeyHolder kh = invocation.getArgument(1);
            java.util.Map<String, Object> keys = new java.util.HashMap<>();
            keys.put("ID_PRODUCCION", 5L);
            kh.getKeyList().add(keys);
            return 1;
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        when(ingredientesService.actualizarStock(anyLong(), any(BigDecimal.class))).thenReturn(1);
        doNothing().when(productosService).actualizarStockProducto(anyLong(), any(BigDecimal.class));

        produccionService.registrarProduccion(requestValido);

        // 1.50 * 3.00 = 4.50 → negado = -4.50
        verify(ingredientesService, times(1))
                .actualizarStock(eq(1L), eq(new BigDecimal("4.50").negate()));
    }

    // ─────────────────────────────────────────────
    //  eliminarProduccion()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("eliminarProduccion - revierte ingredientes, descuenta producto y elimina registro")
    void eliminarProduccion_exitoso_revierteInventarioYElimina() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyLong()))
                .thenReturn(produccionPanIntegral);
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(List.of(detalleHarina, detalleAzucar));
        when(ingredientesService.actualizarStock(anyLong(), any(BigDecimal.class))).thenReturn(1);
        doNothing().when(productosService).actualizarStockProducto(anyLong(), any(BigDecimal.class));
        when(jdbcTemplate.update(anyString(), anyLong())).thenReturn(1);

        assertDoesNotThrow(() -> produccionService.eliminarProduccion(1L));

        // Reposición harina: 2.00 * 5.00 = 10.00 (positivo)
        verify(ingredientesService, times(1))
                .actualizarStock(eq(1L), eq(new BigDecimal("10.00")));

        // Reposición azúcar: 0.50 * 5.00 = 2.50 (positivo)
        verify(ingredientesService, times(1))
                .actualizarStock(eq(2L), eq(new BigDecimal("2.50")));

        // Stock de producto decrementado en -5.00
        verify(productosService, times(1))
                .actualizarStockProducto(eq(10L), eq(new BigDecimal("5.00").negate()));

        // DELETE del registro de producción
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1L));
    }

    @Test
    @DisplayName("eliminarProduccion - lanza IllegalArgumentException cuando la producción no existe")
    void eliminarProduccion_lanzaIllegalArgumentException_cuandoProduccionNoExiste() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyLong()))
                .thenThrow(new EmptyResultDataAccessException(1));

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> produccionService.eliminarProduccion(99L));

        assertTrue(excepcion.getMessage().contains("99"));
        verifyNoInteractions(recetasService);
        verifyNoInteractions(ingredientesService);
        verifyNoInteractions(productosService);
    }

    @Test
    @DisplayName("eliminarProduccion - lanza IllegalStateException cuando el producto no tiene receta al revertir")
    void eliminarProduccion_lanzaIllegalStateException_cuandoProductoSinReceta() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyLong()))
                .thenReturn(produccionPanIntegral);
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(Collections.emptyList());

        IllegalStateException excepcion = assertThrows(IllegalStateException.class,
                () -> produccionService.eliminarProduccion(1L));

        assertTrue(excepcion.getMessage().contains("10"));
        verifyNoInteractions(ingredientesService);
        verifyNoInteractions(productosService);
    }

    @Test
    @DisplayName("eliminarProduccion - lanza IllegalArgumentException cuando el DELETE no afecta filas")
    void eliminarProduccion_lanzaIllegalArgumentException_cuandoDeleteFalla() {
        when(jdbcTemplate.queryForObject(anyString(), any(RowMapper.class), anyLong()))
                .thenReturn(produccionPanIntegral);
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(List.of(detalleHarina));
        when(ingredientesService.actualizarStock(anyLong(), any(BigDecimal.class))).thenReturn(1);
        doNothing().when(productosService).actualizarStockProducto(anyLong(), any(BigDecimal.class));
        when(jdbcTemplate.update(anyString(), anyLong())).thenReturn(0); // DELETE no afecta filas

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> produccionService.eliminarProduccion(1L));

        assertTrue(excepcion.getMessage().contains("1"));
    }

    // ─────────────────────────────────────────────
    //  actualizarParcial()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("actualizarParcial - actualiza cantidadProducida correctamente")
    void actualizarParcial_actualizaCantidadProducida() {
        when(jdbcTemplate.update(anyString())).thenReturn(1);

        Map<String, Object> updates = Map.of("cantidadProducida", new BigDecimal("10.00"));

        assertDoesNotThrow(() -> produccionService.actualizarParcial(1L, updates));

        verify(jdbcTemplate, times(1)).update(anyString());
    }

    @Test
    @DisplayName("actualizarParcial - actualiza idProducto correctamente")
    void actualizarParcial_actualizaIdProducto() {
        when(jdbcTemplate.update(anyString())).thenReturn(1);

        Map<String, Object> updates = Map.of("idProducto", 20L);

        assertDoesNotThrow(() -> produccionService.actualizarParcial(1L, updates));

        verify(jdbcTemplate, times(1)).update(anyString());
    }

    @Test
    @DisplayName("actualizarParcial - lanza IllegalArgumentException cuando la producción no existe")
    void actualizarParcial_lanzaIllegalArgumentException_cuandoProduccionNoExiste() {
        when(jdbcTemplate.update(anyString())).thenReturn(0);

        Map<String, Object> updates = Map.of("cantidadProducida", new BigDecimal("5.00"));

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> produccionService.actualizarParcial(99L, updates));

        assertTrue(excepcion.getMessage().contains("99"));
    }

    @Test
    @DisplayName("actualizarParcial - actualiza múltiples campos a la vez")
    void actualizarParcial_actualizaMultiplesCampos() {
        when(jdbcTemplate.update(anyString())).thenReturn(1);

        Map<String, Object> updates = Map.of(
                "cantidadProducida", new BigDecimal("8.00"),
                "idProducto", 15L
        );

        assertDoesNotThrow(() -> produccionService.actualizarParcial(1L, updates));

        verify(jdbcTemplate, times(1)).update(anyString());
    }
}
