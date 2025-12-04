package com.example.Proyecto.model;

import java.math.BigDecimal;

/**
 * Clase de modelo que representa un ingrediente individual
 * dentro de la receta de un producto, mapeando las columnas
 * de la tabla RECETAS_DETALLE.
 */
public class RecetaProducto {

    // IDs cambiados a Long para máxima compatibilidad con Spring y la base de datos
    private Long idReceta;
    private Long idProducto; // Útil si la consulta hace un JOIN con RECETAS
    private Long idIngrediente;

    private BigDecimal cantidadRequerida; // Usamos BigDecimal para precisión en cantidades

    // CORRECCIÓN CLAVE: Usamos el ID de la unidad en lugar del nombre
    private Long idUnidad;

    // Constructor vacío
    public RecetaProducto() {}

    // ==========================================================
    // Getters y Setters
    // ==========================================================

    public Long getIdReceta() {
        return idReceta;
    }

    public void setIdReceta(Long idReceta) {
        this.idReceta = idReceta;
    }

    public Long getIdProducto() {
        return idProducto;
    }

    public void setIdProducto(Long idProducto) {
        this.idProducto = idProducto;
    }

    public Long getIdIngrediente() {
        return idIngrediente;
    }

    public void setIdIngrediente(Long idIngrediente) {
        this.idIngrediente = idIngrediente;
    }

    public BigDecimal getCantidadRequerida() {
        return cantidadRequerida;
    }

    public void setCantidadRequerida(BigDecimal cantidadRequerida) {
        this.cantidadRequerida = cantidadRequerida;
    }

    public Long getIdUnidad() {
        return idUnidad;
    }

    public void setIdUnidad(Long idUnidad) {
        this.idUnidad = idUnidad;
    }
}