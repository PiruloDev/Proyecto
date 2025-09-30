// Archivo: CategoriaIngredientes.java
package com.example.Proyecto.service.CategoriaIngredientesService;

public class CategoriaIngredientes {
    private int idCategoriaIngrediente;
    private String nombreCategoria;

    public CategoriaIngredientes() {}

    public int getIdCategoriaIngrediente() {
        return idCategoriaIngrediente;
    }

    public void setIdCategoriaIngrediente(int idCategoriaIngrediente) {
        this.idCategoriaIngrediente = idCategoriaIngrediente;
    }

    public String getNombreCategoria() {
        return nombreCategoria;
    }

    public void setNombreCategoria(String nombreCategoria) {
        this.nombreCategoria = nombreCategoria;
    }
}