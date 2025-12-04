package com.example.Proyecto.dto;

import java.math.BigDecimal;

public class IngresoStockRequest {

    private BigDecimal cantidadIngresada;

    public IngresoStockRequest() {
    }

    public BigDecimal getCantidadIngresada() {
        return cantidadIngresada;
    }

    public void setCantidadIngresada(BigDecimal cantidadIngresada) {
        this.cantidadIngresada = cantidadIngresada;
    }
}