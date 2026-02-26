package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.controller.PedidosController;
import com.example.Proyecto.model.Pedidos;
import com.example.Proyecto.service.Pedidos.pedidosService;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.boot.autoconfigure.security.servlet.SecurityAutoConfiguration;
import org.springframework.boot.test.autoconfigure.web.servlet.WebMvcTest;
import org.springframework.http.MediaType;
import org.springframework.security.test.context.support.WithMockUser;
import org.springframework.test.context.bean.override.mockito.MockitoBean;
import org.springframework.test.web.servlet.MockMvc;

import java.util.List;

// IMPORTS ESTÁTICOS: Fundamentales para que get, post, status, etc. no salgan en rojo
import static org.mockito.ArgumentMatchers.any;
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

    @Test
    @DisplayName("GET /pedidos -> Debe retornar 200 OK")
    void obtenerTodos_retorna200() throws Exception {
        when(service.obtenerPedidos()).thenReturn(List.of(new Pedidos()));

        mockMvc.perform(get("/pedidos"))
                .andExpect(status().isOk())
                .andExpect(content().contentType(MediaType.APPLICATION_JSON));
    }

    @Test
    @DisplayName("POST /pedidos -> Debe retornar 201 cuando es exitoso")
    void crearPedido_exito() throws Exception {
        Pedidos pedido = new Pedidos();
        pedido.setID_CLIENTE(1L);

        mockMvc.perform(post("/pedidos")
                        .with(csrf()) // Elimina el error "Cannot resolve method csrf"
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(pedido)))
                .andExpect(status().isCreated())
                .andExpect(content().string("Pedido creado con éxito."));
    }

    @Test
    @DisplayName("POST /pedidos -> Debe retornar 400 si el servicio lanza error")
    void crearPedido_errorStock() throws Exception {
        Pedidos pedido = new Pedidos();

        doThrow(new RuntimeException("No hay suficiente pan/producto"))
                .when(service).crearPedido(any());

        mockMvc.perform(post("/pedidos")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(pedido)))
                .andExpect(status().isBadRequest())
                .andExpect(content().string("No hay suficiente pan/producto"));
    }
}