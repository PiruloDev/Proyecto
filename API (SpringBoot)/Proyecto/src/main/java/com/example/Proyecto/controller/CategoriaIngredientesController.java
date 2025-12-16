package com.example.Proyecto.controller;
import com.example.Proyecto.controller.CategoriaIngredientesController;
import com.example.Proyecto.service.CategoriaIngredientesService.CategoriaIngredientes;
import com.example.Proyecto.controller.CategoriaIngredientesController;
import com.example.Proyecto.service.CategoriaIngredientesService.CategoriaIngredientesService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping
@Tag(name = "Categorías de Ingredientes", description = "Gestión de categorías de ingredientes")
public class CategoriaIngredientesController {

    @Autowired
    private CategoriaIngredientesService categoriaIngredientesService;

    @Operation(summary = "Obtener categorías de ingredientes", description = "Retorna todas las categorías de ingredientes")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping("/categorias/ingredientes")
    public List<CategoriaIngredientes> obtenerTodasLasCategoriasIngredientes() {
        return categoriaIngredientesService.obtenerTodasLasCategoriasIngredientes();
    }

    @Operation(summary = "Crear categoría de ingrediente", description = "Registra una nueva categoría de ingredientes")
    @ApiResponse(responseCode = "200", description = "Categoría creada exitosamente")
    @PostMapping("/nuevacategoriaingrediente")
    public ResponseEntity<String> crearCategoriaIngrediente(
        @Parameter(description = "Datos de la categoría", required = true)
        @RequestBody CategoriaIngredientes categoria) {
        categoriaIngredientesService.crearCategoriaIngrediente(categoria);
        return ResponseEntity.ok("Categoría " + categoria.getNombreCategoria() + " creada con éxito.");
    }

    @Operation(summary = "Editar categoría de ingrediente", description = "Actualiza una categoría existente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Categoría actualizada"),
        @ApiResponse(responseCode = "404", description = "Categoría no encontrada")
    })
    @PutMapping("categoriaingrediente/{id}")
    public ResponseEntity<String> editarCategoriaIngrediente(
        @Parameter(description = "ID de la categoría", required = true)
        @PathVariable int id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody CategoriaIngredientes categoria) {
        categoria.setIdCategoriaIngrediente(id);
        int filas = categoriaIngredientesService.editarCategoriaIngrediente(categoria);
        if (filas > 0) {
            return ResponseEntity.ok("Categoría de ingrediente con ID " + id + " actualizada correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    @Operation(summary = "Eliminar categoría de ingrediente", description = "Elimina una categoría de ingredientes")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Categoría eliminada"),
        @ApiResponse(responseCode = "404", description = "Categoría no encontrada")
    })
    @DeleteMapping("eliminarcategoria/{id}")
    public ResponseEntity<String> eliminarCategoriaIngrediente(
        @Parameter(description = "ID de la categoría", required = true)
        @PathVariable int id) {
        int filas = categoriaIngredientesService.eliminarCategoriaIngrediente(id);
        if (filas > 0) {
            return ResponseEntity.ok("Categoría de ingrediente con ID " + id + " eliminada correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}
