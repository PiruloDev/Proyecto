package com.example.Proyecto.model;

import jakarta.persistence.*;

import java.time.LocalDate;

@Entity
@Table(name = "ordenes_salida")
public class Ordenes_salida {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Column(name = "ID_FACTURA")
    private int idFactura;   // ID_FACTURA
    
    @Column(name = "ID_CLIENTE")
    private Long idCliente;   // ID_CLIENTE
    
    @Column(name = "ID_PEDIDO")
    private Long idPedido;    // ID_PEDIDO
    
    @Column(name = "FECHA_FACTURACION")
    private LocalDate fechaFacturacion; // FECHA_FACTURACION
    
    @Column(name = "TOTAL_FACTURA")
    private double totalFactura; // TOTAL_FACTURA

    // Constructor vacío requerido por JPA
    public Ordenes_salida() {}

    // Constructor
    public Ordenes_salida(int idFactura, Long idCliente, Long idPedido, LocalDate fechaFacturacion, double totalFactura) {
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

    public Long getIdCliente() {
        return idCliente;
    }

    public void setIdCliente(Long idCliente) {
        this.idCliente = idCliente;
    }

    public Long getIdPedido() {
        return idPedido;
    }

    public void setIdPedido(Long idPedido) {
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
