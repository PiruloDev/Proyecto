package com.example.Proyecto.dto;

import java.math.BigDecimal;

public class IngredientesCantidad {

    private Long idIngrediente;
    private String nombreIngrediente;
    private BigDecimal cantidadIngrediente;

    public IngredientesCantidad() {}

    public IngredientesCantidad(Long idIngrediente, String nombreIngrediente, BigDecimal cantidadIngrediente) {
        this.idIngrediente = idIngrediente;
        this.nombreIngrediente = nombreIngrediente;
        this.cantidadIngrediente = cantidadIngrediente;
    }

    public Long getIdIngrediente() {
        return idIngrediente;
    }

    public void setIdIngrediente(Long idIngrediente) {
        this.idIngrediente = idIngrediente;
    }

    public String getNombreIngrediente() {
        return nombreIngrediente;
    }

    public void setNombreIngrediente(String nombreIngrediente) {
        this.nombreIngrediente = nombreIngrediente;
    }

    public BigDecimal getCantidadIngrediente() {
        return cantidadIngrediente;
    }

    public void setCantidadIngrediente(BigDecimal cantidadIngrediente) {
        this.cantidadIngrediente = cantidadIngrediente;
    }
}

