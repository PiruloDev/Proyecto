package com.example.Proyecto.model;

import java.math.BigDecimal;
import java.time.LocalDateTime;

/**
 * Clase de modelo para representar un registro de Producción.
 * Se utiliza para registrar la cantidad de un producto terminado que se ha elaborado
 * y es la entidad central para el historial de producción y la reversión de inventario.
 */
public class Produccion {

    private Long idProduccion; // Clave primaria del registro de producción
    private Long idProducto;   // Clave foránea al producto que se elaboró
    private BigDecimal cantidadProducida; // Cantidad de unidades terminadas producidas
    private LocalDateTime fechaProduccion; // Fecha y hora en que se registró la producción

    // Constructor vacío (necesario para Spring/JPA/Deserialización)
    public Produccion() {}

    // Constructor con todos los campos
    public Produccion(Long idProduccion, Long idProducto, BigDecimal cantidadProducida, LocalDateTime fechaProduccion) {
        this.idProduccion = idProduccion;
        this.idProducto = idProducto;
        this.cantidadProducida = cantidadProducida;
        this.fechaProduccion = fechaProduccion;
    }

    // ==========================================================
    // Getters y Setters
    // ==========================================================

    public Long getIdProduccion() {
        return idProduccion;
    }

    public void setIdProduccion(Long idProduccion) {
        this.idProduccion = idProduccion;
    }

    public Long getIdProducto() {
        return idProducto;
    }

    public void setIdProducto(Long idProducto) {
        this.idProducto = idProducto;
    }

    public BigDecimal getCantidadProducida() {
        return cantidadProducida;
    }

    public void setCantidadProducida(BigDecimal cantidadProducida) {
        this.cantidadProducida = cantidadProducida;
    }

    public LocalDateTime getFechaProduccion() {
        return fechaProduccion;
    }

    public void setFechaProduccion(LocalDateTime fechaProduccion) {
        this.fechaProduccion = fechaProduccion;
    }
}
