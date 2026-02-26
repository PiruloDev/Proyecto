package com.example.Proyecto.service.Pedidos;

import com.example.Proyecto.controller.DetallePedidosController;
import com.example.Proyecto.service.DetallePedidos.DetallePedidos;
import com.example.Proyecto.service.DetallePedidos.DetallePedidosService;
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

import static org.mockito.ArgumentMatchers.any;
import static org.mockito.ArgumentMatchers.anyInt;
import static org.mockito.Mockito.when;
import static org.springframework.security.test.web.servlet.request.SecurityMockMvcRequestPostProcessors.csrf;
import static org.springframework.test.web.servlet.request.MockMvcRequestBuilders.*;
import static org.springframework.test.web.servlet.result.MockMvcResultMatchers.*;

@WebMvcTest(controllers = DetallePedidosController.class, excludeAutoConfiguration = SecurityAutoConfiguration.class)
@WithMockUser
class DetallePedidosControllerTest {

    @Autowired
    private MockMvc mockMvc;

    @MockitoBean
    private DetallePedidosService service;

    @Autowired
    private ObjectMapper objectMapper;

    private DetallePedidos detalle;

    @BeforeEach
    void setUp() {
        detalle = new DetallePedidos();
        detalle.setIdDetalle(1);
        detalle.setCantidadProducto(5);
        detalle.setPrecioUnitario(new BigDecimal("2.50"));
    }

    @Test
    @DisplayName("PUT /detalles-pedidos/{id} -> Retorna 200 al editar con éxito")
    void editarDetalle_exito() throws Exception {
        when(service.editarDetallePedido(any())).thenReturn(1);

        mockMvc.perform(put("/detalles-pedidos/1")
                        .with(csrf())
                        .contentType(MediaType.APPLICATION_JSON)
                        .content(objectMapper.writeValueAsString(detalle)))
                .andExpect(status().isOk())
                .andExpect(content().string(org.hamcrest.Matchers.containsString("actualizado correctamente")));
    }

    @Test
    @DisplayName("DELETE /detalles-pedidos/{id} -> Retorna 404 si el detalle no existe")
    void eliminarDetalle_noEncontrado() throws Exception {
        when(service.eliminarDetallePedido(anyInt())).thenReturn(0);

        mockMvc.perform(delete("/detalles-pedidos/999")
                        .with(csrf()))
                .andExpect(status().isNotFound());
    }
}
