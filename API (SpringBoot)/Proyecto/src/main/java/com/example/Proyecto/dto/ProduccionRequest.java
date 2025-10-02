package com.example.Proyecto.dto;

import java.util.List;

public class ProduccionRequest {
    private Long idProducto;
    private Integer cantidadProducida;
    private List<IngredienteDescontado> ingredientesDescontados; // 👈 nuevo campo

    // ====== Getters y Setters ======
    public Long getIdProducto() { return idProducto; }
    public void setIdProducto(Long idProducto) { this.idProducto = idProducto; }

    public Integer getCantidadProducida() { return cantidadProducida; }
    public void setCantidadProducida(Integer cantidadProducida) { this.cantidadProducida = cantidadProducida; }

    public List<IngredienteDescontado> getIngredientesDescontados() { return ingredientesDescontados; }
    public void setIngredientesDescontados(List<IngredienteDescontado> ingredientesDescontados) {
        this.ingredientesDescontados = ingredientesDescontados;
    }

    // ====== Clase interna DTO para ingredientes ======
    public static class IngredienteDescontado {
        private Long idIngrediente;
        private Double cantidadUsada;

        public Long getIdIngrediente() { return idIngrediente; }
        public void setIdIngrediente(Long idIngrediente) { this.idIngrediente = idIngrediente; }

        public Double getCantidadUsada() { return cantidadUsada; }
        public void setCantidadUsada(Double cantidadUsada) { this.cantidadUsada = cantidadUsada; }
    }
}
