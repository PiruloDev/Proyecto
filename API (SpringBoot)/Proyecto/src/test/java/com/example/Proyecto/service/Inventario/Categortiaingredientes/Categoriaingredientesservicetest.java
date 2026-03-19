package com.example.Proyecto.service.Inventario.Categortiaingredientes;

import com.example.Proyecto.service.CategoriaIngredientesService.CategoriaIngredientes;
import com.example.Proyecto.service.CategoriaIngredientesService.CategoriaIngredientesService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.util.Collections;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("CategoriaIngredientesService - Pruebas Unitarias")
class CategoriaIngredientesServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private CategoriaIngredientesService categoriaIngredientesService;

    private CategoriaIngredientes categoriaHarinas;
    private CategoriaIngredientes categoriaLacteos;

    @BeforeEach
    void setUp() {
        categoriaHarinas = new CategoriaIngredientes();
        categoriaHarinas.setIdCategoriaIngrediente(1);
        categoriaHarinas.setNombreCategoria("Harinas");

        categoriaLacteos = new CategoriaIngredientes();
        categoriaLacteos.setIdCategoriaIngrediente(2);
        categoriaLacteos.setNombreCategoria("Lácteos");
    }

    // ─────────────────────────────────────────────
    //  obtenerTodasLasCategoriasIngredientes()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerTodas - retorna lista con múltiples categorías")
    void obtenerTodas_retornaListaConCategorias() {
        List<CategoriaIngredientes> listaEsperada = List.of(categoriaHarinas, categoriaLacteos);

        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(listaEsperada);

        List<CategoriaIngredientes> resultado = categoriaIngredientesService.obtenerTodasLasCategoriasIngredientes();

        assertNotNull(resultado);
        assertEquals(2, resultado.size());
        assertEquals("Harinas", resultado.get(0).getNombreCategoria());
        assertEquals(1, resultado.get(0).getIdCategoriaIngrediente());
        assertEquals("Lácteos", resultado.get(1).getNombreCategoria());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodas - retorna lista vacía cuando no hay categorías")
    void obtenerTodas_retornaListaVacia_cuandoNoHayCategorias() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<CategoriaIngredientes> resultado = categoriaIngredientesService.obtenerTodasLasCategoriasIngredientes();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodas - lanza excepción cuando falla la consulta a BD")
    void obtenerTodas_lanzaExcepcion_cuandoFallaJdbc() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenThrow(new RuntimeException("Error de conexión a la base de datos"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> categoriaIngredientesService.obtenerTodasLasCategoriasIngredientes());

        assertEquals("Error de conexión a la base de datos", excepcion.getMessage());
        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    // ─────────────────────────────────────────────
    //  crearCategoriaIngrediente()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("crearCategoria - ejecuta INSERT correctamente")
    void crearCategoria_ejecutaInsertCorrectamente() {
        when(jdbcTemplate.update(anyString(), anyString())).thenReturn(1);

        assertDoesNotThrow(() ->
                categoriaIngredientesService.crearCategoriaIngrediente(categoriaHarinas));

        verify(jdbcTemplate, times(1)).update(anyString(), eq("Harinas"));
    }

    @Test
    @DisplayName("crearCategoria - lanza excepción cuando falla el INSERT")
    void crearCategoria_lanzaExcepcion_cuandoFallaInsert() {
        when(jdbcTemplate.update(anyString(), anyString()))
                .thenThrow(new RuntimeException("Error al insertar en BD"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> categoriaIngredientesService.crearCategoriaIngrediente(categoriaHarinas));

        assertEquals("Error al insertar en BD", excepcion.getMessage());
        verify(jdbcTemplate, times(1)).update(anyString(), eq("Harinas"));
    }

    @Test
    @DisplayName("crearCategoria - solo interactúa con jdbcTemplate una vez")
    void crearCategoria_soloUnaInteraccionConJdbc() {
        when(jdbcTemplate.update(anyString(), anyString())).thenReturn(1);

        categoriaIngredientesService.crearCategoriaIngrediente(categoriaHarinas);

        verify(jdbcTemplate, times(1)).update(anyString(), anyString());
        verifyNoMoreInteractions(jdbcTemplate);
    }

    // ─────────────────────────────────────────────
    //  editarCategoriaIngrediente()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("editarCategoria - retorna 1 cuando la actualización es exitosa")
    void editarCategoria_retorna1_cuandoActualizaCorrectamente() {
        when(jdbcTemplate.update(anyString(), anyString(), anyInt())).thenReturn(1);

        int resultado = categoriaIngredientesService.editarCategoriaIngrediente(categoriaHarinas);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq("Harinas"), eq(1));
    }

    @Test
    @DisplayName("editarCategoria - retorna 0 cuando el ID no existe")
    void editarCategoria_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(), anyString(), anyInt())).thenReturn(0);

        int resultado = categoriaIngredientesService.editarCategoriaIngrediente(categoriaHarinas);

        assertEquals(0, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq("Harinas"), eq(1));
    }

    @Test
    @DisplayName("editarCategoria - lanza excepción cuando falla el UPDATE")
    void editarCategoria_lanzaExcepcion_cuandoFallaUpdate() {
        when(jdbcTemplate.update(anyString(), anyString(), anyInt()))
                .thenThrow(new RuntimeException("Error al actualizar en BD"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> categoriaIngredientesService.editarCategoriaIngrediente(categoriaHarinas));

        assertEquals("Error al actualizar en BD", excepcion.getMessage());
    }

    // ─────────────────────────────────────────────
    //  eliminarCategoriaIngrediente()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("eliminarCategoria - retorna 1 cuando la eliminación es exitosa")
    void eliminarCategoria_retorna1_cuandoEliminaCorrectamente() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(1);

        int resultado = categoriaIngredientesService.eliminarCategoriaIngrediente(1);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1));
    }

    @Test
    @DisplayName("eliminarCategoria - retorna 0 cuando el ID no existe")
    void eliminarCategoria_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(0);

        int resultado = categoriaIngredientesService.eliminarCategoriaIngrediente(99);

        assertEquals(0, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq(99));
    }

    @Test
    @DisplayName("eliminarCategoria - lanza excepción cuando falla el DELETE")
    void eliminarCategoria_lanzaExcepcion_cuandoFallaDelete() {
        when(jdbcTemplate.update(anyString(), anyInt()))
                .thenThrow(new RuntimeException("Error al eliminar en BD"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> categoriaIngredientesService.eliminarCategoriaIngrediente(1));

        assertEquals("Error al eliminar en BD", excepcion.getMessage());
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1));
    }

    @Test
    @DisplayName("eliminarCategoria - solo interactúa con jdbcTemplate una vez")
    void eliminarCategoria_soloUnaInteraccionConJdbc() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(1);

        categoriaIngredientesService.eliminarCategoriaIngrediente(1);

        verify(jdbcTemplate, times(1)).update(anyString(), anyInt());
        verifyNoMoreInteractions(jdbcTemplate);
    }
}