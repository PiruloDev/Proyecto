package com.example.Proyecto.controller;

import com.example.Proyecto.model.PojoCategoria_Productos;
import com.example.Proyecto.service.CategoriaProductos.CategoriaProductosService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/categorias")
@Tag(name = "Categorías de Productos", description = "Gestión de categorías de productos")
public class CategoriaProductosController {

    @Autowired
    private CategoriaProductosService service;


    @Operation(summary = "Listar todas las categorías", description = "Retorna la lista completa de categorías de productos")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping
    public List<PojoCategoria_Productos> listarCategorias() {
        return service.obtenerCategorias();
    }


    @Operation(summary = "Crear categoría", description = "Registra una nueva categoría de productos")
    @ApiResponse(responseCode = "200", description = "Categoría creada exitosamente")
    @PostMapping
    public String crearCategoria(
        @Parameter(description = "Datos de la categoría", required = true)
        @RequestBody PojoCategoria_Productos categoria) {
        int result = service.agregarCategoria(categoria);
        return result == 1 ? "Categoría creada correctamente" : "Error al crear la categoría";
    }

    @Operation(summary = "Actualizar categoría", description = "Actualiza una categoría existente")
    @ApiResponse(responseCode = "200", description = "Categoría actualizada exitosamente")
    @PutMapping("/{id}")
    public String actualizarCategoria(
        @Parameter(description = "ID de la categoría", required = true)
        @PathVariable int id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody PojoCategoria_Productos categoria) {
        categoria.setIdCategoriaProducto(id);
        int result = service.actualizarCategoria(categoria);
        return result == 1 ? "Categoría actualizada correctamente" : "Error al actualizar la categoría";
    }

    @Operation(summary = "Eliminar categoría", description = "Elimina una categoría de productos")
    @ApiResponse(responseCode = "200", description = "Categoría eliminada exitosamente")
    @DeleteMapping("/{id}")
    public String eliminarCategoria(
        @Parameter(description = "ID de la categoría", required = true)
        @PathVariable int id) {
        int result = service.eliminarCategoria(id);
        return result == 1 ? "Categoría eliminada correctamente" : "Error al eliminar la categoría";
    }
}
