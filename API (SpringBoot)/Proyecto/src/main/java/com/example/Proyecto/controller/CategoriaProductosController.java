package com.example.Proyecto.controller;

import com.example.Proyecto.model.PojoCategoria_Productos;
import com.example.Proyecto.service.CategoriaProductos.CategoriaProductosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/categorias")
public class CategoriaProductosController {

    @Autowired
    private CategoriaProductosService service;


    @GetMapping
    public List<PojoCategoria_Productos> listarCategorias() {
        return service.obtenerCategorias();
    }


    @PostMapping
    public String crearCategoria(@RequestBody PojoCategoria_Productos categoria) {
        int result = service.agregarCategoria(categoria);
        return result == 1 ? "Categoría creada correctamente" : "Error al crear la categoría";
    }

    @PutMapping("/{id}")
    public String actualizarCategoria(@PathVariable int id, @RequestBody PojoCategoria_Productos categoria) {
        categoria.setIdCategoriaProducto(id);
        int result = service.actualizarCategoria(categoria);
        return result == 1 ? "Categoría actualizada correctamente" : "Error al actualizar la categoría";
    }

    @DeleteMapping("/{id}")
    public String eliminarCategoria(@PathVariable int id) {
        int result = service.eliminarCategoria(id);
        return result == 1 ? "Categoría eliminada correctamente" : "Error al eliminar la categoría";
    }
}
