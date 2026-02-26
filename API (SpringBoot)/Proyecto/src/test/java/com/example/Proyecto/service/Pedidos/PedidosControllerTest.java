package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.controller.PedidosController;
import com.example.Proyecto.model.Pedidos;
import com.example.Proyecto.service.Pedidos.pedidosService;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.autoconfigure.security.servlet.SecurityAutoConfiguration;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.http.MediaType;
import org.springframework.security.test.context.support.WithMockUser;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;

import java.math.BigDecimal;
import java.util.List;

// IMPORTS ESTÁTICOS: Fundamentales para que get, post, status, containsString, etc. funcionen
import static org.hamcrest.Matchers.containsString;
import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyLong;
import static org.mockito.Mockito.doThrow;
import static org.mockito.Mockito.when;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(controllers = PedidosController.class, excludeAutoConfiguration = SecurityAutoConfiguration.class)
@WithMockUser // Simula el usuario del dashboard de empleado
class PedidosControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean // Reemplaza a @MockBean para evitar el aviso de "deprecated"
    private pedidosService service;

    @Autowired
    private ObjectMapper objectMapper;

    private Pedidos pedidoEjemplo;

    @BeforeEach
    void setUp() {
        // Inicializamos el objeto que usaremos en las pruebas de POST, PUT y GET
        pedidoEjemplo = new Pedidos();
        pedidoEjemplo.setID_PEDIDO(1);
        pedidoEjemplo.setID_CLIENTE(1L);
        pedidoEjemplo.setID_EMPLEADO(1L);
        pedidoEjemplo.setTOTAL_PRODUCTO(new BigDecimal("150.00"));
    }

    @Test
    @DisplayName("GET /pedidos -> Debe retornar 200 OK")
    void obtenerTodos_retorna200() throws Exception {
        when(service.obtenerPedidos()).thenReturn(List.of(pedidoEjemplo));

        mockMvc.perform(get("/pedidos"))
                .andExpect(status().isOk())
                .andExpect(content().contentType(MediaType.APPLICATION_JSON));
    }

    @Test
    @DisplayName("POST /pedidos -> Debe retornar 201 cuando es exitoso")
    void crearPedido_exito() throws Exception {
        mockMvc.perform(post("/pedidos")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(pedidoEjemplo)))
                .andExpect(status().isCreated())
                .andExpect(content().string("Pedido creado con éxito."));
    }

    @Test
    @DisplayName("POST /pedidos -> Debe retornar 400 si el servicio lanza error")
    void crearPedido_errorStock() throws Exception {
        doThrow(new RuntimeException("No hay suficiente pan/producto"))
                .when(service).crearPedido(any());

        mockMvc.perform(post("/pedidos")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(pedidoEjemplo)))
                .andExpect(status().isBadRequest())
                .andExpect(content().string("No hay suficiente pan/producto"));
    }

    @Test
    @DisplayName("PUT /pedidos/{id} -> Debe retornar 200 al actualizar")
    void actualizarPedido_exito() throws Exception {
        // En el controlador, el método devuelve un mensaje de texto plano
        mockMvc.perform(put("/pedidos/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(pedidoEjemplo)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("actualizado con éxito")));
    }

    @Test
    @DisplayName("DELETE /pedidos/{id} -> Debe retornar 200 al eliminar")
    void eliminarPedido_exito() throws Exception {
        mockMvc.perform(delete("/pedidos/1")
                        .with(csrf()))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("eliminado con éxito")));
    }

    @Test
    @DisplayName("GET /pedidos/cliente/{id} -> Debe retornar lista de pedidos")
    void obtenerPedidosPorCliente_exito() throws Exception {
        when(service.obtenerPedidosPorCliente(anyLong())).thenReturn(List.of(pedidoEjemplo));

        mockMvc.perform(get("/pedidos/cliente/1"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$.length()").value(1))
                .andExpect(jsonPath("$[0].id_CLIENTE").value(1));
    }
}