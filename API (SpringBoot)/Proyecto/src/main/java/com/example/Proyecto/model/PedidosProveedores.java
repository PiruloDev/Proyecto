package com.example.Proyecto.model;

import java.util.Date;
import java.util.List;

public class PedidosProveedores {
    // Campos del encabezado del pedido
    private int idPedidoProv;
    private int idProveedor;

    private String nombreProveedor; // ← nuevo

    private int numeroPedido;
    private Date fechaPedido;
    private String estadoPedido;

    // Propiedad para la composición: lista de detalles del pedido/ingredientes
    private List<DetallePedidoProveedores> detalles;

    public PedidosProveedores() {}

    // Getters y Setters
    public int getIdPedidoProv() { return idPedidoProv; }
    public void setIdPedidoProv(int idPedidoProv) { this.idPedidoProv = idPedidoProv; }

    public int getIdProveedor() { return idProveedor; }
    public void setIdProveedor(int idProveedor) { this.idProveedor = idProveedor; }

    public String getNombreProveedor() { return nombreProveedor; }
    public void setNombreProveedor(String nombreProveedor) { this.nombreProveedor = nombreProveedor; }

    public int getNumeroPedido() { return numeroPedido; }
    public void setNumeroPedido(int numeroPedido) { this.numeroPedido = numeroPedido; }

    public Date getFechaPedido() { return fechaPedido; }
    public void setFechaPedido(Date fechaPedido) { this.fechaPedido = fechaPedido; }

    public String getEstadoPedido() { return estadoPedido; }
    public void setEstadoPedido(String estadoPedido) { this.estadoPedido = estadoPedido; }

    public List<DetallePedidoProveedores> getDetalles() { return detalles; }
    public void setDetalles(List<DetallePedidoProveedores> detalles) { this.detalles = detalles; }

}