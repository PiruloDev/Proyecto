package com.example.Proyecto.dto;

public class IngredienteDetalleDTO {

    private Long idIngrediente;
    private String nombreIngrediente;
    private String abreviaturaUnidad;
    private Long idUnidad; // ✅ nuevo campo

    public IngredienteDetalleDTO(Long id, String nombre, String abreviatura, Long idUnidad) {
        this.idIngrediente = id;
        this.nombreIngrediente = nombre;
        this.abreviaturaUnidad = abreviatura;
        this.idUnidad = idUnidad; // ✅
    }

    public Long getIdIngrediente() { return idIngrediente; }
    public String getNombreIngrediente() { return nombreIngrediente; }
    public String getAbreviaturaUnidad() { return abreviaturaUnidad; }
    public Long getIdUnidad() { return idUnidad; } // ✅ getter nuevo
}