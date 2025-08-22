package com.example.demo;

import java.util.Date;

public class Productos {
    private int idProducto;
    private int idAdmin;
    private int idCategoriaProducto;
    private String nombreProducto;
    private String descripcionProducto;
    private int productoStockMin;
    private double precioProducto;
    private Date fechaVencimientoProducto;
    private Date fechaIngresoProducto;
    private String tipoProductoMarca;
    private boolean activo;  // DEFAULT TRUE en la BD
    private Date fechaUltimaModificacion;

    // Constructor vacío
    public Productos() {}

    // Constructor con parámetros
    public Productos(int idProducto, int idAdmin, int idCategoriaProducto, String nombreProducto,
                     String descripcionProducto, int productoStockMin, double precioProducto,
                     Date fechaVencimientoProducto, Date fechaIngresoProducto,
                     String tipoProductoMarca, boolean activo, Date fechaUltimaModificacion) {
        this.idProducto = idProducto;
        this.idAdmin = idAdmin;
        this.idCategoriaProducto = idCategoriaProducto;
        this.nombreProducto = nombreProducto;
        this.descripcionProducto = descripcionProducto;
        this.productoStockMin = productoStockMin;
        this.precioProducto = precioProducto;
        this.fechaVencimientoProducto = fechaVencimientoProducto;
        this.fechaIngresoProducto = fechaIngresoProducto;
        this.tipoProductoMarca = tipoProductoMarca;
        this.activo = activo;
        this.fechaUltimaModificacion = fechaUltimaModificacion;
    }

    // Getters y Setters
    public int getIdProducto() { return idProducto; }
    public void setIdProducto(int idProducto) { this.idProducto = idProducto; }

    public int getIdAdmin() { return idAdmin; }
    public void setIdAdmin(int idAdmin) { this.idAdmin = idAdmin; }

    public int getIdCategoriaProducto() { return idCategoriaProducto; }
    public void setIdCategoriaProducto(int idCategoriaProducto) { this.idCategoriaProducto = idCategoriaProducto; }

    public String getNombreProducto() { return nombreProducto; }
    public void setNombreProducto(String nombreProducto) { this.nombreProducto = nombreProducto; }

    public String getDescripcionProducto() { return descripcionProducto; }
    public void setDescripcionProducto(String descripcionProducto) { this.descripcionProducto = descripcionProducto; }

    public int getProductoStockMin() { return productoStockMin; }
    public void setProductoStockMin(int productoStockMin) { this.productoStockMin = productoStockMin; }

    public double getPrecioProducto() { return precioProducto; }
    public void setPrecioProducto(double precioProducto) { this.precioProducto = precioProducto; }

    public Date getFechaVencimientoProducto() { return fechaVencimientoProducto; }
    public void setFechaVencimientoProducto(Date fechaVencimientoProducto) { this.fechaVencimientoProducto = fechaVencimientoProducto; }

    public Date getFechaIngresoProducto() { return fechaIngresoProducto; }
    public void setFechaIngresoProducto(Date fechaIngresoProducto) { this.fechaIngresoProducto = fechaIngresoProducto; }

    public String getTipoProductoMarca() { return tipoProductoMarca; }
    public void setTipoProductoMarca(String tipoProductoMarca) { this.tipoProductoMarca = tipoProductoMarca; }

    public boolean isActivo() { return activo; }
    public void setActivo(boolean activo) { this.activo = activo; }

    public Date getFechaUltimaModificacion() { return fechaUltimaModificacion; }
    public void setFechaUltimaModificacion(Date fechaUltimaModificacion) { this.fechaUltimaModificacion = fechaUltimaModificacion; }
}
