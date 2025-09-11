package com.example.demoJava1.Ordenes_salida;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;

import java.time.LocalDate;

@Entity
public class Ordenes_salida {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private int idFactura;   // ID_FACTURA
    private String idCliente;   // ID_CLIENTE
    private String idPedido;    // ID_PEDIDO
    private LocalDate fechaFacturacion; // FECHA_FACTURACION
    private double totalFactura; // TOTAL_FACTURA

    // Constructor
    public Ordenes_salida(int idFactura, String idCliente, String idPedido, LocalDate fechaFacturacion, double totalFactura) {
        this.idFactura = idFactura;
        this.idCliente = idCliente;
        this.idPedido = idPedido;
        this.fechaFacturacion = fechaFacturacion;
        this.totalFactura = totalFactura;
    }

    // METODOS
    public int getIdFactura() {
        return idFactura;
    }

    public void setIdFactura(int idFactura) {
        this.idFactura = idFactura;
    }

    public String getIdCliente() {
        return idCliente;
    }

    public void setIdCliente(String idCliente) {
        this.idCliente = idCliente;
    }

    public String getIdPedido() {
        return idPedido;
    }

    public void setIdPedido(String idPedido) {
        this.idPedido = idPedido;
    }

    public LocalDate getFechaFacturacion() {
        return fechaFacturacion;
    }

    public void setFechaFacturacion(LocalDate fechaFacturacion) {
        this.fechaFacturacion = fechaFacturacion;
    }

    public double getTotalFactura() {
        return totalFactura;
    }

    public void setTotalFactura(double totalFactura) {
        this.totalFactura = totalFactura;
    }
}


