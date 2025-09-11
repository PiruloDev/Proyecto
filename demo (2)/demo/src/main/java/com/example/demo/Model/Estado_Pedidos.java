package com.example.demo.Model;

public class Estado_Pedidos {
    private int ID_ESTADO_PEDIDOS;
    private String NOMBRE_ESTADO;

    public Estado_Pedidos(int ID_ESTADO_PEDIDOS, String NOMBRE_ESTADO) {
        this.ID_ESTADO_PEDIDOS = ID_ESTADO_PEDIDOS;
        this.NOMBRE_ESTADO = NOMBRE_ESTADO;
    }

    public int getID_ESTADO_PEDIDOS() {
        return ID_ESTADO_PEDIDOS;
    }

    public void setID_ESTADO_PEDIDOS(int ID_ESTADO_PEDIDOS) {
        this.ID_ESTADO_PEDIDOS = ID_ESTADO_PEDIDOS;
    }

    public String getNOMBRE_ESTADO() {
        return NOMBRE_ESTADO;
    }

    public void setNOMBRE_ESTADO(String NOMBRE_ESTADO) {
        this.NOMBRE_ESTADO = NOMBRE_ESTADO;
    }
}
