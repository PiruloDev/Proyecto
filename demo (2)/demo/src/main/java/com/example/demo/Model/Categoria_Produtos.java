package com.example.demo.Model;

public class Categoria_Produtos {
    private int ID_CATEGORIA_PRODUCTOS;
    private String NOMBRE_CATEGORIA_PRODUCTO;

    public Categoria_Produtos(int ID_CATEGORIA_PRODUCTOS, String NOMBRE_CATEGORIA_PRODUCTO) {
        this.ID_CATEGORIA_PRODUCTOS = ID_CATEGORIA_PRODUCTOS;
        this.NOMBRE_CATEGORIA_PRODUCTO = NOMBRE_CATEGORIA_PRODUCTO;
    }

    public int getID_CATEGORIA_PRODUCTOS() {
        return ID_CATEGORIA_PRODUCTOS;
    }

    public void setID_CATEGORIA_PRODUCTOS(int ID_CATEGORIA_PRODUCTOS) {
        this.ID_CATEGORIA_PRODUCTOS = ID_CATEGORIA_PRODUCTOS;
    }

    public String getNOMBRE_CATEGORIA_PRODUCTO() {
        return NOMBRE_CATEGORIA_PRODUCTO;
    }

    public void setNOMBRE_CATEGORIA_PRODUCTO(String NOMBRE_CATEGORIA_PRODUCTO) {
        this.NOMBRE_CATEGORIA_PRODUCTO = NOMBRE_CATEGORIA_PRODUCTO;
    }
}
