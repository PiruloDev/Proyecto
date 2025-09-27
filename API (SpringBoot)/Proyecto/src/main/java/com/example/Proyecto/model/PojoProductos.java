package com.example.Proyecto.model;
import java.time.LocalDate;


public class PojoProductos {
    private int id;
    private String nombreProducto;
    private int stockMinimo;
    private int precio;
    private LocalDate fechaVencimiento;
    private String marcaProducto;

    public PojoProductos() {}

    // Getters y Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getNombreProducto() {
        return nombreProducto;
    }

    public void setNombreProducto(String nombreProducto) {
        this.nombreProducto = nombreProducto;
    }

    public int getStockMinimo() {
        return stockMinimo;
    }

    public void setStockMinimo(int stockMinimo) {
        this.stockMinimo = stockMinimo;
    }

    public int getPrecio() {
        return precio;
    }

    public void setPrecio(int precio) {
        this.precio = precio;
    }

    public LocalDate getFechaVencimiento() {
        return fechaVencimiento;
    }

    public void setFechaVencimiento(LocalDate fechaVencimiento) {
        this.fechaVencimiento = fechaVencimiento;
    }
    public String getMarcaProducto(){
        return marcaProducto;
    }

    public void setMarcaProducto(String marcaProducto) {
        this.marcaProducto = marcaProducto;
    }
}
