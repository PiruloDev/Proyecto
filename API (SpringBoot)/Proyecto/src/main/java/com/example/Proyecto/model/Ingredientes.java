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
    private String referenciaIngrediente;
    public Ingredientes() {}

    public Ingredientes(Long idIngrediente,
                        Long idProveedor,
                        Long idCategoria,
                        Long idUnidadMedida,
                        String nombreIngrediente,
                        BigDecimal cantidadIngrediente,
                        String referenciaIngrediente ) {
        this.idIngrediente = idIngrediente;
        this.idProveedor = idProveedor;
        this.idCategoria = idCategoria;
        this.idUnidadMedida =  idUnidadMedida;
        this.nombreIngrediente = nombreIngrediente;
        this.cantidadIngrediente = cantidadIngrediente;
        this.referenciaIngrediente = referenciaIngrediente;
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

    public String getReferenciaIngrediente() { return referenciaIngrediente; }
    public void setReferenciaIngrediente(String referenciaIngrediente) { this.referenciaIngrediente = referenciaIngrediente; }

}
