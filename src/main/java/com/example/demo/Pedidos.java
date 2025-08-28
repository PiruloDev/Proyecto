package com.example.demo;

import java.util.Date;

public class Pedidos {
    private int idPedido;
    private int idCliente;
    private int idEmpleado;
    private int idEstadoPedido;
    private Date fechaIngreso;
    private Date fechaEntrega;
    private double totalProducto;

    // Constructor vacío
    public Pedidos() {}

    // Constructor con parámetros
    public Pedidos(int idPedido, int idCliente, int idEmpleado, int idEstadoPedido,
                   Date fechaIngreso, Date fechaEntrega, double totalProducto) {
        this.idPedido = idPedido;
        this.idCliente = idCliente;
        this.idEmpleado = idEmpleado;
        this.idEstadoPedido = idEstadoPedido;
        this.fechaIngreso = fechaIngreso;
        this.fechaEntrega = fechaEntrega;
        this.totalProducto = totalProducto;
    }

    // Getters y Setters
    public int getIdPedido() { return idPedido; }
    public void setIdPedido(int idPedido) { this.idPedido = idPedido; }

    public int getIdCliente() { return idCliente; }
    public void setIdCliente(int idCliente) { this.idCliente = idCliente; }

    public int getIdEmpleado() { return idEmpleado; }
    public void setIdEmpleado(int idEmpleado) { this.idEmpleado = idEmpleado; }

    public int getIdEstadoPedido() { return idEstadoPedido; }
    public void setIdEstadoPedido(int idEstadoPedido) { this.idEstadoPedido = idEstadoPedido; }

    public Date getFechaIngreso() { return fechaIngreso; }
    public void setFechaIngreso(Date fechaIngreso) { this.fechaIngreso = fechaIngreso; }

    public Date getFechaEntrega() { return fechaEntrega; }
    public void setFechaEntrega(Date fechaEntrega) { this.fechaEntrega = fechaEntrega; }

    public double getTotalProducto() { return totalProducto; }
    public void setTotalProducto(double totalProducto) { this.totalProducto = totalProducto; }
}
