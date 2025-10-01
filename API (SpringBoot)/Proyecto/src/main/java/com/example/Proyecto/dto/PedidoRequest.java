// src/main/java/com/example/Proyecto/dto/PedidoRequest.java

package com.example.Proyecto.dto;

import java.math.BigDecimal;
import java.util.List;

public class PedidoRequest {

    private Long idUsuario; // Mapea a ID_CLIENTE
    private BigDecimal totalPedido;
    private List<DetallePedidoRequest> detalles;

    public static class DetallePedidoRequest {
        private Long idProducto;
        private Integer cantidad;
        private BigDecimal precioUnitario;

        // Getters y Setters
        public Long getIdProducto() { return idProducto; }
        public void setIdProducto(Long idProducto) { this.idProducto = idProducto; }
        public Integer getCantidad() { return cantidad; }
        public void setCantidad(Integer cantidad) { this.cantidad = cantidad; }
        public BigDecimal getPrecioUnitario() { return precioUnitario; }
        public void setPrecioUnitario(BigDecimal precioUnitario) { this.precioUnitario = precioUnitario; }
    }

    // Getters y Setters de PedidoRequest
    public Long getIdUsuario() { return idUsuario; }
    public void setIdUsuario(Long idUsuario) { this.idUsuario = idUsuario; }
    public BigDecimal getTotalPedido() { return totalPedido; }
    public void setTotalPedido(BigDecimal totalPedido) { this.totalPedido = totalPedido; }
    public List<DetallePedidoRequest> getDetalles() { return detalles; }
    public void setDetalles(List<DetallePedidoRequest> detalles) { this.detalles = detalles; }
}