package com.example.Proyecto.model;

import java.math.BigDecimal;
import java.util.Date;

public class Pedidos {

    private Long ID_PEDIDO; // <<-- ¡NUEVO CAMPO CRÍTICO!
    private Long ID_CLIENTE; // Cambiado a Long
    private Long ID_EMPLEADO; // Cambiado a Long
    private Long ID_ESTADO_PEDIDO; // Cambiado a Long
    private Date FECHA_INGRESO;
    private Date FECHA_ENTREGA;
    private BigDecimal TOTAL_PRODUCTO;

    // Constructor vacío
    public Pedidos() {}

    // --------------------------------------------------
    // Getters y Setters para ID_PEDIDO
    // --------------------------------------------------
    public Long getID_PEDIDO() {
        return ID_PEDIDO;
    }

    public void setID_PEDIDO(Long ID_PEDIDO) {
        this.ID_PEDIDO = ID_PEDIDO;
    }

    // --------------------------------------------------
    // Getters y Setters: Ahora devuelven Long, no int.
    // --------------------------------------------------

    public Long getID_CLIENTE() {
        return ID_CLIENTE;
    }

    public void setID_CLIENTE(Long ID_CLIENTE) {
        this.ID_CLIENTE = ID_CLIENTE;
    }

    public Long getID_EMPLEADO() {
        return ID_EMPLEADO;
    }

    public void setID_EMPLEADO(Long ID_EMPLEADO) {
        this.ID_EMPLEADO = ID_EMPLEADO;
    }

    public Long getID_ESTADO_PEDIDO() {
        return ID_ESTADO_PEDIDO;
    }

    public void setID_ESTADO_PEDIDO(Long ID_ESTADO_PEDIDO) {
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