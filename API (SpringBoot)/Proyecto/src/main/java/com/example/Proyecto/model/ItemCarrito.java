package com.example.Proyecto.model;

/**
 * Representa un producto individual dentro del array 'items' del PedidoRequest.
 * Esta estructura mapea los datos de cada producto que Laravel envía desde su carrito.
 */
public class ItemCarrito {

    private Integer id;
    private String nombre;
    private Double precio;
    private Integer cantidad;

    // --- Getters y Setters ---

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public Double getPrecio() {
        return precio;
    }

    public void setPrecio(Double precio) {
        this.precio = precio;
    }

    public Integer getCantidad() {
        return cantidad;
    }

    public void setCantidad(Integer cantidad) {
        this.cantidad = cantidad;
    }
}
