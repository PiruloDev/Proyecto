package com.example.Proyecto.model;

public class Estado_Pedidos {

    private Long ID_ESTADO_PEDIDO;
    private String NOMBRE_ESTADO;

    public Estado_Pedidos() {
        this.ID_ESTADO_PEDIDO = ID_ESTADO_PEDIDO;
        this.NOMBRE_ESTADO = NOMBRE_ESTADO;
    }

    public Long getID_ESTADO_PEDIDO() {
        return ID_ESTADO_PEDIDO;
    }

    public void setID_ESTADO_PEDIDO(Long ID_ESTADO_PEDIDO) {
        this.ID_ESTADO_PEDIDO = ID_ESTADO_PEDIDO;
    }

    public String getNOMBRE_ESTADO() {
        return NOMBRE_ESTADO;
    }

    public void setNOMBRE_ESTADO(String NOMBRE_ESTADO) {
        this.NOMBRE_ESTADO = NOMBRE_ESTADO;
    }
}