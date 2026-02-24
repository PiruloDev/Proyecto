package com.example.Proyecto.dto;

public class IngredienteListadoDTO {

    private Long idIngrediente;
    private Long idProveedor;
    private Long idCategoria;
    private String nombreIngrediente;
    private String referenciaIngrediente;
    private String abreviaturaUnidad; // ← nuevo

    // Constructor vacío (necesario para el nuevo mapper manual)
    public IngredienteListadoDTO() {}

    // Constructor legacy desde modelo (se mantiene por compatibilidad)
    public IngredienteListadoDTO(com.example.Proyecto.model.Ingredientes ingrediente) {
        this.idIngrediente = ingrediente.getIdIngrediente();
        this.idProveedor = ingrediente.getIdProveedor();
        this.idCategoria = ingrediente.getIdCategoria();
        this.nombreIngrediente = ingrediente.getNombreIngrediente();
        this.referenciaIngrediente = ingrediente.getReferenciaIngrediente();
        this.abreviaturaUnidad = null; // sin JOIN desde modelo
    }

    public Long getIdIngrediente() { return idIngrediente; }
    public void setIdIngrediente(Long idIngrediente) { this.idIngrediente = idIngrediente; }

    public Long getIdProveedor() { return idProveedor; }
    public void setIdProveedor(Long idProveedor) { this.idProveedor = idProveedor; }

    public Long getIdCategoria() { return idCategoria; }
    public void setIdCategoria(Long idCategoria) { this.idCategoria = idCategoria; }

    public String getNombreIngrediente() { return nombreIngrediente; }
    public void setNombreIngrediente(String nombreIngrediente) { this.nombreIngrediente = nombreIngrediente; }

    public String getReferenciaIngrediente() { return referenciaIngrediente; }
    public void setReferenciaIngrediente(String referenciaIngrediente) { this.referenciaIngrediente = referenciaIngrediente; }

    public String getAbreviaturaUnidad() { return abreviaturaUnidad; } // ← nuevo
    public void setAbreviaturaUnidad(String abreviaturaUnidad) { this.abreviaturaUnidad = abreviaturaUnidad; } // ← nuevo
}