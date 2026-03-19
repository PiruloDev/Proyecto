package com.example.Proyecto.service.Inventario.Categortiaingredientes;

import com.example.Proyecto.controller.CategoriaIngredientesController;
import com.example.Proyecto.service.CategoriaIngredientesService.CategoriaIngredientes;
import com.example.Proyecto.service.CategoriaIngredientesService.CategoriaIngredientesService;
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

import java.util.Collections;
import java.util.List;

import static org.hamcrest.Matchers.*;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyInt;
import static org.mockito.Mockito.*;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(CategoriaIngredientesController.class)
@DisplayName("CategoriaIngredientesController - Pruebas de Integración Web")
class CategoriaIngredientesControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private CategoriaIngredientesService categoriaIngredientesService;

    @Autowired
    private ObjectMapper objectMapper;

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
    //  GET /categorias/ingredientes
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /categorias/ingredientes - retorna 200 con lista de categorías")
    @WithMockUser
    void obtenerTodas_retorna200_conListaDeCategorias() throws Exception {
        when(categoriaIngredientesService.obtenerTodasLasCategoriasIngredientes())
                .thenReturn(List.of(categoriaHarinas, categoriaLacteos));

        mockMvc.perform(get("/categorias/ingredientes")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(content().contentType(MediaType.APPLICATION_JSON))
                .andExpect(jsonPath("$", hasSize(2)))
                .andExpect(jsonPath("$[0].idCategoriaIngrediente", is(1)))
                .andExpect(jsonPath("$[0].nombreCategoria", is("Harinas")))
                .andExpect(jsonPath("$[1].idCategoriaIngrediente", is(2)))
                .andExpect(jsonPath("$[1].nombreCategoria", is("Lácteos")));

        verify(categoriaIngredientesService, times(1)).obtenerTodasLasCategoriasIngredientes();
    }

    @Test
    @DisplayName("GET /categorias/ingredientes - retorna 200 con lista vacía")
    @WithMockUser
    void obtenerTodas_retorna200_conListaVacia() throws Exception {
        when(categoriaIngredientesService.obtenerTodasLasCategoriasIngredientes())
                .thenReturn(Collections.emptyList());

        mockMvc.perform(get("/categorias/ingredientes")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(0)));

        verify(categoriaIngredientesService, times(1)).obtenerTodasLasCategoriasIngredientes();
    }

    @Test
    @DisplayName("GET /categorias/ingredientes - retorna 401 sin autenticación")
    void obtenerTodas_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(get("/categorias/ingredientes"))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(categoriaIngredientesService);
    }

    // ─────────────────────────────────────────────
    //  POST /nuevacategoriaingrediente
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("POST /nuevacategoriaingrediente - retorna 200 con mensaje de éxito")
    @WithMockUser
    void crearCategoria_retorna200_conMensajeExito() throws Exception {
        doNothing().when(categoriaIngredientesService).crearCategoriaIngrediente(any(CategoriaIngredientes.class));

        mockMvc.perform(post("/nuevacategoriaingrediente")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(categoriaHarinas)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("Harinas")))
                .andExpect(content().string(containsString("creada con éxito")));

        verify(categoriaIngredientesService, times(1)).crearCategoriaIngrediente(any(CategoriaIngredientes.class));
    }

    @Test
    @DisplayName("POST /nuevacategoriaingrediente - retorna 401 sin autenticación")
    void crearCategoria_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(post("/nuevacategoriaingrediente")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(categoriaHarinas)))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(categoriaIngredientesService);
    }

    // ─────────────────────────────────────────────
    //  PUT /categoriaingrediente/{id}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("PUT /categoriaingrediente/{id} - retorna 200 cuando la categoría existe")
    @WithMockUser
    void editarCategoria_retorna200_cuandoCategoriaExiste() throws Exception {
        when(categoriaIngredientesService.editarCategoriaIngrediente(any(CategoriaIngredientes.class)))
                .thenReturn(1);

        mockMvc.perform(put("/categoriaingrediente/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(categoriaHarinas)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("actualizada correctamente")));

        verify(categoriaIngredientesService, times(1)).editarCategoriaIngrediente(any(CategoriaIngredientes.class));
    }

    @Test
    @DisplayName("PUT /categoriaingrediente/{id} - retorna 404 cuando el ID no existe")
    @WithMockUser
    void editarCategoria_retorna404_cuandoIdNoExiste() throws Exception {
        when(categoriaIngredientesService.editarCategoriaIngrediente(any(CategoriaIngredientes.class)))
                .thenReturn(0);

        mockMvc.perform(put("/categoriaingrediente/99")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(categoriaHarinas)))
                .andExpect(status().isNotFound());

        verify(categoriaIngredientesService, times(1)).editarCategoriaIngrediente(any(CategoriaIngredientes.class));
    }

    @Test
    @DisplayName("PUT /categoriaingrediente/{id} - retorna 401 sin autenticación")
    void editarCategoria_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(put("/categoriaingrediente/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(categoriaHarinas)))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(categoriaIngredientesService);
    }

    // ─────────────────────────────────────────────
    //  DELETE /eliminarcategoria/{id}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("DELETE /eliminarcategoria/{id} - retorna 200 cuando la categoría existe")
    @WithMockUser
    void eliminarCategoria_retorna200_cuandoCategoriaExiste() throws Exception {
        when(categoriaIngredientesService.eliminarCategoriaIngrediente(anyInt()))
                .thenReturn(1);

        mockMvc.perform(delete("/eliminarcategoria/1")
                        .with(csrf()))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("eliminada correctamente")));

        verify(categoriaIngredientesService, times(1)).eliminarCategoriaIngrediente(1);
    }

    @Test
    @DisplayName("DELETE /eliminarcategoria/{id} - retorna 404 cuando el ID no existe")
    @WithMockUser
    void eliminarCategoria_retorna404_cuandoIdNoExiste() throws Exception {
        when(categoriaIngredientesService.eliminarCategoriaIngrediente(anyInt()))
                .thenReturn(0);

        mockMvc.perform(delete("/eliminarcategoria/99")
                        .with(csrf()))
                .andExpect(status().isNotFound());

        verify(categoriaIngredientesService, times(1)).eliminarCategoriaIngrediente(99);
    }

    @Test
    @DisplayName("DELETE /eliminarcategoria/{id} - retorna 401 sin autenticación")
    void eliminarCategoria_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(delete("/eliminarcategoria/1")
                        .with(csrf()))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(categoriaIngredientesService);
    }
}