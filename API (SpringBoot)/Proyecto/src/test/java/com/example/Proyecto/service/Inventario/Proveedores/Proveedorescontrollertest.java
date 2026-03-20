package com.example.Proyecto.service.Inventario.Proveedores;


import com.example.Proyecto.controller.ProveedoresController;
import com.example.Proyecto.service.Proveedores.Proveedores;
import com.example.Proyecto.service.Proveedores.ProveedoresService;
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

@WebMvcTest(ProveedoresController.class)
@DisplayName("ProveedoresController - Pruebas de Integración Web")
class ProveedoresControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private ProveedoresService proveedoresService;

    @Autowired
    private ObjectMapper objectMapper;

    private Proveedores proveedorActivo;
    private Proveedores proveedorInactivo;

    @BeforeEach
    void setUp() {
        proveedorActivo = new Proveedores();
        proveedorActivo.setIdProveedor(1);
        proveedorActivo.setNombreProv("Harinera del Valle");
        proveedorActivo.setTelefonoProv("3001234567");
        proveedorActivo.setActivoProv(true);
        proveedorActivo.setEmailProv("contacto@harinera.com");
        proveedorActivo.setDireccionProv("Calle 10 #5-23, Cali");

        proveedorInactivo = new Proveedores();
        proveedorInactivo.setIdProveedor(2);
        proveedorInactivo.setNombreProv("Lácteos El Campo");
        proveedorInactivo.setTelefonoProv("3119876543");
        proveedorInactivo.setActivoProv(false);
        proveedorInactivo.setEmailProv("ventas@lacteos.com");
        proveedorInactivo.setDireccionProv("Carrera 8 #12-45, Bogotá");
    }

    // ─────────────────────────────────────────────
    //  GET /proveedores
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("GET /proveedores - retorna 200 con lista de proveedores")
    @WithMockUser
    void obtenerTodos_retorna200_conListaDeProveedores() throws Exception {
        when(proveedoresService.obtenerTodosLosProveedores())
                .thenReturn(List.of(proveedorActivo, proveedorInactivo));

        mockMvc.perform(get("/proveedores")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(content().contentType(MediaType.APPLICATION_JSON))
                .andExpect(jsonPath("$", hasSize(2)))
                .andExpect(jsonPath("$[0].idProveedor",   is(1)))
                .andExpect(jsonPath("$[0].nombreProv",    is("Harinera del Valle")))
                .andExpect(jsonPath("$[0].telefonoProv",  is("3001234567")))
                .andExpect(jsonPath("$[0].activoProv",    is(true)))
                .andExpect(jsonPath("$[0].emailProv",     is("contacto@harinera.com")))
                .andExpect(jsonPath("$[0].direccionProv", is("Calle 10 #5-23, Cali")))
                .andExpect(jsonPath("$[1].idProveedor",   is(2)))
                .andExpect(jsonPath("$[1].activoProv",    is(false)));

        verify(proveedoresService, times(1)).obtenerTodosLosProveedores();
    }

    @Test
    @DisplayName("GET /proveedores - retorna 200 con lista vacía")
    @WithMockUser
    void obtenerTodos_retorna200_conListaVacia() throws Exception {
        when(proveedoresService.obtenerTodosLosProveedores())
                .thenReturn(Collections.emptyList());

        mockMvc.perform(get("/proveedores")
                        .contentType(MediaType.APPLICATION_JSON))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$", hasSize(0)));

        verify(proveedoresService, times(1)).obtenerTodosLosProveedores();
    }

    @Test
    @DisplayName("GET /proveedores - retorna 401 sin autenticación")
    void obtenerTodos_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(get("/proveedores"))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(proveedoresService);
    }

    // ─────────────────────────────────────────────
    //  POST /proveedores
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("POST /proveedores - retorna 200 con mensaje de éxito")
    @WithMockUser
    void crearProveedor_retorna200_conMensajeExito() throws Exception {
        doNothing().when(proveedoresService).crearProveedor(any(Proveedores.class));

        mockMvc.perform(post("/proveedores")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(proveedorActivo)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("Harinera del Valle")))
                .andExpect(content().string(containsString("creado con éxito")));

        verify(proveedoresService, times(1)).crearProveedor(any(Proveedores.class));
    }

    @Test
    @DisplayName("POST /proveedores - retorna 200 con proveedor inactivo")
    @WithMockUser
    void crearProveedor_retorna200_conProveedorInactivo() throws Exception {
        doNothing().when(proveedoresService).crearProveedor(any(Proveedores.class));

        mockMvc.perform(post("/proveedores")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(proveedorInactivo)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("Lácteos El Campo")));

        verify(proveedoresService, times(1)).crearProveedor(any(Proveedores.class));
    }

    @Test
    @DisplayName("POST /proveedores - retorna 401 sin autenticación")
    void crearProveedor_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(post("/proveedores")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(proveedorActivo)))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(proveedoresService);
    }

    // ─────────────────────────────────────────────
    //  PUT /proveedores/{id}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("PUT /proveedores/{id} - retorna 200 cuando el proveedor existe")
    @WithMockUser
    void editarProveedor_retorna200_cuandoProveedorExiste() throws Exception {
        when(proveedoresService.editarProveedor(any(Proveedores.class))).thenReturn(1);

        mockMvc.perform(put("/proveedores/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(proveedorActivo)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("actualizado correctamente")));

        verify(proveedoresService, times(1)).editarProveedor(any(Proveedores.class));
    }

    @Test
    @DisplayName("PUT /proveedores/{id} - retorna 404 cuando el ID no existe")
    @WithMockUser
    void editarProveedor_retorna404_cuandoIdNoExiste() throws Exception {
        when(proveedoresService.editarProveedor(any(Proveedores.class))).thenReturn(0);

        mockMvc.perform(put("/proveedores/99")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(proveedorActivo)))
                .andExpect(status().isNotFound());

        verify(proveedoresService, times(1)).editarProveedor(any(Proveedores.class));
    }

    @Test
    @DisplayName("PUT /proveedores/{id} - retorna 401 sin autenticación")
    void editarProveedor_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(put("/proveedores/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(proveedorActivo)))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(proveedoresService);
    }

    // ─────────────────────────────────────────────
    //  DELETE /proveedores/{id}
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("DELETE /proveedores/{id} - retorna 200 cuando el proveedor existe")
    @WithMockUser
    void eliminarProveedor_retorna200_cuandoProveedorExiste() throws Exception {
        when(proveedoresService.eliminarProveedor(anyInt())).thenReturn(1);

        mockMvc.perform(delete("/proveedores/1")
                        .with(csrf()))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("eliminado correctamente")));

        verify(proveedoresService, times(1)).eliminarProveedor(1);
    }

    @Test
    @DisplayName("DELETE /proveedores/{id} - retorna 404 cuando el ID no existe")
    @WithMockUser
    void eliminarProveedor_retorna404_cuandoIdNoExiste() throws Exception {
        when(proveedoresService.eliminarProveedor(anyInt())).thenReturn(0);

        mockMvc.perform(delete("/proveedores/99")
                        .with(csrf()))
                .andExpect(status().isNotFound());

        verify(proveedoresService, times(1)).eliminarProveedor(99);
    }

    @Test
    @DisplayName("DELETE /proveedores/{id} - retorna 401 sin autenticación")
    void eliminarProveedor_retorna401_sinAutenticacion() throws Exception {
        mockMvc.perform(delete("/proveedores/1")
                        .with(csrf()))
                .andExpect(status().isUnauthorized());

        verifyNoInteractions(proveedoresService);
    }
}