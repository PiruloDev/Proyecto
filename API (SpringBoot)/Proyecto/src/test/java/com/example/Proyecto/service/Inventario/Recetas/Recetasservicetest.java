package com.example.Proyecto.service.Inventario.Recetas;

import com.example.Proyecto.dto.RecetaDetalleDTO;
import com.example.Proyecto.dto.RecetaRequest;
import com.example.Proyecto.model.RecetaProducto;
import com.example.Proyecto.service.Inventario.RecetasService;
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
import java.util.Collections;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("RecetasService - Pruebas Unitarias")
class RecetasServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private RecetasService recetasService;

    private RecetaProducto recetaProducto1;
    private RecetaProducto recetaProducto2;
    private RecetaRequest requestConIngredientes;
    private RecetaRequest requestSinIngredientes;

    @BeforeEach
    void setUp() {
        recetaProducto1 = new RecetaProducto();
        recetaProducto1.setIdReceta(1L);
        recetaProducto1.setIdProducto(10L);
        recetaProducto1.setIdIngrediente(1L);
        recetaProducto1.setCantidadRequerida(new BigDecimal("2.50"));
        recetaProducto1.setIdUnidad(1L);

        recetaProducto2 = new RecetaProducto();
        recetaProducto2.setIdReceta(1L);
        recetaProducto2.setIdProducto(10L);
        recetaProducto2.setIdIngrediente(2L);
        recetaProducto2.setCantidadRequerida(new BigDecimal("1.00"));
        recetaProducto2.setIdUnidad(2L);

        // Request con dos ingredientes
        RecetaRequest.IngredienteReceta ing1 = new RecetaRequest.IngredienteReceta();
        ing1.setIdIngrediente(1L);
        ing1.setCantidadNecesaria(new BigDecimal("2.50"));
        ing1.setIdUnidad(1L);

        RecetaRequest.IngredienteReceta ing2 = new RecetaRequest.IngredienteReceta();
        ing2.setIdIngrediente(2L);
        ing2.setCantidadNecesaria(new BigDecimal("1.00"));
        ing2.setIdUnidad(2L);

        requestConIngredientes = new RecetaRequest();
        requestConIngredientes.setIdProducto(10L);
        requestConIngredientes.setIngredientes(List.of(ing1, ing2));

        // Request sin ingredientes
        requestSinIngredientes = new RecetaRequest();
        requestSinIngredientes.setIdProducto(10L);
        requestSinIngredientes.setIngredientes(Collections.emptyList());
    }

    // ─────────────────────────────────────────────
    //  obtenerTodasLasRecetas()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerTodasLasRecetas - retorna lista con múltiples recetas")
    void obtenerTodasLasRecetas_retornaListaConRecetas() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(recetaProducto1, recetaProducto2));

        List<RecetaProducto> resultado = recetasService.obtenerTodasLasRecetas();

        assertNotNull(resultado);
        assertEquals(2, resultado.size());
        assertEquals(1L,  resultado.get(0).getIdReceta());
        assertEquals(10L, resultado.get(0).getIdProducto());
        assertEquals(1L,  resultado.get(0).getIdIngrediente());
        assertEquals(new BigDecimal("2.50"), resultado.get(0).getCantidadRequerida());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodasLasRecetas - retorna lista vacía cuando no hay recetas")
    void obtenerTodasLasRecetas_retornaListaVacia() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<RecetaProducto> resultado = recetasService.obtenerTodasLasRecetas();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    @Test
    @DisplayName("obtenerTodasLasRecetas - lanza excepción cuando falla la consulta")
    void obtenerTodasLasRecetas_lanzaExcepcion_cuandoFallaJdbc() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenThrow(new RuntimeException("Error de BD"));

        assertThrows(RuntimeException.class,
                () -> recetasService.obtenerTodasLasRecetas());
    }

    // ─────────────────────────────────────────────
    //  obtenerRecetaPorIdProducto()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerRecetaPorIdProducto - retorna lista de ingredientes para el producto")
    void obtenerRecetaPorIdProducto_retornaIngredientes() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyLong()))
                .thenReturn(List.of(recetaProducto1, recetaProducto2));

        List<RecetaProducto> resultado = recetasService.obtenerRecetaPorIdProducto(10L);

        assertNotNull(resultado);
        assertEquals(2, resultado.size());
        assertEquals(10L, resultado.get(0).getIdProducto());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class), eq(10L));
    }

    @Test
    @DisplayName("obtenerRecetaPorIdProducto - retorna lista vacía cuando el producto no tiene receta")
    void obtenerRecetaPorIdProducto_retornaListaVacia_cuandoNoTieneReceta() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyLong()))
                .thenReturn(Collections.emptyList());

        List<RecetaProducto> resultado = recetasService.obtenerRecetaPorIdProducto(99L);

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    // ─────────────────────────────────────────────
    //  crearReceta()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("crearReceta - lanza IllegalArgumentException cuando idProducto es nulo")
    void crearReceta_lanzaIllegalArgumentException_cuandoIdProductoEsNulo() {
        RecetaRequest requestNulo = new RecetaRequest();
        requestNulo.setIdProducto(null);

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> recetasService.crearReceta(requestNulo));

        assertEquals("El ID de producto es obligatorio para crear una receta.",
                excepcion.getMessage());

        verifyNoInteractions(jdbcTemplate);
    }

    @Test
    @DisplayName("crearReceta - inserta encabezado y detalles cuando hay ingredientes")
    void crearReceta_insertaEncabezadoYDetalles_cuandoHayIngredientes() {
        // Mock del KeyHolder: simula que el INSERT devuelve ID = 100
        doAnswer(invocation -> {
            KeyHolder kh = invocation.getArgument(1);
            // Usamos reflexión para setear la clave generada simulada
            java.util.Map<String, Object> keys = new java.util.HashMap<>();
            keys.put("ID_RECETA", 100L);
            kh.getKeyList().add(keys);
            return 1;
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        when(jdbcTemplate.update(anyString(), anyLong(), anyLong(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);

        assertDoesNotThrow(() -> recetasService.crearReceta(requestConIngredientes));

        // Verificar INSERT del encabezado
        verify(jdbcTemplate, times(1))
                .update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        // Verificar INSERT de los 2 detalles
        verify(jdbcTemplate, times(2))
                .update(anyString(), anyLong(), anyLong(), any(BigDecimal.class), anyLong());
    }

    @Test
    @DisplayName("crearReceta - solo inserta encabezado cuando la lista de ingredientes está vacía")
    void crearReceta_soloInsertaEncabezado_cuandoListaVacia() {
        doAnswer(invocation -> {
            KeyHolder kh = invocation.getArgument(1);
            java.util.Map<String, Object> keys = new java.util.HashMap<>();
            keys.put("ID_RECETA", 100L);
            kh.getKeyList().add(keys);
            return 1;
        }).when(jdbcTemplate).update(any(PreparedStatementCreator.class), any(KeyHolder.class));

        assertDoesNotThrow(() -> recetasService.crearReceta(requestSinIngredientes));

        verify(jdbcTemplate, times(1))
                .update(any(PreparedStatementCreator.class), any(KeyHolder.class));
        // No debe insertar ningún detalle
        verify(jdbcTemplate, never())
                .update(anyString(), anyLong(), anyLong(), any(BigDecimal.class), anyLong());
    }

    // ─────────────────────────────────────────────
    //  actualizarReceta()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("actualizarReceta - borra detalles antiguos e inserta nuevos correctamente")
    void actualizarReceta_borraBDetallesEInsertaNuevos() {
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), anyLong()))
                .thenReturn(1L);  // ID_RECETA existente
        when(jdbcTemplate.update(anyString(), anyLong()))
                .thenReturn(2);   // DELETE de detalles
        when(jdbcTemplate.update(anyString(), anyLong(), anyLong(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);   // INSERT de nuevos detalles

        assertDoesNotThrow(() -> recetasService.actualizarReceta(10L, requestConIngredientes));

        // Verifica SELECT del ID_RECETA
        verify(jdbcTemplate, times(1))
                .queryForObject(anyString(), eq(Long.class), eq(10L));
        // Verifica DELETE de detalles
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1L));
        // Verifica INSERT de los 2 nuevos detalles
        verify(jdbcTemplate, times(2))
                .update(anyString(), anyLong(), anyLong(), any(BigDecimal.class), anyLong());
    }

    @Test
    @DisplayName("actualizarReceta - lanza IllegalArgumentException cuando el producto no tiene receta")
    void actualizarReceta_lanzaIllegalArgumentException_cuandoProductoNoTieneReceta() {
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), anyLong()))
                .thenThrow(new EmptyResultDataAccessException(1));

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> recetasService.actualizarReceta(99L, requestConIngredientes));

        assertTrue(excepcion.getMessage().contains("99"));
        // No debe ejecutar DELETE ni INSERT
        verify(jdbcTemplate, never()).update(anyString(), anyLong());
    }

    @Test
    @DisplayName("actualizarReceta - solo borra detalles cuando la lista de ingredientes está vacía")
    void actualizarReceta_soloBorraDetalles_cuandoListaVacia() {
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), anyLong()))
                .thenReturn(1L);
        when(jdbcTemplate.update(anyString(), anyLong())).thenReturn(2);

        assertDoesNotThrow(() -> recetasService.actualizarReceta(10L, requestSinIngredientes));

        verify(jdbcTemplate, times(1)).update(anyString(), eq(1L));
        verify(jdbcTemplate, never())
                .update(anyString(), anyLong(), anyLong(), any(BigDecimal.class), anyLong());
    }

    // ─────────────────────────────────────────────
    //  eliminarReceta()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("eliminarReceta - elimina detalles y encabezado correctamente")
    void eliminarReceta_eliminaDetallesYEncabezado() {
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), anyLong()))
                .thenReturn(1L);
        // Primera llamada: DELETE detalles → 2 filas
        // Segunda llamada: DELETE encabezado → 1 fila
        when(jdbcTemplate.update(anyString(), anyLong()))
                .thenReturn(2)
                .thenReturn(1);

        assertDoesNotThrow(() -> recetasService.eliminarReceta(10L));

        verify(jdbcTemplate, times(1))
                .queryForObject(anyString(), eq(Long.class), eq(10L));
        verify(jdbcTemplate, times(2)).update(anyString(), anyLong());
    }

    @Test
    @DisplayName("eliminarReceta - lanza IllegalArgumentException cuando el producto no tiene receta")
    void eliminarReceta_lanzaIllegalArgumentException_cuandoProductoNoTieneReceta() {
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), anyLong()))
                .thenThrow(new EmptyResultDataAccessException(1));

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> recetasService.eliminarReceta(99L));

        assertTrue(excepcion.getMessage().contains("99"));
        verify(jdbcTemplate, never()).update(anyString(), anyLong());
    }

    @Test
    @DisplayName("eliminarReceta - lanza IllegalArgumentException cuando el DELETE del encabezado no afecta filas")
    void eliminarReceta_lanzaIllegalArgumentException_cuandoDeleteEncabezadoFalla() {
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), anyLong()))
                .thenReturn(1L);
        // Primera llamada: DELETE detalles → 2 filas
        // Segunda llamada: DELETE encabezado → 0 filas (no eliminó nada)
        when(jdbcTemplate.update(anyString(), anyLong()))
                .thenReturn(2)
                .thenReturn(0);

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> recetasService.eliminarReceta(10L));

        assertTrue(excepcion.getMessage().contains("10"));
    }

    // ─────────────────────────────────────────────
    //  obtenerTodasLasRecetasOptimizadas()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerTodasLasRecetasOptimizadas - retorna lista de DTOs con nombres")
    void obtenerTodasLasRecetasOptimizadas_retornaListaDeDTOs() {
        RecetaDetalleDTO dto = new RecetaDetalleDTO();
        dto.setIdReceta(1L);
        dto.setIdProducto(10L);
        dto.setNombreProducto("Pan Integral");
        dto.setIdIngrediente(1L);
        dto.setNombreIngrediente("Harina de trigo");
        dto.setCantidadRequerida(new BigDecimal("2.50"));
        dto.setNombreUnidad("Kilogramo");

        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(dto));

        List<RecetaDetalleDTO> resultado = recetasService.obtenerTodasLasRecetasOptimizadas();

        assertNotNull(resultado);
        assertEquals(1, resultado.size());
        assertEquals("Pan Integral",    resultado.get(0).getNombreProducto());
        assertEquals("Harina de trigo", resultado.get(0).getNombreIngrediente());
        assertEquals("Kilogramo",       resultado.get(0).getNombreUnidad());
        assertEquals(new BigDecimal("2.50"), resultado.get(0).getCantidadRequerida());
    }

    @Test
    @DisplayName("obtenerTodasLasRecetasOptimizadas - retorna lista vacía")
    void obtenerTodasLasRecetasOptimizadas_retornaListaVacia() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<RecetaDetalleDTO> resultado = recetasService.obtenerTodasLasRecetasOptimizadas();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    // ─────────────────────────────────────────────
    //  obtenerRecetaOptimizadaPorProducto()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerRecetaOptimizadaPorProducto - retorna DTOs para el producto dado")
    void obtenerRecetaOptimizadaPorProducto_retornaDTOs() {
        RecetaDetalleDTO dto = new RecetaDetalleDTO();
        dto.setIdProducto(10L);
        dto.setNombreProducto("Pan Integral");
        dto.setNombreIngrediente("Harina de trigo");
        dto.setIdUnidad(1L);
        dto.setNombreUnidad("Kilogramo");

        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyLong()))
                .thenReturn(List.of(dto));

        List<RecetaDetalleDTO> resultado = recetasService.obtenerRecetaOptimizadaPorProducto(10L);

        assertNotNull(resultado);
        assertEquals(1, resultado.size());
        assertEquals(10L, resultado.get(0).getIdProducto());
        assertEquals("Pan Integral", resultado.get(0).getNombreProducto());

        verify(jdbcTemplate, times(1))
                .query(anyString(), any(RowMapper.class), eq(10L));
    }

    @Test
    @DisplayName("obtenerRecetaOptimizadaPorProducto - retorna lista vacía cuando producto sin receta")
    void obtenerRecetaOptimizadaPorProducto_retornaListaVacia_cuandoSinReceta() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), anyLong()))
                .thenReturn(Collections.emptyList());

        List<RecetaDetalleDTO> resultado =
                recetasService.obtenerRecetaOptimizadaPorProducto(99L);

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }
}