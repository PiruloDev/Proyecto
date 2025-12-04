package com.example.Proyecto.dto;

import com.example.Proyecto.model.Ingredientes;

public class IngredienteListadoDTO {

    private Long idIngrediente;
    private Long idProveedor;
    private Long idCategoria;
    private String nombreIngrediente;
    private String referenciaIngrediente;

    // --- Constructor desde el modelo Ingredientes ---
    public IngredienteListadoDTO(Ingredientes ingrediente) {
        this.idIngrediente = ingrediente.getIdIngrediente();
        this.idProveedor = ingrediente.getIdProveedor();
        this.idCategoria = ingrediente.getIdCategoria();
        this.nombreIngrediente = ingrediente.getNombreIngrediente();
        this.referenciaIngrediente = ingrediente.getReferenciaIngrediente();
    }

    public Long getIdIngrediente() {
        return idIngrediente;
    }

    public void setIdIngrediente(Long idIngrediente) {
        this.idIngrediente = idIngrediente;
    }

    public Long getIdProveedor() {
        return idProveedor;
    }

    public void setIdProveedor(Long idProveedor) {
        this.idProveedor = idProveedor;
    }

    public Long getIdCategoria() {
        return idCategoria;
    }

    public void setIdCategoria(Long idCategoria) {
        this.idCategoria = idCategoria;
    }

    public String getNombreIngrediente() {
        return nombreIngrediente;
    }

    public void setNombreIngrediente(String nombreIngrediente) {
        this.nombreIngrediente = nombreIngrediente;
    }

    public String getReferenciaIngrediente() {
        return referenciaIngrediente;
    }

    public void setReferenciaIngrediente(String referenciaIngrediente) {
        this.referenciaIngrediente = referenciaIngrediente;
    }

}
