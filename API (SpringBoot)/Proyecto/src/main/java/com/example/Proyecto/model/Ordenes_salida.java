package com.example.Proyecto.model;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

import java.time.LocalDateTime;

@Entity
@Table(name = "ordenes_salida")
public class Ordenes_salida {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "id_factura")
    private int idFactura;

    @Column(name = "nombre_cliente")
    private String nombreCliente;

    @Column(name = "id_cliente")
    private int idCliente;

    @Column(name = "id_pedido")
    private int idPedido;

    @Column(name = "fecha_facturacion")
    private LocalDateTime fechaFacturacion;

    @Column(name = "total_factura")
    private double totalFactura;

    public int getIdFactura() { return idFactura; }
    public void setIdFactura(int idFactura) { this.idFactura = idFactura; }

    public String getNombreCliente() { return nombreCliente; }
    public void setNombreCliente(String nombreCliente) { this.nombreCliente = nombreCliente; }

    public int getIdCliente() { return idCliente; }
    public void setIdCliente(int idCliente) { this.idCliente = idCliente; }

    public int getIdPedido() { return idPedido; }
    public void setIdPedido(int idPedido) { this.idPedido = idPedido; }

    public LocalDateTime getFechaFacturacion() { return fechaFacturacion; }
    public void setFechaFacturacion(LocalDateTime fechaFacturacion) { this.fechaFacturacion = fechaFacturacion; }

    public double getTotalFactura() { return totalFactura; }
    public void setTotalFactura(double totalFactura) { this.totalFactura = totalFactura; }
}