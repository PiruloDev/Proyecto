// Archivo: PedidosProveedores.java
package com.example.demo.Service.PedidosProveedores;

import java.util.Date;

public class PedidosProveedores {
    private int IdPedidoProv;
    private int IdProveedor;
    private int NumeroPedido; // <-- Cambiado a 'int'
    private Date FechaPedido;
    private String EstadoPedido;

    public PedidosProveedores() {}

    public int getIdPedidoProv() { return IdPedidoProv; }
    public void setIdPedidoProv(int IdPedidoProv) { this.IdPedidoProv = IdPedidoProv; }

    public int getIdProveedor() { return IdProveedor; }
    public void setIdProveedor(int IdProveedor) { this.IdProveedor = IdProveedor; }

    public int getNumeroPedido() { return NumeroPedido; } // <-- Retorna 'int'
    public void setNumeroPedido(int NumeroPedido) { this.NumeroPedido = NumeroPedido; } // <-- Recibe 'int'

    public Date getFechaPedido() { return FechaPedido; }
    public void setFechaPedido(Date FechaPedido) { this.FechaPedido = FechaPedido; }

    public String getEstadoPedido() { return EstadoPedido; }
    public void setEstadoPedido(String EstadoPedido) { this.EstadoPedido = EstadoPedido; }
}