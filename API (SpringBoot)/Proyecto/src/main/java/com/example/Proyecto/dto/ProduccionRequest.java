// src/main/java/com/example/Proyecto/dto/ProduccionRequest.java

package com.example.Proyecto.dto;

import java.math.BigDecimal;
import java.util.List;

public class ProduccionRequest {

    private Long idProducto;
    private Integer cantidadProducida;
    private List<IngredienteDescontado> ingredientesDescontados;

    public static class IngredienteDescontado {
        private Long idIngrediente;
        private BigDecimal cantidadUsada; // Cantidad a descontar (ej: 5.000 KG)

        // Getters y Setters
        public Long getIdIngrediente() { return idIngrediente; }
        public void setIdIngrediente(Long idIngrediente) { this.idIngrediente = idIngrediente; }
        public BigDecimal getCantidadUsada() { return cantidadUsada; }
        public void setCantidadUsada(BigDecimal cantidadUsada) { this.cantidadUsada = cantidadUsada; }
    }

    // Getters y Setters de ProduccionRequest
    public Long getIdProducto() { return idProducto; }
    public void setIdProducto(Long idProducto) { this.idProducto = idProducto; }
    public Integer getCantidadProducida() { return cantidadProducida; }
    public void setCantidadProducida(Integer cantidadProducida) { this.cantidadProducida = cantidadProducida; }
    public List<IngredienteDescontado> getIngredientesDescontados() { return ingredientesDescontados; }
    public void setIngredientesDescontados(List<IngredienteDescontado> ingredientesDescontados) { this.ingredientesDescontados = ingredientesDescontados; }
}