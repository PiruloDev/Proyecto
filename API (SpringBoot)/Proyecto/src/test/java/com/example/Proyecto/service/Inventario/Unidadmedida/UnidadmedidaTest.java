package com.example.Proyecto.service.Inventario.Unidadmedida;



import com.example.Proyecto.model.UnidadMedida;
import com.example.Proyecto.service.UnidadMedida.UnidadMedidaService;
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
@DisplayName("UnidadMedidaService - Pruebas Unitarias")
class UnidadMedidaServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private UnidadMedidaService unidadMedidaService;

    private UnidadMedida unidadKilogramo;
    private UnidadMedida unidadLitro;

    @BeforeEach
    void setUp() {
        unidadKilogramo = new UnidadMedida();
        unidadKilogramo.setIdUnidad(1L);
        unidadKilogramo.setNombreUnidad("Kilogramo");
        unidadKilogramo.setAbreviaturaUnidad("kg");

        unidadLitro = new UnidadMedida();
        unidadLitro.setIdUnidad(2L);
        unidadLitro.setNombreUnidad("Litro");
        unidadLitro.setAbreviaturaUnidad("L");
    }

    // ─────────────────────────────────────────────
    //  obtenerTodas()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerTodas - retorna lista con múltiples unidades")
    void obtenerTodas_retornaListaConUnidades() {
        List<UnidadMedida> listaEsperada = List.of(unidadKilogramo, unidadLitro);

        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(listaEsperada);

        List<UnidadMedida> resultado = unidadMedidaService.obtenerTodas();

        assertNotNull(resultado, "La lista no debe ser nula");
        assertEquals(2, resultado.size(), "Debe retornar exactamente 2 unidades");
        assertEquals("Kilogramo", resultado.get(0).getNombreUnidad());
        assertEquals("kg", resultado.get(0).getAbreviaturaUnidad());
        assertEquals(1L, resultado.get(0).getIdUnidad());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodas - retorna lista vacía cuando no hay unidades registradas")
    void obtenerTodas_retornaListaVacia_cuandoNoHayUnidades() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<UnidadMedida> resultado = unidadMedidaService.obtenerTodas();

        assertNotNull(resultado, "La lista no debe ser nula");
        assertTrue(resultado.isEmpty(), "La lista debe estar vacía");

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodas - retorna lista con una sola unidad")
    void obtenerTodas_retornaUnaUnidad() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(unidadKilogramo));

        List<UnidadMedida> resultado = unidadMedidaService.obtenerTodas();

        assertEquals(1, resultado.size());
        assertEquals(1L, resultado.get(0).getIdUnidad());
        assertEquals("Kilogramo", resultado.get(0).getNombreUnidad());
        assertEquals("kg", resultado.get(0).getAbreviaturaUnidad());
    }

    @Test
    @DisplayName("obtenerTodas - lanza excepción cuando falla la consulta a BD")
    void obtenerTodas_lanzaExcepcion_cuandoFallaJdbc() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenThrow(new RuntimeException("Error de conexión a la base de datos"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> unidadMedidaService.obtenerTodas());

        assertEquals("Error de conexión a la base de datos", excepcion.getMessage());
        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodas - verifica que se ejecuta exactamente una consulta SQL")
    void obtenerTodas_ejecutaExactamenteUnaConsultaSQL() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(unidadKilogramo));

        unidadMedidaService.obtenerTodas();

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
        verifyNoMoreInteractions(jdbcTemplate);
    }

    @Test
    @DisplayName("obtenerTodas - los campos de cada unidad no son nulos")
    void obtenerTodas_camposDeCadaUnidadNoSonNulos() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(unidadKilogramo, unidadLitro));

        List<UnidadMedida> resultado = unidadMedidaService.obtenerTodas();

        resultado.forEach(u -> {
            assertNotNull(u.getIdUnidad(), "El id no debe ser nulo");
            assertNotNull(u.getNombreUnidad(), "El nombre no debe ser nulo");
            assertNotNull(u.getAbreviaturaUnidad(), "La abreviatura no debe ser nula");
        });
    }
}
