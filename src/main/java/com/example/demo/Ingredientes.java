package com.example.demo;

import java.util.Date;

public class Ingredientes {
    private int idIngrediente;
    private int idProveedor;
    private int idCategoria;
    private String nombreIngrediente;
    private int cantidadIngrediente;
    private Date fechaVencimiento;
    private String referenciaIngrediente;
    private Date fechaEntregaIngrediente;

    // Constructor vacío
    public Ingredientes() {}

    // Constructor con parámetros
    public Ingredientes(int idIngrediente,
                        int idProveedor,
                        int idCategoria,
                        String nombreIngrediente,
                        int cantidadIngrediente,
                        Date fechaVencimiento,
                        String referenciaIngrediente,
                        Date fechaEntregaIngrediente) {
        this.idIngrediente = idIngrediente;
        this.idProveedor = idProveedor;
        this.idCategoria = idCategoria;
        this.nombreIngrediente = nombreIngrediente;
        this.cantidadIngrediente = cantidadIngrediente;
        this.fechaVencimiento = fechaVencimiento;
        this.referenciaIngrediente = referenciaIngrediente;
        this.fechaEntregaIngrediente = fechaEntregaIngrediente;
    }

    // Getters y Setters
    public int getIdIngrediente() { return idIngrediente; }
    public void setIdIngrediente(int idIngrediente) { this.idIngrediente = idIngrediente; }

    public int getIdProveedor() { return idProveedor; }
    public void setIdProveedor(int idProveedor) { this.idProveedor = idProveedor; }

    public int getIdCategoria() { return idCategoria; }
    public void setIdCategoria(int idCategoria) { this.idCategoria = idCategoria; }

    public String getNombreIngrediente() { return nombreIngrediente; }
    public void setNombreIngrediente(String nombreIngrediente) { this.nombreIngrediente = nombreIngrediente; }

    public int getCantidadIngrediente() { return cantidadIngrediente; }
    public void setCantidadIngrediente(int cantidadIngrediente) { this.cantidadIngrediente = cantidadIngrediente; }

    public Date getFechaVencimiento() { return fechaVencimiento; }
    public void setFechaVencimiento(Date fechaVencimiento) { this.fechaVencimiento = fechaVencimiento; }

    public String getReferenciaIngrediente() { return referenciaIngrediente; }
    public void setReferenciaIngrediente(String referenciaIngrediente) { this.referenciaIngrediente = referenciaIngrediente; }

    public Date getFechaEntregaIngrediente() { return fechaEntregaIngrediente; }
    public void setFechaEntregaIngrediente(Date fechaEntregaIngrediente) { this.fechaEntregaIngrediente = fechaEntregaIngrediente; }
}
