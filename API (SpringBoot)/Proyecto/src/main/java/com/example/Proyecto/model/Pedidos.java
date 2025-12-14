package com.example.Proyecto.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;

import java.math.BigDecimal;
import java.util.Date;

@JsonIgnoreProperties(ignoreUnknown = true)
public class Pedidos {

    private int ID_PEDIDO;

    @JsonProperty(value = "cliente_id", access = JsonProperty.Access.WRITE_ONLY)
    private long ID_CLIENTE;

    @JsonProperty(value = "empleado_id", access = JsonProperty.Access.WRITE_ONLY)
    private long ID_EMPLEADO;

    @JsonProperty(value = "estado_pedido_id", access = JsonProperty.Access.WRITE_ONLY)
    private long ID_ESTADO_PEDIDO;

    @JsonProperty(value = "fecha_ingreso", access = JsonProperty.Access.WRITE_ONLY)
    private Date FECHA_INGRESO;

    @JsonProperty(value = "fecha_entrega", access = JsonProperty.Access.WRITE_ONLY)
    private Date FECHA_ENTREGA;

    @JsonProperty(value = "total_producto", access = JsonProperty.Access.WRITE_ONLY)
    private BigDecimal TOTAL_PRODUCTO;

    public int getID_PEDIDO() {
        return ID_PEDIDO;
    }

    public void setID_PEDIDO(int ID_PEDIDO) {
        this.ID_PEDIDO = ID_PEDIDO;
    }

    public long getID_CLIENTE() {
        return ID_CLIENTE;
    }

    public void setID_CLIENTE(long ID_CLIENTE) {
        this.ID_CLIENTE = ID_CLIENTE;
    }

    public long getID_EMPLEADO() {
        return ID_EMPLEADO;
    }

    public void setID_EMPLEADO(long ID_EMPLEADO) {
        this.ID_EMPLEADO = ID_EMPLEADO;
    }

    public long getID_ESTADO_PEDIDO() {
        return ID_ESTADO_PEDIDO;
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