package com.example.Proyecto.model;

import java.time.LocalDate;
import java.math.BigDecimal;
import com.fasterxml.jackson.annotation.JsonFormat;
import com.fasterxml.jackson.annotation.JsonProperty;

public class PojoProductos {

    @JsonProperty("ID_PRODUCTO")
    private int id;

    @JsonProperty("ID_ADMIN")
    private int idAdmin;

    @JsonProperty("ID_CATEGORIA_PRODUCTO")
    private int idCategoriaProducto;

    @JsonProperty("NOMBRE_PRODUCTO")
    private String nombreProducto;

    @JsonProperty("DESCRIPCION_PRODUCTO")
    private String descripcionProducto;

    @JsonProperty("PRODUCTO_STOCK_MIN")
    private int stockMinimo;

    @JsonProperty("PRECIO_PRODUCTO")
    private BigDecimal precio;

    @JsonFormat(pattern = "yyyy-MM-dd")
    @JsonProperty("FECHA_VENCIMIENTO_PRODUCTO")
    private LocalDate fechaVencimiento;

    @JsonFormat(pattern = "yyyy-MM-dd")
    @JsonProperty("FECHA_INGRESO_PRODUCTO")
    private LocalDate fechaIngreso;

    @JsonProperty("TIPO_PRODUCTO_MARCA")
    private String marcaProducto;

    @JsonProperty("ACTIVO")
    private boolean activo = true;

    @JsonProperty("IMAGEN_URL_PRODUCTO")
    private String imagenUrl;

    public PojoProductos() {}

    // Getters y Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getIdAdmin() {
        return idAdmin;
    }

    public void setIdAdmin(int idAdmin) {
        this.idAdmin = idAdmin;
    }

    public int getIdCategoriaProducto() {
        return idCategoriaProducto;
    }

    public void setIdCategoriaProducto(int idCategoriaProducto) {
        this.idCategoriaProducto = idCategoriaProducto;
    }

    public String getNombreProducto() {
        return nombreProducto;
    }

    public void setNombreProducto(String nombreProducto) {
        this.nombreProducto = nombreProducto;
    }

    public String getDescripcionProducto() {
        return descripcionProducto;
    }

    public void setDescripcionProducto(String descripcionProducto) {
        this.descripcionProducto = descripcionProducto;
    }

    public int getStockMinimo() {
        return stockMinimo;
    }

    public void setStockMinimo(int stockMinimo) {
        this.stockMinimo = stockMinimo;
    }

    public BigDecimal getPrecio() {
        return precio;
    }

    public void setPrecio(BigDecimal precio) {
        this.precio = precio;
    }

    public LocalDate getFechaVencimiento() {
        return fechaVencimiento;
    }

    public void setFechaVencimiento(LocalDate fechaVencimiento) {
        this.fechaVencimiento = fechaVencimiento;
    }

    public LocalDate getFechaIngreso() {
        return fechaIngreso;
    }

    public void setFechaIngreso(LocalDate fechaIngreso) {
        this.fechaIngreso = fechaIngreso;
    }

    public String getMarcaProducto() {
        return marcaProducto;
    }

    public void setMarcaProducto(String marcaProducto) {
        this.marcaProducto = marcaProducto;
    }

    public boolean isActivo() {
        return activo;
    }

    public void setActivo(boolean activo) {
        this.activo = activo;
    }

    public String getImagenUrl() {
        return imagenUrl;
    }

    public void setImagenUrl(String imagenUrl) {
        this.imagenUrl = imagenUrl;
    }

    @Override
    public String toString() {
        return "PojoProductos{" +
                "id=" + id +
                ", nombreProducto='" + nombreProducto + '\'' +
                ", idCategoriaProducto=" + idCategoriaProducto +
                ", precio=" + precio +
                ", stockMinimo=" + stockMinimo +
                ", activo=" + activo +
                '}';
    }
}