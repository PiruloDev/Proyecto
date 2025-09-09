// Archivo: DetallePedidos.java
package com.example.Proyecto.service.DetallePedidos;

import java.math.BigDecimal;

public class DetallePedidos {
    private int idDetalle;
    private int idPedido;
    private int idProducto;
    private int cantidadProducto;
    private BigDecimal precioUnitario;
    private BigDecimal subtotal;

    public DetallePedidos() {}

    public int getIdDetalle() { return idDetalle; }
    public void setIdDetalle(int idDetalle) { this.idDetalle = idDetalle; }

    public int getIdPedido() { return idPedido; }
    public void setIdPedido(int idPedido) { this.idPedido = idPedido; }

    public int getIdProducto() { return idProducto; }
    public void setIdProducto(int idProducto) { this.idProducto = idProducto; }

    public int getCantidadProducto() { return cantidadProducto; }
    public void setCantidadProducto(int cantidadProducto) { this.cantidadProducto = cantidadProducto; }

    public BigDecimal getPrecioUnitario() { return precioUnitario; }
    public void setPrecioUnitario(BigDecimal precioUnitario) { this.precioUnitario = precioUnitario; }

    public BigDecimal getSubtotal() { return subtotal; }
    public void setSubtotal(BigDecimal subtotal) { this.subtotal = subtotal; }
}