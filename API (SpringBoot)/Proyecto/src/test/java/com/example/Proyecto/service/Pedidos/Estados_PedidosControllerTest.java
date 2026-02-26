package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.controller.Estados_PedidosController;
import com.example.Proyecto.model.Estado_Pedidos;
import com.example.Proyecto.service.Pedidos.Estados_PedidosService;
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

import java.util.List;

import static org.mockito.ArgumentMatchers.any;
import static org.mockito.Mockito.when;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;
import static org.hamcrest.Matchers.containsString;

@WebMvcTest(controllers = Estados_PedidosController.class, excludeAutoConfiguration = SecurityAutoConfiguration.class)
@WithMockUser
class Estados_PedidosControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private Estados_PedidosService service;

    @Autowired
    private ObjectMapper objectMapper;

    private Estado_Pedidos estado;

    @BeforeEach
    void setUp() {
        estado = new Estado_Pedidos();
        estado.setID_ESTADO_PEDIDO(1L);
        estado.setNOMBRE_ESTADO("Pendiente");
    }

    @Test
    @DisplayName("GET /estadosPedidos -> Retorna 200 y lista")
    void obtenerTodos_exito() throws Exception {
        when(service.obtenerEstado_Pedidos()).thenReturn(List.of(estado));

        mockMvc.perform(get("/estadosPedidos"))
                .andExpect(status().isOk())
                .andExpect(jsonPath("$[0].nombre_ESTADO").value("Pendiente"));
    }

    @Test
    @DisplayName("POST /estadosPedidos -> Retorna 201 al crear")
    void crearEstado_exito() throws Exception {
        when(service.crearEstadoPedido(any())).thenReturn(10L);

        mockMvc.perform(post("/estadosPedidos")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(estado)))
                .andExpect(status().isCreated())
                .andExpect(content().string(containsString("Estado de pedido 10 creado con éxito")));
    }

    @Test
    @DisplayName("PUT /estadosPedidos/{id} -> Debe retornar 200 al actualizar")
    void actualizarEstado_exito() throws Exception {
        // No es necesario que el servicio retorne nada (es void),
        // solo verificamos que el controlador responda 200 OK.
        mockMvc.perform(put("/estadosPedidos/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(estado)))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("actualizado con éxito")));
    }

    @Test
    @DisplayName("DELETE /estadosPedidos/{id} -> Retorna 200 al eliminar")
    void eliminarEstado_exito() throws Exception {
        mockMvc.perform(delete("/estadosPedidos/1")
                        .with(csrf()))
                .andExpect(status().isOk())
                .andExpect(content().string(containsString("eliminado con éxito")));
    }
}
