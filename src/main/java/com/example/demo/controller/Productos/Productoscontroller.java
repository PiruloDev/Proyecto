package com.example.demo.controller.Productos;

import com.example.demo.Productos;
import com.example.demo.Service.Productos.ProductosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class Productoscontroller {

    @Autowired
    private ProductosService productosService;

    // GET → obtener todos los productos
    @GetMapping("/productos") // Endpoint GET
    public List<String> obtenerProductos() {
        return productosService.obtenerProductos();
    }

    // POST → crear un nuevo producto
    @PostMapping("/productosnuevos") // Endpoint POST
    public ResponseEntity<String> crearProducto(@RequestBody Productos producto) {
        try {
            productosService.crearProducto(producto);
            System.out.println("Producto recibido: " + producto.getNombreProducto());
            return new ResponseEntity<>("Producto " + producto.getNombreProducto() + " creado con éxito.", HttpStatus.CREATED);
        } catch (Exception e) {
            return new ResponseEntity<>("Error al crear el producto: " + e.getMessage(), HttpStatus.INTERNAL_SERVER_ERROR);
        }
    }
}
