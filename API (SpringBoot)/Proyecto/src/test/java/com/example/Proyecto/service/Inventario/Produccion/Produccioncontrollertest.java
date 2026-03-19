package com.example.Proyecto.service.Inventario.Produccion;

import com.example.Proyecto.controller.InventarioController;
import com.example.Proyecto.dto.ProduccionRequest;
import com.example.Proyecto.model.Produccion;
import com.example.Proyecto.service.Inventario.ProduccionService;
import com.example.Proyecto.service.Inventario.RecetasService;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.http.MediaType;
import org.springframework.security.test.context.support.WithMockUser;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.Collections;
import java.util.List;

import static org.hamcrest.Matchers.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(InventarioController.class)
@DisplayName("InventarioController - Pruebas de Integración Web")
class InventarioControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private ProduccionService produccionService;

    @MockitoBean
    private RecetasService recetasService;

    @Autowired
    private ObjectMapper objectMapper;

    private Produccion produccionPanIntegral;
    private ProduccionRequest requestValido;
    private ProduccionRequest requestIdNulo;
    private ProduccionRequest requestCantidadCero;

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

        requestIdNulo = new ProduccionRequest();
        requestIdNulo.setIdProducto(null);
        requestIdNulo.setCantidadProducida(new BigDecimal("5.00"));

        requestCantidadCero = new ProduccionRequest();
        requestCantidadCero.setIdProducto(10L);
        requestCantidadCero.setCantidadProducida(BigDecimal.ZERO);
    }

    // ─────────────────────────────────────────────
    //  GET /inventario/produccion
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /inventario/produccion - retorna 200 con historial de producciones")
    @WithMockUser
    void obtenerTodoElHistorial_retorna200_conHistorial() throws Exception {
        when(produccionService.obtenerTodoElHistorial())
                .thenReturn(List.of(produccionPanIntegral));

        mockMvc.perform(get("/inventario/produccion"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].idProduccion",    is(1)))
                .andExpect(jsonPath("$[0].idProducto",      is(10)))
                .andExpect(jsonPath("$[0].nombreProducto",  is("Pan Integral")));

        verify(produccionService, times(1)).obtenerTodoElHistorial();
    }

    @Test
    @DisplayName("GET /inventario/produccion - retorna 204 cuando no hay registros")
    @WithMockUser
    void obtenerTodoElHistorial_retorna204_cuandoListaVacia() throws Exception {
        when(produccionService.obtenerTodoElHistorial())
                .thenReturn(Collections.emptyList());

        mockMvc.perform(get("/inventario/produccion"))
                .andExpect(status().isNoContent());

        verify(produccionService, times(1)).obtenerTodoElHistorial();
    }

    @Test
    @DisplayName("GET /inventario/produccion - retorna 401 sin autenticación")
    void obtenerTodoElHistorial_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(get("/inventario/produccion"))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(produccionService);
    }

    // ─────────────────────────────────────────────
    //  POST /inventario/produccion
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("POST /inventario/produccion - retorna 201 con id de producción generado")
    @WithMockUser
    void registrarProduccion_retorna201_conIdProduccion() throws Exception {
        when(produccionService.registrarProduccion(any())).thenReturn(42L);

        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestValido)))
                .andExpect(status().isCreated())
                .andExpect(jsonPath("$.idProduccion",  is(42)))
                .andExpect(jsonPath("$.mensaje",       containsString("éxito")))
                .andExpect(jsonPath("$.status",        is(201)));

        verify(produccionService, times(1)).registrarProduccion(any());
    }

    @Test
    @DisplayName("POST /inventario/produccion - retorna 400 cuando idProducto es nulo")
    @WithMockUser
    void registrarProduccion_retorna400_cuandoIdProductoNulo() throws Exception {
        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestIdNulo)))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.error", containsString("obligatorio")))
                .andExpect(jsonPath("$.status", is(400)));

        verifyNoInteractions(produccionService);
    }

    @Test
    @DisplayName("POST /inventario/produccion - retorna 400 cuando cantidadProducida es cero")
    @WithMockUser
    void registrarProduccion_retorna400_cuandoCantidadCero() throws Exception {
        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestCantidadCero)))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.error", containsString("positivo")));

        verifyNoInteractions(produccionService);
    }

    @Test
    @DisplayName("POST /inventario/produccion - retorna 400 cuando cantidadProducida es negativa")
    @WithMockUser
    void registrarProduccion_retorna400_cuandoCantidadNegativa() throws Exception {
        ProduccionRequest requestNegativo = new ProduccionRequest();
        requestNegativo.setIdProducto(10L);
        requestNegativo.setCantidadProducida(new BigDecimal("-3.00"));

        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestNegativo)))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.error", containsString("positivo")));

        verifyNoInteractions(produccionService);
    }

    @Test
    @DisplayName("POST /inventario/produccion - retorna 400 cuando el servicio lanza IllegalStateException (stock insuficiente)")
    @WithMockUser
    void registrarProduccion_retorna400_cuandoStockInsuficiente() throws Exception {
        when(produccionService.registrarProduccion(any()))
                .thenThrow(new IllegalStateException("Stock insuficiente para el ingrediente ID 1"));

        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestValido)))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.error", containsString("Error de inventario")));
    }

    @Test
    @DisplayName("POST /inventario/produccion - retorna 400 cuando el servicio lanza IllegalArgumentException (sin receta)")
    @WithMockUser
    void registrarProduccion_retorna400_cuandoSinReceta() throws Exception {
        when(produccionService.registrarProduccion(any()))
                .thenThrow(new IllegalArgumentException("No se encontró receta para el producto ID 10"));

        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestValido)))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.error", containsString("receta")));
    }

    @Test
    @DisplayName("POST /inventario/produccion - retorna 500 cuando error inesperado")
    @WithMockUser
    void registrarProduccion_retorna500_cuandoErrorInesperado() throws Exception {
        when(produccionService.registrarProduccion(any()))
                .thenThrow(new RuntimeException("Error inesperado de BD"));

        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestValido)))
                .andExpect(status().isInternalServerError())
                .andExpect(jsonPath("$.error", containsString("Error inesperado")));
    }

    @Test
    @DisplayName("POST /inventario/produccion - retorna 401 sin autenticación")
    void registrarProduccion_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(post("/inventario/produccion")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(requestValido)))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(produccionService);
    }

    // ─────────────────────────────────────────────
    //  DELETE /inventario/produccion/{idProduccion}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("DELETE /inventario/produccion/{id} - retorna 204 cuando se elimina correctamente")
    @WithMockUser
    void eliminarProduccion_retorna204_cuandoEliminaCorrectamente() throws Exception {
        doNothing().when(produccionService).eliminarProduccion(anyLong());

        mockMvc.perform(delete("/inventario/produccion/1")
                        .with(csrf()))
                .andExpect(status().isNoContent());

        verify(produccionService, times(1)).eliminarProduccion(1L);
    }

    @Test
    @DisplayName("DELETE /inventario/produccion/{id} - retorna 404 cuando la producción no existe")
    @WithMockUser
    void eliminarProduccion_retorna404_cuandoProduccionNoExiste() throws Exception {
        doThrow(new IllegalArgumentException("Producción con ID 99 no encontrada."))
                .when(produccionService).eliminarProduccion(anyLong());

        mockMvc.perform(delete("/inventario/produccion/99")
                        .with(csrf()))
                .andExpect(status().isNotFound())
                .andExpect(jsonPath("$.error", containsString("99")))
                .andExpect(jsonPath("$.status", is(404)));
    }

    @Test
    @DisplayName("DELETE /inventario/produccion/{id} - retorna 400 cuando falla la reversión de inventario")
    @WithMockUser
    void eliminarProduccion_retorna400_cuandoFallaReversionInventario() throws Exception {
        doThrow(new IllegalStateException("No se puede revertir el inventario"))
                .when(produccionService).eliminarProduccion(anyLong());

        mockMvc.perform(delete("/inventario/produccion/1")
                        .with(csrf()))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.error", containsString("reversión")));
    }

    @Test
    @DisplayName("DELETE /inventario/produccion/{id} - retorna 500 cuando error inesperado")
    @WithMockUser
    void eliminarProduccion_retorna500_cuandoErrorInesperado() throws Exception {
        doThrow(new RuntimeException("Error inesperado"))
                .when(produccionService).eliminarProduccion(anyLong());

        mockMvc.perform(delete("/inventario/produccion/1")
                        .with(csrf()))
                .andExpect(status().isInternalServerError())
                .andExpect(jsonPath("$.error", containsString("Error inesperado")));
    }

    @Test
    @DisplayName("DELETE /inventario/produccion/{id} - retorna 401 sin autenticación")
    void eliminarProduccion_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(delete("/inventario/produccion/1").with(csrf()))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(produccionService);
    }
}