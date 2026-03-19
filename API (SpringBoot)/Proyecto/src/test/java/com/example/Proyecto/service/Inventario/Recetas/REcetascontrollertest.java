package com.example.Proyecto.service.Inventario.Recetas;

import com.example.Proyecto.controller.RecetasController;
import com.example.Proyecto.dto.RecetaDetalleDTO;
import com.example.Proyecto.dto.RecetaRequest;
import com.example.Proyecto.model.RecetaProducto;
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
import java.util.Collections;
import java.util.List;

import static org.hamcrest.Matchers.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(RecetasController.class)
@DisplayName("RecetasController - Pruebas de Integración Web")
class RecetasControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private RecetasService recetasService;

    @Autowired
    private ObjectMapper objectMapper;

    private RecetaProducto recetaProducto;
    private RecetaDetalleDTO recetaDetalleDTO;
    private RecetaRequest recetaRequest;

    @BeforeEach
    void setUp() {
        recetaProducto = new RecetaProducto();
        recetaProducto.setIdReceta(1L);
        recetaProducto.setIdProducto(10L);
        recetaProducto.setIdIngrediente(1L);
        recetaProducto.setCantidadRequerida(new BigDecimal("2.50"));
        recetaProducto.setIdUnidad(1L);

        recetaDetalleDTO = new RecetaDetalleDTO();
        recetaDetalleDTO.setIdReceta(1L);
        recetaDetalleDTO.setIdProducto(10L);
        recetaDetalleDTO.setNombreProducto("Pan Integral");
        recetaDetalleDTO.setIdIngrediente(1L);
        recetaDetalleDTO.setNombreIngrediente("Harina de trigo");
        recetaDetalleDTO.setCantidadRequerida(new BigDecimal("2.50"));
        recetaDetalleDTO.setNombreUnidad("Kilogramo");

        RecetaRequest.IngredienteReceta ing = new RecetaRequest.IngredienteReceta();
        ing.setIdIngrediente(1L);
        ing.setCantidadNecesaria(new BigDecimal("2.50"));
        ing.setIdUnidad(1L);

        recetaRequest = new RecetaRequest();
        recetaRequest.setIdProducto(10L);
        recetaRequest.setIngredientes(List.of(ing));
    }

    // ─────────────────────────────────────────────
    //  GET /inventario/recetas
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /inventario/recetas - retorna 200 con lista de recetas")
    @WithMockUser
    void obtenerTodasLasRecetas_retorna200_conLista() throws Exception {
        when(recetasService.obtenerTodasLasRecetas())
                .thenReturn(List.of(recetaProducto));

        mockMvc.perform(get("/inventario/recetas"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].idReceta",         is(1)))
                .andExpect(jsonPath("$[0].idProducto",       is(10)))
                .andExpect(jsonPath("$[0].idIngrediente",    is(1)))
                .andExpect(jsonPath("$[0].idUnidad",         is(1)));

        verify(recetasService, times(1)).obtenerTodasLasRecetas();
    }

    @Test
    @DisplayName("GET /inventario/recetas - retorna 200 con lista vacía")
    @WithMockUser
    void obtenerTodasLasRecetas_retorna200_conListaVacia() throws Exception {
        when(recetasService.obtenerTodasLasRecetas()).thenReturn(Collections.emptyList());

        mockMvc.perform(get("/inventario/recetas"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(0)));
    }

    @Test
    @DisplayName("GET /inventario/recetas - retorna 401 sin autenticación")
    void obtenerTodasLasRecetas_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(get("/inventario/recetas"))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(recetasService);
    }

    // ─────────────────────────────────────────────
    //  GET /inventario/recetas/producto/{idProducto}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /inventario/recetas/producto/{id} - retorna 200 cuando existe receta")
    @WithMockUser
    void obtenerRecetaPorProducto_retorna200_cuandoExiste() throws Exception {
        when(recetasService.obtenerRecetaPorIdProducto(10L))
                .thenReturn(List.of(recetaProducto));

        mockMvc.perform(get("/inventario/recetas/producto/10"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].idProducto", is(10)));

        verify(recetasService, times(1)).obtenerRecetaPorIdProducto(10L);
    }

    @Test
    @DisplayName("GET /inventario/recetas/producto/{id} - retorna 404 cuando no existe receta")
    @WithMockUser
    void obtenerRecetaPorProducto_retorna404_cuandoNoExiste() throws Exception {
        when(recetasService.obtenerRecetaPorIdProducto(99L))
                .thenReturn(Collections.emptyList());

        mockMvc.perform(get("/inventario/recetas/producto/99"))
                .andExpect(status().isNotFound());

        verify(recetasService, times(1)).obtenerRecetaPorIdProducto(99L);
    }

    // ─────────────────────────────────────────────
    //  POST /inventario/recetas
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("POST /inventario/recetas - retorna 201 con mensaje de éxito")
    @WithMockUser
    void crearReceta_retorna201_conMensajeExito() throws Exception {
        doNothing().when(recetasService).crearReceta(any());

        mockMvc.perform(post("/inventario/recetas")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(recetaRequest)))
                .andExpect(status().isCreated())
                .andExpect(jsonPath("$.mensaje", containsString("10")));

        verify(recetasService, times(1)).crearReceta(any());
    }

    @Test
    @DisplayName("POST /inventario/recetas - retorna 400 cuando idProducto es nulo")
    @WithMockUser
    void crearReceta_retorna400_cuandoIdProductoNulo() throws Exception {
        doThrow(new IllegalArgumentException("El ID de producto es obligatorio para crear una receta."))
                .when(recetasService).crearReceta(any());

        mockMvc.perform(post("/inventario/recetas")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(recetaRequest)))
                .andExpect(status().isBadRequest())
                .andExpect(jsonPath("$.error", containsString("obligatorio")));
    }

    @Test
    @DisplayName("POST /inventario/recetas - retorna 500 cuando ocurre error inesperado")
    @WithMockUser
    void crearReceta_retorna500_cuandoErrorInesperado() throws Exception {
        doThrow(new RuntimeException("Error de BD"))
                .when(recetasService).crearReceta(any());

        mockMvc.perform(post("/inventario/recetas")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(recetaRequest)))
                .andExpect(status().isInternalServerError())
                .andExpect(jsonPath("$.error", containsString("Error interno")));
    }

    @Test
    @DisplayName("POST /inventario/recetas - retorna 401 sin autenticación")
    void crearReceta_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(post("/inventario/recetas")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(recetaRequest)))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(recetasService);
    }

    // ─────────────────────────────────────────────
    //  PUT /inventario/recetas/producto/{idProducto}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("PUT /inventario/recetas/producto/{id} - retorna 200 cuando receta existe")
    @WithMockUser
    void actualizarReceta_retorna200_cuandoExiste() throws Exception {
        doNothing().when(recetasService).actualizarReceta(anyLong(), any());

        mockMvc.perform(put("/inventario/recetas/producto/10")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(recetaRequest)))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.mensaje", containsString("10")));

        verify(recetasService, times(1)).actualizarReceta(eq(10L), any());
    }

    @Test
    @DisplayName("PUT /inventario/recetas/producto/{id} - retorna 404 cuando receta no existe")
    @WithMockUser
    void actualizarReceta_retorna404_cuandoNoExiste() throws Exception {
        doThrow(new IllegalArgumentException("No se encontró receta para el producto ID 99"))
                .when(recetasService).actualizarReceta(anyLong(), any());

        mockMvc.perform(put("/inventario/recetas/producto/99")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(recetaRequest)))
                .andExpect(status().isNotFound())
                .andExpect(jsonPath("$.error", containsString("99")));
    }

    @Test
    @DisplayName("PUT /inventario/recetas/producto/{id} - retorna 500 cuando error inesperado")
    @WithMockUser
    void actualizarReceta_retorna500_cuandoErrorInesperado() throws Exception {
        doThrow(new RuntimeException("Error de BD"))
                .when(recetasService).actualizarReceta(anyLong(), any());

        mockMvc.perform(put("/inventario/recetas/producto/10")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(recetaRequest)))
                .andExpect(status().isInternalServerError())
                .andExpect(jsonPath("$.error", containsString("Error interno")));
    }

    // ─────────────────────────────────────────────
    //  DELETE /inventario/recetas/{idProducto}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("DELETE /inventario/recetas/{id} - retorna 200 cuando receta existe")
    @WithMockUser
    void eliminarReceta_retorna200_cuandoExiste() throws Exception {
        doNothing().when(recetasService).eliminarReceta(anyLong());

        mockMvc.perform(delete("/inventario/recetas/10")
                        .with(csrf()))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.mensaje", containsString("10")));

        verify(recetasService, times(1)).eliminarReceta(10L);
    }

    @Test
    @DisplayName("DELETE /inventario/recetas/{id} - retorna 404 cuando receta no existe")
    @WithMockUser
    void eliminarReceta_retorna404_cuandoNoExiste() throws Exception {
        doThrow(new IllegalArgumentException("No se encontró receta para el producto ID 99"))
                .when(recetasService).eliminarReceta(anyLong());

        mockMvc.perform(delete("/inventario/recetas/99")
                        .with(csrf()))
                .andExpect(status().isNotFound())
                .andExpect(jsonPath("$.error", containsString("99")));
    }

    @Test
    @DisplayName("DELETE /inventario/recetas/{id} - retorna 500 cuando error inesperado")
    @WithMockUser
    void eliminarReceta_retorna500_cuandoErrorInesperado() throws Exception {
        doThrow(new RuntimeException("Error de BD"))
                .when(recetasService).eliminarReceta(anyLong());

        mockMvc.perform(delete("/inventario/recetas/10")
                        .with(csrf()))
                .andExpect(status().isInternalServerError())
                .andExpect(jsonPath("$.error", containsString("Error interno")));
    }

    @Test
    @DisplayName("DELETE /inventario/recetas/{id} - retorna 401 sin autenticación")
    void eliminarReceta_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(delete("/inventario/recetas/10").with(csrf()))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(recetasService);
    }

    // ─────────────────────────────────────────────
    //  GET /inventario/recetas/optimizadas
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /inventario/recetas/optimizadas - retorna 200 con DTOs completos")
    @WithMockUser
    void obtenerTodasOptimizadas_retorna200_conDTOs() throws Exception {
        when(recetasService.obtenerTodasLasRecetasOptimizadas())
                .thenReturn(List.of(recetaDetalleDTO));

        mockMvc.perform(get("/inventario/recetas/optimizadas"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].nombreProducto",    is("Pan Integral")))
                .andExpect(jsonPath("$[0].nombreIngrediente", is("Harina de trigo")))
                .andExpect(jsonPath("$[0].nombreUnidad",      is("Kilogramo")));

        verify(recetasService, times(1)).obtenerTodasLasRecetasOptimizadas();
    }

    @Test
    @DisplayName("GET /inventario/recetas/optimizadas - retorna 200 con lista vacía")
    @WithMockUser
    void obtenerTodasOptimizadas_retorna200_conListaVacia() throws Exception {
        when(recetasService.obtenerTodasLasRecetasOptimizadas())
                .thenReturn(Collections.emptyList());

        mockMvc.perform(get("/inventario/recetas/optimizadas"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(0)));
    }

    // ─────────────────────────────────────────────
    //  GET /inventario/recetas/optimizadas/producto/{idProducto}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /inventario/recetas/optimizadas/producto/{id} - retorna 200 cuando existe")
    @WithMockUser
    void obtenerRecetaOptimizadaPorProducto_retorna200_cuandoExiste() throws Exception {
        when(recetasService.obtenerRecetaOptimizadaPorProducto(10L))
                .thenReturn(List.of(recetaDetalleDTO));

        mockMvc.perform(get("/inventario/recetas/optimizadas/producto/10"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$[0].idProducto",    is(10)))
                .andExpect(jsonPath("$[0].nombreProducto", is("Pan Integral")));

        verify(recetasService, times(1)).obtenerRecetaOptimizadaPorProducto(10L);
    }

    @Test
    @DisplayName("GET /inventario/recetas/optimizadas/producto/{id} - retorna 404 cuando no existe")
    @WithMockUser
    void obtenerRecetaOptimizadaPorProducto_retorna404_cuandoNoExiste() throws Exception {
        when(recetasService.obtenerRecetaOptimizadaPorProducto(99L))
                .thenReturn(Collections.emptyList());

        mockMvc.perform(get("/inventario/recetas/optimizadas/producto/99"))
                .andExpect(status().isNotFound());

        verify(recetasService, times(1)).obtenerRecetaOptimizadaPorProducto(99L);
    }
}