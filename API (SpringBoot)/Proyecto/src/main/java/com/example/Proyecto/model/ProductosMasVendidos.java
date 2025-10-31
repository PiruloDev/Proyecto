package com.example.Proyecto.model;

import java.math.BigDecimal;

public class ProductosMasVendidos {
    private Long idProducto;
    private String nombreProducto;
    private String descripcionProducto;
    private BigDecimal precioProducto;
    private Integer stockMin;
    private Long cantidadVendida;
    // getters y setters

    // Constructor
    public ProductosMasVendidos(Long idProducto, String nombreProducto, String descripcionProducto,
                                BigDecimal precioProducto, Integer stockMin, Long cantidadVendida) {
        this.idProducto = idProducto;
        this.nombreProducto = nombreProducto;
        this.descripcionProducto = descripcionProducto;
        this.precioProducto = precioProducto;
        this.stockMin = stockMin;
        this.cantidadVendida = cantidadVendida;
    }

    // Getters y Setters
    public Long getIdProducto() { return idProducto; }
    public void setIdProducto(Long idProducto) { this.idProducto = idProducto; }

    public String getNombreProducto() { return nombreProducto; }
    public void setNombreProducto(String nombreProducto) { this.nombreProducto = nombreProducto; }

    public String getDescripcionProducto() { return descripcionProducto; }
    public void setDescripcionProducto(String descripcionProducto) { this.descripcionProducto = descripcionProducto; }

    public BigDecimal getPrecioProducto() { return precioProducto; }
    public void setPrecioProducto(BigDecimal precioProducto) { this.precioProducto = precioProducto; }

    public Integer getStockMin() { return stockMin; }
    public void setStockMin(Integer stockMin) { this.stockMin = stockMin; }

    public Long getCantidadVendida() { return cantidadVendida; }
    public void setCantidadVendida(Long cantidadVendida) { this.cantidadVendida = cantidadVendida; }
}
