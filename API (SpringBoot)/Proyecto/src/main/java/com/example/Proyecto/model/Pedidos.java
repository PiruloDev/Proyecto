package com.example.Proyecto.model;

import java.math.BigDecimal;
import java.util.Date;

public class Pedidos {

    private long ID_CLIENTE;
    private long ID_EMPLEADO;
    private long ID_ESTADO_PEDIDO;
    private Date FECHA_INGRESO;
    private Date FECHA_ENTREGA;
    private BigDecimal TOTAL_PRODUCTO;

    public Pedidos() {
        this.ID_CLIENTE = ID_CLIENTE;
        this.ID_EMPLEADO = ID_EMPLEADO;
        this.ID_ESTADO_PEDIDO = ID_ESTADO_PEDIDO;
        this.FECHA_INGRESO = FECHA_INGRESO;
        this.FECHA_ENTREGA = FECHA_ENTREGA;
        this.TOTAL_PRODUCTO = TOTAL_PRODUCTO;
    }

    public int getID_CLIENTE() {
        return (int) ID_CLIENTE;
    }

    public void setID_CLIENTE(long ID_CLIENTE) {
        this.ID_CLIENTE = ID_CLIENTE;
    }

    public int getID_EMPLEADO() {
        return (int) ID_EMPLEADO;
    }

    public void setID_EMPLEADO(long ID_EMPLEADO) {
        this.ID_EMPLEADO = ID_EMPLEADO;
    }

    public int getID_ESTADO_PEDIDO() {
        return (int) ID_ESTADO_PEDIDO;
    }

    public void setID_ESTADO_PEDIDO(long ID_ESTADO_PEDIDO) {
        this.ID_ESTADO_PEDIDO = ID_ESTADO_PEDIDO;
    }

    public Date getFECHA_INGRESO() {
        return FECHA_INGRESO;
    }

    public void setFECHA_INGRESO(Date FECHA_INGRESO) {
        this.FECHA_INGRESO = FECHA_INGRESO;
    }

    public Date getFECHA_ENTREGA() {
        return FECHA_ENTREGA;
    }

    public void setFECHA_ENTREGA(Date FECHA_ENTREGA) {
        this.FECHA_ENTREGA = FECHA_ENTREGA;
    }

    public BigDecimal getTOTAL_PRODUCTO() {
        return TOTAL_PRODUCTO;
    }

    public void setTOTAL_PRODUCTO(BigDecimal TOTAL_PRODUCTO) {
        this.TOTAL_PRODUCTO = TOTAL_PRODUCTO;
    }
}


