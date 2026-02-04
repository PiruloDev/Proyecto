package com.example.Proyecto.model;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;
import com.fasterxml.jackson.annotation.JsonFormat;

import java.math.BigDecimal;
import java.time.LocalDateTime; // Usamos LocalDateTime para mejor manejo de horas

@JsonIgnoreProperties(ignoreUnknown = true)
public class Pedidos {

    private int ID_PEDIDO;

    // Cambiamos a READ_WRITE (o quitamos el access) para que Java envíe el dato a Laravel
    @JsonProperty(value = "cliente_id")
    private long ID_CLIENTE;

    @JsonProperty(value = "empleado_id")
    private long ID_EMPLEADO;

    @JsonProperty(value = "estado_pedido_id")
    private long ID_ESTADO_PEDIDO;

    @JsonProperty(value = "fecha_ingreso")
    @JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss") // <--- Esto fuerza el envío de la hora real
    private LocalDateTime FECHA_INGRESO;

    @JsonProperty(value = "fecha_entrega")
    @JsonFormat(pattern = "yyyy-MM-dd HH:mm:ss")
    private LocalDateTime FECHA_ENTREGA;

    @JsonProperty(value = "total_producto")
    private BigDecimal TOTAL_PRODUCTO;

    // --- GETTERS Y SETTERS ACTUALIZADOS ---

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

    public LocalDateTime getFECHA_INGRESO() {
        return FECHA_INGRESO;
    }

    public void setFECHA_INGRESO(LocalDateTime FECHA_INGRESO) {
        this.FECHA_INGRESO = FECHA_INGRESO;
    }

    public LocalDateTime getFECHA_ENTREGA() {
        return FECHA_ENTREGA;
    }

    public void setFECHA_ENTREGA(LocalDateTime FECHA_ENTREGA) {
        this.FECHA_ENTREGA = FECHA_ENTREGA;
    }

    public BigDecimal getTOTAL_PRODUCTO() {
        return TOTAL_PRODUCTO;
    }

    public void setTOTAL_PRODUCTO(BigDecimal TOTAL_PRODUCTO) {
        this.TOTAL_PRODUCTO = TOTAL_PRODUCTO;
    }
}