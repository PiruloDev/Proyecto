package com.example.Proyecto.model;

import com.fasterxml.jackson.annotation.JsonProperty;
import java.util.List;

public class PedidoRequest {

    @JsonProperty("cliente_id")
    private Integer clienteId;

    @JsonProperty("items")
    private List<ItemCarrito> items;

    // Getters y Setters

    public Integer getClienteId() {
        return clienteId;
    }

    public void setClienteId(Integer clienteId) {
        this.clienteId = clienteId;
    }

    public List<ItemCarrito> getItems() {
        return items;
    }

    public void setItems(List<ItemCarrito> items) {
        this.items = items;
    }
}
