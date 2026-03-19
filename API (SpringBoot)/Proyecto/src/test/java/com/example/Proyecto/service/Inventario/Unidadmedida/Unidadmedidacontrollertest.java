package com.example.Proyecto.service.Inventario.Unidadmedida;

import com.example.Proyecto.controller.UnidadMedidaController;
import com.example.Proyecto.model.UnidadMedida;
import com.example.Proyecto.service.UnidadMedida.UnidadMedidaService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.context.annotation.Import;
import org.springframework.http.MediaType;
import org.springframework.security.test.context.support.WithMockUser;
import org.springframework.test.web.servlet.MockMvc;

import java.util.Collections;
import java.util.List;

import static org.hamcrest.Matchers.*;
import static org.mockito.Mockito.*;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.get;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(UnidadMedidaController.class)
@DisplayName("UnidadMedidaController - Pruebas de Integración Web")
class UnidadMedidaControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
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
    //  GET /unidades-medida
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /unidades-medida - retorna 200 con lista de unidades")
    @WithMockUser
    void obtenerTodas_retorna200_conListaDeUnidades() throws Exception {
        when(unidadMedidaService.obtenerTodas())
                .thenReturn(List.of(unidadKilogramo, unidadLitro));

        mockMvc.perform(get("/unidades-medida")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(content().contentType(MediaType.APPLICATION_JSON))
                .andExpect(jsonPath("$", hasSize(2)))
                .andExpect(jsonPath("$[0].idUnidad", is(1)))
                .andExpect(jsonPath("$[0].nombreUnidad", is("Kilogramo")))
                .andExpect(jsonPath("$[0].abreviaturaUnidad", is("kg")))
                .andExpect(jsonPath("$[1].idUnidad", is(2)))
                .andExpect(jsonPath("$[1].nombreUnidad", is("Litro")))
                .andExpect(jsonPath("$[1].abreviaturaUnidad", is("L")));

        verify(unidadMedidaService, times(1)).obtenerTodas();
    }

    @Test
    @DisplayName("GET /unidades-medida - retorna 200 con lista vacía cuando no hay unidades")
    @WithMockUser
    void obtenerTodas_retorna200_conListaVacia() throws Exception {
        when(unidadMedidaService.obtenerTodas())
                .thenReturn(Collections.emptyList());

        mockMvc.perform(get("/unidades-medida")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(content().contentType(MediaType.APPLICATION_JSON))
                .andExpect(jsonPath("$", hasSize(0)));

        verify(unidadMedidaService, times(1)).obtenerTodas();
    }

    @Test
    @DisplayName("GET /unidades-medida - retorna 200 con una sola unidad")
    @WithMockUser
    void obtenerTodas_retorna200_conUnaUnidad() throws Exception {
        when(unidadMedidaService.obtenerTodas())
                .thenReturn(List.of(unidadKilogramo));

        mockMvc.perform(get("/unidades-medida")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].nombreUnidad", is("Kilogramo")));

        verify(unidadMedidaService, times(1)).obtenerTodas();
    }

    @Test
    @DisplayName("GET /unidades-medida - retorna 500 cuando el servicio lanza excepción inesperada")
    @WithMockUser
    void obtenerTodas_retorna500_cuandoServicioLanzaExcepcion() throws Exception {
        when(unidadMedidaService.obtenerTodas())
                .thenThrow(new RuntimeException("Error inesperado en base de datos"));

        mockMvc.perform(get("/unidades-medida")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isInternalServerError());

        verify(unidadMedidaService, times(1)).obtenerTodas();
    }

    @Test
    @DisplayName("GET /unidades-medida - retorna 401 cuando el usuario no está autenticado")
    void obtenerTodas_retorna401_cuandoNoEstaAutenticado() throws Exception {
        mockMvc.perform(get("/unidades-medida")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(unidadMedidaService);
    }
}