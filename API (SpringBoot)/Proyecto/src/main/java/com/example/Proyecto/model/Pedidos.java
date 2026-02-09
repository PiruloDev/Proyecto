package com.example.Proyecto.model;

import com.example.Proyecto.service.DetallePedidos.DetallePedidos;
import com.fasterxml.jackson.annotation.JsonIgnoreProperties;
import com.fasterxml.jackson.annotation.JsonProperty;
import com.fasterxml.jackson.annotation.JsonFormat;

import java.math.BigDecimal;
import java.time.LocalDateTime;
import java.util.List;

@JsonIgnoreProperties(ignoreUnknown = true)
public class Pedidos {

    private int ID_PEDIDO;

    // IDs de referencia (Mapeados para recibir datos de Laravel)
    @JsonProperty("cliente_id")
    private long ID_CLIENTE;

    @JsonProperty("empleado_id")
    private long ID_EMPLEADO;

    @JsonProperty("estado_pedido_id")
    private long ID_ESTADO_PEDIDO;

    // --- NUEVOS CAMPOS PARA MOSTRAR NOMBRES EN LA TABLA ---
    @JsonProperty("nombre_cliente")
    private String nombreCliente;

    @JsonProperty("nombre_empleado")
    private String nombreEmpleado;

    @JsonProperty("nombre_estado")
    private String nombreEstado;

    // --- MANEJO DE FECHAS (FORMATO FLEXIBLE) ---
    @JsonProperty("fecha_ingreso")
    @JsonFormat(pattern = "yyyy-MM-dd[ HH:mm:ss]")
    private LocalDateTime FECHA_INGRESO;

    @JsonProperty("fecha_entrega")
    @JsonFormat(pattern = "yyyy-MM-dd[ HH:mm:ss]")
    private LocalDateTime FECHA_ENTREGA;

    @JsonProperty("total_producto")
    private BigDecimal TOTAL_PRODUCTO;

    // --- RELACIÓN CON DETALLES (Agregado para guardado automático) ---
    @JsonProperty("detalles")
    private List<DetallePedidos> detalles;

    // --- CONSTRUCTOR VACÍO ---
    public Pedidos() {}

    // --- GETTERS Y SETTERS ---

    public int getID_PEDIDO() { return ID_PEDIDO; }
    public void setID_PEDIDO(int ID_PEDIDO) { this.ID_PEDIDO = ID_PEDIDO; }

    public long getID_CLIENTE() { return ID_CLIENTE; }
    public void setID_CLIENTE(long ID_CLIENTE) { this.ID_CLIENTE = ID_CLIENTE; }

    public long getID_EMPLEADO() { return ID_EMPLEADO; }
    public void setID_EMPLEADO(long ID_EMPLEADO) { this.ID_EMPLEADO = ID_EMPLEADO; }

    public long getID_ESTADO_PEDIDO() { return ID_ESTADO_PEDIDO; }
    public void setID_ESTADO_PEDIDO(long ID_ESTADO_PEDIDO) { this.ID_ESTADO_PEDIDO = ID_ESTADO_PEDIDO; }

    public String getNombreCliente() { return nombreCliente; }
    public void setNombreCliente(String nombreCliente) { this.nombreCliente = nombreCliente; }

    public String getNombreEmpleado() { return nombreEmpleado; }
    public void setNombreEmpleado(String nombreEmpleado) { this.nombreEmpleado = nombreEmpleado; }

    public String getNombreEstado() { return nombreEstado; }
    public void setNombreEstado(String nombreEstado) { this.nombreEstado = nombreEstado; }

    public LocalDateTime getFECHA_INGRESO() { return FECHA_INGRESO; }
    public void setFECHA_INGRESO(LocalDateTime FECHA_INGRESO) { this.FECHA_INGRESO = FECHA_INGRESO; }

    public LocalDateTime getFECHA_ENTREGA() { return FECHA_ENTREGA; }
    public void setFECHA_ENTREGA(LocalDateTime FECHA_ENTREGA) { this.FECHA_ENTREGA = FECHA_ENTREGA; }

    public BigDecimal getTOTAL_PRODUCTO() { return TOTAL_PRODUCTO; }
    public void setTOTAL_PRODUCTO(BigDecimal TOTAL_PRODUCTO) { this.TOTAL_PRODUCTO = TOTAL_PRODUCTO; }

    // Getters y Setters de la lista de detalles
    public List<DetallePedidos> getDetalles() { return detalles; }
    public void setDetalles(List<DetallePedidos> detalles) { this.detalles = detalles; }
}