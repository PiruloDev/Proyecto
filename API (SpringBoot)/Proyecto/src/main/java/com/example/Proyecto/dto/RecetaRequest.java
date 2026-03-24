package com.example.Proyecto.dto;

import java.math.BigDecimal;
import java.util.List;

import com.fasterxml.jackson.annotation.JsonProperty;

public class RecetaRequest {

    private Long idProducto;
    private List<IngredienteReceta> ingredientes;

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

    // ==========================================
    public static class IngredienteReceta {

        private Long idIngrediente;

        // 🔥 MAPEA LO QUE ENVÍA LARAVEL
        @JsonProperty("cantidadRequerida")
        private BigDecimal cantidadNecesaria;

        private Long idUnidad;

        public Long getIdIngrediente() {
            return idIngrediente;
        }

        public BigDecimal getCantidadNecesaria() {
            return cantidadNecesaria;
        }

        public Long getIdUnidad() {
            return idUnidad;
        }

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