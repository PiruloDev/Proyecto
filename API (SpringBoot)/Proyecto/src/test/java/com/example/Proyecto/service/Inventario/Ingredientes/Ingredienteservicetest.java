package com.example.Proyecto.service.Inventario.Ingredientes;

import com.example.Proyecto.dto.IngredienteDetalleDTO;
import com.example.Proyecto.dto.IngredienteListadoDTO;
import com.example.Proyecto.dto.IngredientesCantidad;
import com.example.Proyecto.model.Ingredientes;
import com.example.Proyecto.service.Ingredientes.IngredientesService;
import jakarta.persistence.EntityManager;
import jakarta.persistence.Query;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.math.BigDecimal;
import java.util.Collections;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("IngredientesService - Pruebas Unitarias")
class IngredientesServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @Mock
    private EntityManager entityManager;

    @Mock
    private Query query;

    @InjectMocks
    private IngredientesService ingredientesService;

    private Ingredientes harina;
    private Ingredientes azucar;

    @BeforeEach
    void setUp() {
        harina = new Ingredientes(1L, 1L, 1L, 1L,
                "Harina de trigo", new BigDecimal("50.00"), "HAR-001");

        azucar = new Ingredientes(2L, 1L, 1L, 1L,
                "Azúcar blanca", new BigDecimal("30.00"), "AZU-001");
    }

    // ─────────────────────────────────────────────
    //  obtenerIngredientes()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerIngredientes - retorna lista de nombres")
    void obtenerIngredientes_retornaListaDeNombres() {
        List<String> nombresEsperados = List.of("Azúcar blanca", "Harina de trigo");

        when(jdbcTemplate.queryForList(anyString(), eq(String.class)))
                .thenReturn(nombresEsperados);

        List<String> resultado = ingredientesService.obtenerIngredientes();

        assertNotNull(resultado);
        assertEquals(2, resultado.size());
        assertEquals("Azúcar blanca", resultado.get(0));
        assertEquals("Harina de trigo", resultado.get(1));

        verify(jdbcTemplate, times(1)).queryForList(anyString(), eq(String.class));
    }

    @Test
    @DisplayName("obtenerIngredientes - retorna lista vacía cuando no hay ingredientes")
    void obtenerIngredientes_retornaListaVacia() {
        when(jdbcTemplate.queryForList(anyString(), eq(String.class)))
                .thenReturn(Collections.emptyList());

        List<String> resultado = ingredientesService.obtenerIngredientes();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    @Test
    @DisplayName("obtenerIngredientes - lanza excepción cuando falla la consulta")
    void obtenerIngredientes_lanzaExcepcion_cuandoFallaJdbc() {
        when(jdbcTemplate.queryForList(anyString(), eq(String.class)))
                .thenThrow(new RuntimeException("Error de BD"));

        assertThrows(RuntimeException.class, () -> ingredientesService.obtenerIngredientes());
    }

    // ─────────────────────────────────────────────
    //  obtenerIngredientesCantidad()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerIngredientesCantidad - retorna lista con cantidades")
    void obtenerIngredientesCantidad_retornaListaConCantidades() {
        IngredientesCantidad ic1 = new IngredientesCantidad();
        ic1.setIdIngrediente(1L);
        ic1.setNombreIngrediente("Harina de trigo");
        ic1.setCantidadIngrediente(new BigDecimal("50.00"));

        IngredientesCantidad ic2 = new IngredientesCantidad();
        ic2.setIdIngrediente(2L);
        ic2.setNombreIngrediente("Azúcar blanca");
        ic2.setCantidadIngrediente(new BigDecimal("30.00"));

        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(ic1, ic2));

        List<IngredientesCantidad> resultado = ingredientesService.obtenerIngredientesCantidad();

        assertNotNull(resultado);
        assertEquals(2, resultado.size());
        assertEquals("Harina de trigo", resultado.get(0).getNombreIngrediente());
        assertEquals(new BigDecimal("50.00"), resultado.get(0).getCantidadIngrediente());
    }

    @Test
    @DisplayName("obtenerIngredientesCantidad - retorna lista vacía")
    void obtenerIngredientesCantidad_retornaListaVacia() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<IngredientesCantidad> resultado = ingredientesService.obtenerIngredientesCantidad();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    // ─────────────────────────────────────────────
    //  obtenerIngredientesParaListado()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerIngredientesParaListado - retorna DTOs con abreviatura de unidad")
    void obtenerIngredientesParaListado_retornaDTOs() {
        IngredienteListadoDTO dto = new IngredienteListadoDTO();
        dto.setIdIngrediente(1L);
        dto.setNombreIngrediente("Harina de trigo");
        dto.setAbreviaturaUnidad("kg");

        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(dto));

        List<IngredienteListadoDTO> resultado = ingredientesService.obtenerIngredientesParaListado();

        assertNotNull(resultado);
        assertEquals(1, resultado.size());
        assertEquals("Harina de trigo", resultado.get(0).getNombreIngrediente());
        assertEquals("kg", resultado.get(0).getAbreviaturaUnidad());
    }

    @Test
    @DisplayName("obtenerIngredientesParaListado - retorna lista vacía")
    void obtenerIngredientesParaListado_retornaListaVacia() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<IngredienteListadoDTO> resultado = ingredientesService.obtenerIngredientesParaListado();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    // ─────────────────────────────────────────────
    //  crearIngrediente()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("crearIngrediente - ejecuta INSERT con cantidad inicial en cero")
    void crearIngrediente_ejecutaInsertConCantidadCero() {
        when(jdbcTemplate.update(anyString(),
                anyLong(), anyLong(), anyLong(), anyString(), anyString(), any(BigDecimal.class)))
                .thenReturn(1);

        assertDoesNotThrow(() -> ingredientesService.crearIngrediente(harina));

        verify(jdbcTemplate, times(1)).update(
                anyString(),
                eq(harina.getIdProveedor()),
                eq(harina.getIdCategoria()),
                eq(harina.getIdUnidadMedida()),
                eq("Harina de trigo"),
                eq("HAR-001"),
                eq(BigDecimal.ZERO)   // siempre inicia en 0
        );
    }

    @Test
    @DisplayName("crearIngrediente - lanza excepción cuando falla el INSERT")
    void crearIngrediente_lanzaExcepcion_cuandoFallaInsert() {
        when(jdbcTemplate.update(anyString(),
                anyLong(), anyLong(), anyLong(), anyString(), anyString(), any(BigDecimal.class)))
                .thenThrow(new RuntimeException("Error al crear ingrediente"));

        assertThrows(RuntimeException.class,
                () -> ingredientesService.crearIngrediente(harina));
    }

    // ─────────────────────────────────────────────
    //  editarIngrediente()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("editarIngrediente - retorna 1 cuando la actualización es exitosa")
    void editarIngrediente_retorna1_cuandoActualizaCorrectamente() {
        when(jdbcTemplate.update(anyString(),
                anyLong(), anyLong(), anyLong(), anyString(), anyString(), anyLong()))
                .thenReturn(1);

        int resultado = ingredientesService.editarIngrediente(1L, harina);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(
                anyString(),
                eq(harina.getIdProveedor()),
                eq(harina.getIdCategoria()),
                eq(harina.getIdUnidadMedida()),
                eq("Harina de trigo"),
                eq("HAR-001"),
                eq(1L)
        );
    }

    @Test
    @DisplayName("editarIngrediente - retorna 0 cuando el ID no existe")
    void editarIngrediente_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(),
                anyLong(), anyLong(), anyLong(), anyString(), anyString(), anyLong()))
                .thenReturn(0);

        int resultado = ingredientesService.editarIngrediente(99L, harina);

        assertEquals(0, resultado);
    }

    @Test
    @DisplayName("editarIngrediente - lanza excepción cuando falla el UPDATE")
    void editarIngrediente_lanzaExcepcion_cuandoFallaUpdate() {
        when(jdbcTemplate.update(anyString(),
                anyLong(), anyLong(), anyLong(), anyString(), anyString(), anyLong()))
                .thenThrow(new RuntimeException("Error al editar ingrediente"));

        assertThrows(RuntimeException.class,
                () -> ingredientesService.editarIngrediente(1L, harina));
    }

    // ─────────────────────────────────────────────
    //  actualizarCantidad()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("actualizarCantidad - retorna 1 cuando la actualización es exitosa")
    void actualizarCantidad_retorna1_cuandoActualizaCorrectamente() {
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);

        int resultado = ingredientesService.actualizarCantidad(1L, new BigDecimal("25.00"));

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq(new BigDecimal("25.00")), eq(1L));
    }

    @Test
    @DisplayName("actualizarCantidad - retorna 0 cuando el ID no existe")
    void actualizarCantidad_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(0);

        int resultado = ingredientesService.actualizarCantidad(99L, new BigDecimal("25.00"));

        assertEquals(0, resultado);
    }

    // ─────────────────────────────────────────────
    //  eliminarIngrediente()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("eliminarIngrediente - retorna 1 cuando la eliminación es exitosa")
    void eliminarIngrediente_retorna1_cuandoEliminaCorrectamente() {
        when(jdbcTemplate.update(anyString(), anyLong())).thenReturn(1);

        int resultado = ingredientesService.eliminarIngrediente(1L);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1L));
    }

    @Test
    @DisplayName("eliminarIngrediente - retorna 0 cuando el ID no existe")
    void eliminarIngrediente_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(), anyLong())).thenReturn(0);

        int resultado = ingredientesService.eliminarIngrediente(99L);

        assertEquals(0, resultado);
    }

    @Test
    @DisplayName("eliminarIngrediente - lanza excepción cuando falla el DELETE")
    void eliminarIngrediente_lanzaExcepcion_cuandoFallaDelete() {
        when(jdbcTemplate.update(anyString(), anyLong()))
                .thenThrow(new RuntimeException("Error al eliminar ingrediente"));

        assertThrows(RuntimeException.class,
                () -> ingredientesService.eliminarIngrediente(1L));
    }

    // ─────────────────────────────────────────────
    //  descontarStock()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("descontarStock - descuenta correctamente cuando hay stock suficiente")
    void descontarStock_descuentaCorrectamente_cuandoHayStockSuficiente() {
        when(jdbcTemplate.queryForObject(anyString(), eq(BigDecimal.class), anyLong()))
                .thenReturn(new BigDecimal("50.00"));
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);

        int resultado = ingredientesService.descontarStock(1L, new BigDecimal("20.00"));

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(),
                eq(new BigDecimal("20.00")), eq(1L));
    }

    @Test
    @DisplayName("descontarStock - lanza IllegalStateException cuando stock es insuficiente")
    void descontarStock_lanzaIllegalStateException_cuandoStockInsuficiente() {
        when(jdbcTemplate.queryForObject(anyString(), eq(BigDecimal.class), anyLong()))
                .thenReturn(new BigDecimal("10.00"));

        IllegalStateException excepcion = assertThrows(IllegalStateException.class,
                () -> ingredientesService.descontarStock(1L, new BigDecimal("20.00")));

        assertTrue(excepcion.getMessage().contains("Stock insuficiente"));
        assertTrue(excepcion.getMessage().contains("10.00"));
        assertTrue(excepcion.getMessage().contains("20.00"));
        verify(jdbcTemplate, never()).update(anyString(), any(BigDecimal.class), anyLong());
    }

    @Test
    @DisplayName("descontarStock - lanza IllegalArgumentException cuando el ingrediente no existe")
    void descontarStock_lanzaIllegalArgumentException_cuandoIngredienteNoExiste() {
        when(jdbcTemplate.queryForObject(anyString(), eq(BigDecimal.class), anyLong()))
                .thenThrow(new EmptyResultDataAccessException(1));

        IllegalArgumentException excepcion = assertThrows(IllegalArgumentException.class,
                () -> ingredientesService.descontarStock(99L, new BigDecimal("10.00")));

        assertTrue(excepcion.getMessage().contains("99"));
        verify(jdbcTemplate, never()).update(anyString(), any(BigDecimal.class), anyLong());
    }

    @Test
    @DisplayName("descontarStock - descuenta exactamente el stock disponible (caso límite)")
    void descontarStock_descuentaExactamenteElStockDisponible() {
        BigDecimal stockActual = new BigDecimal("15.00");

        when(jdbcTemplate.queryForObject(anyString(), eq(BigDecimal.class), anyLong()))
                .thenReturn(stockActual);
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);

        int resultado = ingredientesService.descontarStock(1L, new BigDecimal("15.00"));

        assertEquals(1, resultado);
    }

    // ─────────────────────────────────────────────
    //  reponerStock()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("reponerStock - repone stock correctamente")
    void reponerStock_reponerStockCorrectamente() {
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);

        int resultado = ingredientesService.reponerStock(1L, new BigDecimal("100.00"));

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(),
                eq(new BigDecimal("100.00")), eq(1L));
    }

    @Test
    @DisplayName("reponerStock - retorna 0 cuando el ingrediente no existe")
    void reponerStock_retorna0_cuandoIngredienteNoExiste() {
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(0);

        int resultado = ingredientesService.reponerStock(99L, new BigDecimal("10.00"));

        assertEquals(0, resultado);
    }

    @Test
    @DisplayName("reponerStock - lanza excepción cuando falla el UPDATE")
    void reponerStock_lanzaExcepcion_cuandoFallaUpdate() {
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenThrow(new RuntimeException("Error al reponer stock"));

        assertThrows(RuntimeException.class,
                () -> ingredientesService.reponerStock(1L, new BigDecimal("10.00")));
    }

    // ─────────────────────────────────────────────
    //  actualizarStock() — delega a reponerStock o descontarStock
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("actualizarStock - delega a reponerStock cuando la cantidad es positiva")
    void actualizarStock_delegaAReponerStock_cuandoCantidadPositiva() {
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);

        int resultado = ingredientesService.actualizarStock(1L, new BigDecimal("20.00"));

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(),
                eq(new BigDecimal("20.00")), eq(1L));
    }

    @Test
    @DisplayName("actualizarStock - delega a descontarStock cuando la cantidad es negativa")
    void actualizarStock_delegaADescontarStock_cuandoCantidadNegativa() {
        when(jdbcTemplate.queryForObject(anyString(), eq(BigDecimal.class), anyLong()))
                .thenReturn(new BigDecimal("50.00"));
        when(jdbcTemplate.update(anyString(), any(BigDecimal.class), anyLong()))
                .thenReturn(1);

        int resultado = ingredientesService.actualizarStock(1L, new BigDecimal("-10.00"));

        assertEquals(1, resultado);
    }

    // ─────────────────────────────────────────────
    //  obtenerIngredientesParaModal()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerIngredientesParaModal - retorna lista de DTOs desde EntityManager")
    void obtenerIngredientesParaModal_retornaListaDeDTOs() {
        Object[] fila1 = {1L, "Harina de trigo", "kg", 1L};
        Object[] fila2 = {2L, "Azúcar blanca",   "g",  1L};

        when(entityManager.createNativeQuery(anyString())).thenReturn(query);
        when(query.getResultList()).thenReturn(List.of(fila1, fila2));

        List<IngredienteDetalleDTO> resultado = ingredientesService.obtenerIngredientesParaModal();

        assertNotNull(resultado);
        assertEquals(2, resultado.size());
        assertEquals(1L,             resultado.get(0).getIdIngrediente());
        assertEquals("Harina de trigo", resultado.get(0).getNombreIngrediente());
        assertEquals("kg",           resultado.get(0).getAbreviaturaUnidad());
        assertEquals(1L,             resultado.get(0).getIdUnidad());
    }

    @Test
    @DisplayName("obtenerIngredientesParaModal - retorna lista vacía cuando no hay resultados")
    void obtenerIngredientesParaModal_retornaListaVacia() {
        when(entityManager.createNativeQuery(anyString())).thenReturn(query);
        when(query.getResultList()).thenReturn(Collections.emptyList());

        List<IngredienteDetalleDTO> resultado = ingredientesService.obtenerIngredientesParaModal();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());
    }

    @Test
    @DisplayName("obtenerIngredientesParaModal - lanza excepción cuando falla EntityManager")
    void obtenerIngredientesParaModal_lanzaExcepcion_cuandoFallaEntityManager() {
        when(entityManager.createNativeQuery(anyString()))
                .thenThrow(new RuntimeException("Error de persistencia"));

        assertThrows(RuntimeException.class,
                () -> ingredientesService.obtenerIngredientesParaModal());
    }
}
