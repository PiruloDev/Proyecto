package com.example.Proyecto.model;

import java.math.BigDecimal;
import java.util.Date;

public class Ingredientes {
    private Long idIngrediente;
    private Long idProveedor;
    private Long idCategoria;
    private Long idUnidadMedida;
    private String nombreIngrediente;
    private BigDecimal cantidadIngrediente;
    private Date fechaVencimiento;
    private String referenciaIngrediente;
    private Date fechaEntregaIngrediente;
    public Ingredientes() {}

    public Ingredientes(Long idIngrediente,
                        Long idProveedor,
                        Long idCategoria,
                        Long idUnidadMedida,
                        String nombreIngrediente,
                        BigDecimal cantidadIngrediente,
                        Date fechaVencimiento,
                        String referenciaIngrediente,
                        Date fechaEntregaIngrediente) {
        this.idIngrediente = idIngrediente;
        this.idProveedor = idProveedor;
        this.idCategoria = idCategoria;
        this.idUnidadMedida =  idUnidadMedida;
        this.nombreIngrediente = nombreIngrediente;
        this.cantidadIngrediente = cantidadIngrediente;
        this.fechaVencimiento = fechaVencimiento;
        this.referenciaIngrediente = referenciaIngrediente;
        this.fechaEntregaIngrediente = fechaEntregaIngrediente;
    }

    // Getters y Setters
    public Long getIdIngrediente() { return idIngrediente; }
    public void setIdIngrediente(Long idIngrediente) { this.idIngrediente = idIngrediente; }

    public Long getIdProveedor() { return idProveedor; }
    public void setIdProveedor(Long idProveedor) { this.idProveedor = idProveedor; }

    public Long getIdCategoria() { return idCategoria; }
    public void setIdCategoria(Long idCategoria) { this.idCategoria = idCategoria; }

    public Long getIdUnidadMedida() {
        return idUnidadMedida;
    }
    public void setIdUnidadMedida(Long idUnidadMedida) {
        this.idUnidadMedida = idUnidadMedida;
    }

    public String getNombreIngrediente() { return nombreIngrediente; }
    public void setNombreIngrediente(String nombreIngrediente) { this.nombreIngrediente = nombreIngrediente; }

    public BigDecimal getCantidadIngrediente() { return cantidadIngrediente; }
    public void setCantidadIngrediente(BigDecimal cantidadIngrediente) { this.cantidadIngrediente = cantidadIngrediente; }

    public Date getFechaVencimiento() { return fechaVencimiento; }
    public void setFechaVencimiento(Date fechaVencimiento) { this.fechaVencimiento = fechaVencimiento; }

    public String getReferenciaIngrediente() { return referenciaIngrediente; }
    public void setReferenciaIngrediente(String referenciaIngrediente) { this.referenciaIngrediente = referenciaIngrediente; }

    public Date getFechaEntregaIngrediente() { return fechaEntregaIngrediente; }
    public void setFechaEntregaIngrediente(Date fechaEntregaIngrediente) { this.fechaEntregaIngrediente = fechaEntregaIngrediente; }
}
