package com.example.Proyecto.dto;

import java.math.BigDecimal;
import java.util.List;

/**
 * DTO utilizado para recibir los datos de una nueva receta o una actualización.
 * Contiene el ID del producto y la lista de ingredientes requeridos.
 */
public class RecetaRequest {

    // El ID del producto al que pertenece la receta
    private Long idProducto;

    // La lista de ingredientes y sus cantidades
    private List<IngredienteReceta> ingredientes;

    // ==========================================================
    // Getters y Setters de RecetaRequest
    // ==========================================================

    public Long getIdProducto() {
        return idProducto;
    }

    public void setIdProducto(Long idProducto) {
        this.idProducto = idProducto;
    }

    public List<IngredienteReceta> getIngredientes() {
        return ingredientes;
    }

    public void setIngredientes(List<IngredienteReceta> ingredientes) {
        this.ingredientes = ingredientes;
    }

    // ==========================================================
    // Clase interna DTO: IngredienteReceta
    // ==========================================================

    /**
     * DTO interno que representa un solo ingrediente dentro de la lista de la receta.
     */
    public static class IngredienteReceta {

        private Long idIngrediente;
        // Se usa BigDecimal para evitar errores de precisión con la cantidad
        private BigDecimal cantidadNecesaria;
        // Se usa el ID de la unidad (Long), consistente con la DB
        private Long idUnidad;

        // Getters
        public Long getIdIngrediente() {
            return idIngrediente;
        }

        public BigDecimal getCantidadNecesaria() {
            return cantidadNecesaria;
        }

        public Long getIdUnidad() {
            return idUnidad;
        }

        // Setters
        public void setIdIngrediente(Long idIngrediente) {
            this.idIngrediente = idIngrediente;
        }

        public void setCantidadNecesaria(BigDecimal cantidadNecesaria) {
            this.cantidadNecesaria = cantidadNecesaria;
        }

        public void setIdUnidad(Long idUnidad) {
            this.idUnidad = idUnidad;
        }
    }
}