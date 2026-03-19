package com.example.Proyecto.service.Inventario.Ingredientes;

import com.example.Proyecto.controller.Ingredientescontroller;
import com.example.Proyecto.dto.IngredienteDetalleDTO;
import com.example.Proyecto.dto.IngredienteListadoDTO;
import com.example.Proyecto.dto.IngredientesCantidad;
import com.example.Proyecto.dto.IngresoStockRequest;
import com.example.Proyecto.model.Ingredientes;
import com.example.Proyecto.service.Ingredientes.IngredientesService;
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
import java.util.Map;

import static org.hamcrest.Matchers.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(Ingredientescontroller.class)
@DisplayName("IngredientesController - Pruebas de Integración Web")
class IngredientescontrollerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private IngredientesService ingredientesService;

    @Autowired
    private ObjectMapper objectMapper;

    private Ingredientes harina;
    private IngredientesCantidad harinaCantidad;
    private IngredienteListadoDTO harinaListado;
    private IngredienteDetalleDTO harinaDetalle;

    @BeforeEach
    void setUp() {
        harina = new Ingredientes(1L, 1L, 1L, 1L,
                "Harina de trigo", new BigDecimal("50.00"), "HAR-001");

        harinaCantidad = new IngredientesCantidad();
        harinaCantidad.setIdIngrediente(1L);
        harinaCantidad.setNombreIngrediente("Harina de trigo");
        harinaCantidad.setCantidadIngrediente(new BigDecimal("50.00"));

        harinaListado = new IngredienteListadoDTO();
        harinaListado.setIdIngrediente(1L);
        harinaListado.setNombreIngrediente("Harina de trigo");
        harinaListado.setAbreviaturaUnidad("kg");

        harinaDetalle = new IngredienteDetalleDTO(1L, "Harina de trigo", "kg", 1L);
    }

    // ─────────────────────────────────────────────
    //  GET /ingredientes
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /ingredientes - retorna 200 con lista de nombres")
    @WithMockUser
    void obtenerIngredientes_retorna200_conListaDeNombres() throws Exception {
        when(ingredientesService.obtenerIngredientes())
                .thenReturn(List.of("Azúcar blanca", "Harina de trigo"));

        mockMvc.perform(get("/ingredientes")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(2)))
                .andExpect(jsonPath("$[0]", is("Azúcar blanca")))
                .andExpect(jsonPath("$[1]", is("Harina de trigo")));

        verify(ingredientesService, times(1)).obtenerIngredientes();
    }

    @Test
    @DisplayName("GET /ingredientes - retorna 200 con lista vacía")
    @WithMockUser
    void obtenerIngredientes_retorna200_conListaVacia() throws Exception {
        when(ingredientesService.obtenerIngredientes()).thenReturn(Collections.emptyList());

        mockMvc.perform(get("/ingredientes"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(0)));
    }

    @Test
    @DisplayName("GET /ingredientes - retorna 401 sin autenticación")
    void obtenerIngredientes_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(get("/ingredientes"))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(ingredientesService);
    }

    // ─────────────────────────────────────────────
    //  GET /ingredientes/cantidad
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /ingredientes/cantidad - retorna 200 con lista de cantidades")
    @WithMockUser
    void obtenerIngredientesCantidad_retorna200_conCantidades() throws Exception {
        when(ingredientesService.obtenerIngredientesCantidad())
                .thenReturn(List.of(harinaCantidad));

        mockMvc.perform(get("/ingredientes/cantidad"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].idIngrediente",      is(1)))
                .andExpect(jsonPath("$[0].nombreIngrediente",  is("Harina de trigo")))
                .andExpect(jsonPath("$[0].cantidadIngrediente", is(50.00)));

        verify(ingredientesService, times(1)).obtenerIngredientesCantidad();
    }

    @Test
    @DisplayName("GET /ingredientes/cantidad - retorna 500 cuando el servicio lanza excepción")
    @WithMockUser
    void obtenerIngredientesCantidad_retorna500_cuandoFallaServicio() throws Exception {
        when(ingredientesService.obtenerIngredientesCantidad())
                .thenThrow(new RuntimeException("Error de BD"));

        mockMvc.perform(get("/ingredientes/cantidad"))
                .andExpect(status().isInternalServerError());
    }

    // ─────────────────────────────────────────────
    //  GET /ingredientes/lista
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /ingredientes/lista - retorna 200 con lista de DTOs")
    @WithMockUser
    void obtenerIngredientesListas_retorna200_conListaDeDTOs() throws Exception {
        when(ingredientesService.obtenerIngredientesParaListado())
                .thenReturn(List.of(harinaListado));

        mockMvc.perform(get("/ingredientes/lista"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].nombreIngrediente", is("Harina de trigo")))
                .andExpect(jsonPath("$[0].abreviaturaUnidad", is("kg")));

        verify(ingredientesService, times(1)).obtenerIngredientesParaListado();
    }

    // ─────────────────────────────────────────────
    //  POST /crearingrediente
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("POST /crearingrediente - retorna 200 con mensaje de éxito")
    @WithMockUser
    void crearIngrediente_retorna200_conMensajeExito() throws Exception {
        doNothing().when(ingredientesService).crearIngrediente(any());

        mockMvc.perform(post("/crearingrediente")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(harina)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("Harina de trigo")))
                .andExpect(content().string(containsString("creado con éxito")));

        verify(ingredientesService, times(1)).crearIngrediente(any());
    }

    @Test
    @DisplayName("POST /crearingrediente - retorna 401 sin autenticación")
    void crearIngrediente_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(post("/crearingrediente")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(harina)))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(ingredientesService);
    }

    // ─────────────────────────────────────────────
    //  PUT /ingrediente/{id}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("PUT /ingrediente/{id} - retorna 200 cuando el ingrediente existe")
    @WithMockUser
    void editarIngrediente_retorna200_cuandoExiste() throws Exception {
        when(ingredientesService.editarIngrediente(anyLong(), any()))
                .thenReturn(1);

        mockMvc.perform(put("/ingrediente/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(harina)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("actualizado correctamente")));
    }

    @Test
    @DisplayName("PUT /ingrediente/{id} - retorna 200 con mensaje de no encontrado cuando ID no existe")
    @WithMockUser
    void editarIngrediente_retornaMensajeNoEncontrado_cuandoIdNoExiste() throws Exception {
        when(ingredientesService.editarIngrediente(anyLong(), any()))
                .thenReturn(0);

        mockMvc.perform(put("/ingrediente/99")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(harina)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("No se encontró")));
    }

    // ─────────────────────────────────────────────
    //  PATCH /{id}/cantidad
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("PATCH /{id}/cantidad - retorna 200 con cantidad numérica válida")
    @WithMockUser
    void patchCantidad_retorna200_conCantidadNumerica() throws Exception {
        when(ingredientesService.actualizarCantidad(anyLong(), any()))
                .thenReturn(1);

        mockMvc.perform(patch("/1/cantidad")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"cantidadIngrediente\": 25.5}"))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("Cantidad actualizada")));
    }

    @Test
    @DisplayName("PATCH /{id}/cantidad - retorna mensaje de no encontrado cuando ID no existe")
    @WithMockUser
    void patchCantidad_retornaMensajeNoEncontrado_cuandoIdNoExiste() throws Exception {
        when(ingredientesService.actualizarCantidad(anyLong(), any()))
                .thenReturn(0);

        mockMvc.perform(patch("/99/cantidad")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"cantidadIngrediente\": 10}"))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("No se encontró")));
    }

    @Test
    @DisplayName("PATCH /{id}/cantidad - retorna mensaje de error cuando no se envía la cantidad")
    @WithMockUser
    void patchCantidad_retornaMensajeError_cuandoNoSeEnviaCantidad() throws Exception {
        mockMvc.perform(patch("/1/cantidad")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content("{\"otrocampo\": 10}"))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("No se envió la cantidad")));

        verifyNoInteractions(ingredientesService);
    }

    // ─────────────────────────────────────────────
    //  DELETE /ingrediente/{id}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("DELETE /ingrediente/{id} - retorna 200 cuando el ingrediente existe")
    @WithMockUser
    void eliminarIngrediente_retorna200_cuandoExiste() throws Exception {
        when(ingredientesService.eliminarIngrediente(anyLong())).thenReturn(1);

        mockMvc.perform(delete("/ingrediente/1")
                        .with(csrf()))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("eliminado correctamente")));

        verify(ingredientesService, times(1)).eliminarIngrediente(1L);
    }

    @Test
    @DisplayName("DELETE /ingrediente/{id} - retorna mensaje de no encontrado cuando ID no existe")
    @WithMockUser
    void eliminarIngrediente_retornaMensajeNoEncontrado_cuandoIdNoExiste() throws Exception {
        when(ingredientesService.eliminarIngrediente(anyLong())).thenReturn(0);

        mockMvc.perform(delete("/ingrediente/99")
                        .with(csrf()))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("No se encontró")));
    }

    @Test
    @DisplayName("DELETE /ingrediente/{id} - retorna 401 sin autenticación")
    void eliminarIngrediente_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(delete("/ingrediente/1").with(csrf()))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(ingredientesService);
    }

    // ─────────────────────────────────────────────
    //  POST /ingredientes/{id}/ingreso
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("POST /ingredientes/{id}/ingreso - retorna 200 con cantidad válida")
    @WithMockUser
    void ingresarStock_retorna200_conCantidadValida() throws Exception {
        when(ingredientesService.reponerStock(anyLong(), any()))
                .thenReturn(1);

        IngresoStockRequest request = new IngresoStockRequest();
        request.setCantidadIngresada(new BigDecimal("50.00"));

        mockMvc.perform(post("/ingredientes/1/ingreso")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(request)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("registrado con éxito")));

        verify(ingredientesService, times(1)).reponerStock(eq(1L), any());
    }

    @Test
    @DisplayName("POST /ingredientes/{id}/ingreso - retorna 400 con cantidad cero")
    @WithMockUser
    void ingresarStock_retorna400_conCantidadCero() throws Exception {
        IngresoStockRequest request = new IngresoStockRequest();
        request.setCantidadIngresada(BigDecimal.ZERO);

        mockMvc.perform(post("/ingredientes/1/ingreso")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(request)))
                .andExpect(status().isBadRequest())
                .andExpect(content().string(containsString("positiva")));

        verifyNoInteractions(ingredientesService);
    }

    @Test
    @DisplayName("POST /ingredientes/{id}/ingreso - retorna 400 con cantidad negativa")
    @WithMockUser
    void ingresarStock_retorna400_conCantidadNegativa() throws Exception {
        IngresoStockRequest request = new IngresoStockRequest();
        request.setCantidadIngresada(new BigDecimal("-10.00"));

        mockMvc.perform(post("/ingredientes/1/ingreso")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(request)))
                .andExpect(status().isBadRequest());

        verifyNoInteractions(ingredientesService);
    }

    @Test
    @DisplayName("POST /ingredientes/{id}/ingreso - retorna 404 cuando el ingrediente no existe")
    @WithMockUser
    void ingresarStock_retorna404_cuandoIngredienteNoExiste() throws Exception {
        when(ingredientesService.reponerStock(anyLong(), any()))
                .thenReturn(0);

        IngresoStockRequest request = new IngresoStockRequest();
        request.setCantidadIngresada(new BigDecimal("10.00"));

        mockMvc.perform(post("/ingredientes/99/ingreso")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(request)))
                .andExpect(status().isNotFound())
                .andExpect(content().string(containsString("no encontrado")));
    }

    // ─────────────────────────────────────────────
    //  GET /recetas/lista-modal
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /recetas/lista-modal - retorna 200 con lista de DTOs para modal")
    @WithMockUser
    void listaModal_retorna200_conDTOs() throws Exception {
        when(ingredientesService.obtenerIngredientesParaModal())
                .thenReturn(List.of(harinaDetalle));

        mockMvc.perform(get("/recetas/lista-modal"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(1)))
                .andExpect(jsonPath("$[0].idIngrediente",     is(1)))
                .andExpect(jsonPath("$[0].nombreIngrediente", is("Harina de trigo")))
                .andExpect(jsonPath("$[0].abreviaturaUnidad", is("kg")));

        verify(ingredientesService, times(1)).obtenerIngredientesParaModal();
    }

    @Test
    @DisplayName("GET /recetas/lista-modal - retorna 401 sin autenticación")
    void listaModal_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(get("/recetas/lista-modal"))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(ingredientesService);
    }
}