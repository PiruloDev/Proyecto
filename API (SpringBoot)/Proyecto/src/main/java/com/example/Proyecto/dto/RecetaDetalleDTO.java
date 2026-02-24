package com.example.Proyecto.dto;

import java.math.BigDecimal;

public class RecetaDetalleDTO {

    private Long idReceta;
    private Long idProducto;
    private String nombreProducto;
    private Long idIngrediente;
    private String nombreIngrediente;

    private Long idUnidad;
    private BigDecimal cantidadRequerida;
    private String nombreUnidad;

    public String getNombreUnidad() {
        return nombreUnidad;
    }

    public BigDecimal getCantidadRequerida() {
        return cantidadRequerida;
    }

    public String getNombreIngrediente() {
        return nombreIngrediente;
    }

    public Long getIdIngrediente() {
        return idIngrediente;
    }

    public String getNombreProducto() {
        return nombreProducto;
    }

    public Long getIdProducto() {
        return idProducto;
    }

    public Long getIdReceta() {
        return idReceta;
    }

    public void setIdReceta(Long idReceta) {
        this.idReceta = idReceta;
    }

    public void setIdProducto(Long idProducto) {
        this.idProducto = idProducto;
    }

    public void setNombreProducto(String nombreProducto) {
        this.nombreProducto = nombreProducto;
    }

    public void setIdIngrediente(Long idIngrediente) {
        this.idIngrediente = idIngrediente;
    }

    public void setNombreIngrediente(String nombreIngrediente) {
        this.nombreIngrediente = nombreIngrediente;
    }

    public void setCantidadRequerida(BigDecimal cantidadRequerida) {
        this.cantidadRequerida = cantidadRequerida;
    }

    public void setNombreUnidad(String nombreUnidad) {
        this.nombreUnidad = nombreUnidad;
    }

    public Long getIdUnidad() { return idUnidad; }          // ← faltaba
    public void setIdUnidad(Long idUnidad) { this.idUnidad = idUnidad; }
}
