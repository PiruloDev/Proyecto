    package com.example.Proyecto.dto;

    import java.util.List;

    public class RecetaRequest {

        // 1. Campo principal: El ID del producto al que pertenece la receta
        private Long idProducto;

        // 2. Campo complejo: La lista de ingredientes y cantidades
        private List<IngredienteReceta> ingredientes;

        // ====== Getters y Setters de RecetaRequest ======

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

        // ====== Clase interna DTO para los Ingredientes de la Receta ======

        /**
         * DTO interno que representa un solo ingrediente dentro de la lista de la receta.
         * Mapea el objeto complejo 'ingredientes' del JSON.
         */
        public static class IngredienteReceta {
            private Long idIngrediente;
            private Double cantidadNecesaria;
            private String unidadMedida; // <-- ¡NUEVO CAMPO!

            public Long getIdIngrediente() {
                return idIngrediente;
            }

            public void setIdIngrediente(Long idIngrediente) {
                this.idIngrediente = idIngrediente;
            }

            public Double getCantidadNecesaria() {
                return cantidadNecesaria;
            }

            public void setCantidadNecesaria(Double cantidadNecesaria) {
                this.cantidadNecesaria = cantidadNecesaria;
            }

            // NUEVOS Getters y Setters para unidadMedida
            public String getUnidadMedida() {
                return unidadMedida;
            }

            public void setUnidadMedida(String unidadMedida) {
                this.unidadMedida = unidadMedida;
            }
        }
    }