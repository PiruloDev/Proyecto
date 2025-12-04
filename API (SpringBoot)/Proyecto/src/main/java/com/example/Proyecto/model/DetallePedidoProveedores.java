package com.example.Proyecto.model;

import java.math.BigDecimal;

public class DetallePedidoProveedores {
    private int idDetalleProv;
    private int idPedidoProv;
    private int idIngrediente;
    private String nombreIngrediente; // Campo para mostrar el nombre del ingrediente al hacer el JOIN
    private int cantidad;
    private BigDecimal precioUnitario;
    private BigDecimal subtotal;

    public DetallePedidoProveedores() {}

    // Getters y Setters
    public int getIdDetalleProv() { return idDetalleProv; }
    public void setIdDetalleProv(int idDetalleProv) { this.idDetalleProv = idDetalleProv; }

    public int getIdPedidoProv() { return idPedidoProv; }
    public void setIdPedidoProv(int idPedidoProv) { this.idPedidoProv = idPedidoProv; }

    public int getIdIngrediente() { return idIngrediente; }
    public void setIdIngrediente(int idIngrediente) { this.idIngrediente = idIngrediente; }

    public String getNombreIngrediente() { return nombreIngrediente; }
    public void setNombreIngrediente(String nombreIngrediente) { this.nombreIngrediente = nombreIngrediente; }

    public int getCantidad() { return cantidad; }
    public void setCantidad(int cantidad) { this.cantidad = cantidad; }

    public BigDecimal getPrecioUnitario() { return precioUnitario; }
    public void setPrecioUnitario(BigDecimal precioUnitario) { this.precioUnitario = precioUnitario; }

    public BigDecimal getSubtotal() { return subtotal; }
    public void setSubtotal(BigDecimal subtotal) { this.subtotal = subtotal; }
}