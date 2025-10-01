package com.example.Proyecto.model;

public class PojoCategoria_Productos {

    private int idCategoriaProducto;
    private String nombreCategoriaProducto;

    public PojoCategoria_Productos() {
    }

    public PojoCategoria_Productos(int idCategoriaProducto, String nombreCategoriaProducto) {
        this.idCategoriaProducto = idCategoriaProducto;
        this.nombreCategoriaProducto = nombreCategoriaProducto;
    }

    public int getIdCategoriaProducto() {
        return idCategoriaProducto;
    }

    public void setIdCategoriaProducto(int idCategoriaProducto) {
        this.idCategoriaProducto = idCategoriaProducto;
    }

    public String getNombreCategoriaProducto() {
        return nombreCategoriaProducto;
    }

    public void setNombreCategoriaProducto(String nombreCategoriaProducto) {
        this.nombreCategoriaProducto = nombreCategoriaProducto;
    }
}
