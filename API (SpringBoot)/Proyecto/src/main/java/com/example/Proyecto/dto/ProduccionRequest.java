package com.example.Proyecto.dto;

import java.math.BigDecimal; // Importado para precisión
import java.util.List;

public class ProduccionRequest {

    private Long idProducto;
    // CORRECCIÓN: Usamos BigDecimal para permitir cantidades fraccionarias y mantener precisión
    private BigDecimal cantidadProducida;

    // Lista de ingredientes manualmente descontados (si aplica)
    private List<IngredienteDescontado> ingredientesDescontados;

    // ====== Getters y Setters de ProduccionRequest ======
    public Long getIdProducto() { return idProducto; }
    public void setIdProducto(Long idProducto) { this.idProducto = idProducto; }

    public BigDecimal getCantidadProducida() { return cantidadProducida; }
    public void setCantidadProducida(BigDecimal cantidadProducida) { this.cantidadProducida = cantidadProducida; }

    public List<IngredienteDescontado> getIngredientesDescontados() { return ingredientesDescontados; }
    public void setIngredientesDescontados(List<IngredienteDescontado> ingredientesDescontados) {
        this.ingredientesDescontados = ingredientesDescontados;
    }

    // ====== Clase interna DTO para ingredientes descontados ======
    public static class IngredienteDescontado {
        private Long idIngrediente;
        // CORRECCIÓN: Usamos BigDecimal para evitar pérdidas de precisión en el descuento de stock
        private BigDecimal cantidadUsada;

        public Long getIdIngrediente() { return idIngrediente; }
        public void setIdIngrediente(Long idIngrediente) { this.idIngrediente = idIngrediente; }

        public BigDecimal getCantidadUsada() { return cantidadUsada; }
        public void setCantidadUsada(BigDecimal cantidadUsada) { this.cantidadUsada = cantidadUsada; }
    }
}