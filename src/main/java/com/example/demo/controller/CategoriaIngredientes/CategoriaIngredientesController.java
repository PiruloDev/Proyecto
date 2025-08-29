package com.example.demo.controller.CategoriaIngredientes;



import com.example.demo.Service.CategoriaIngredientesService.CategoriaIngredientes;
import com.example.demo.Service.CategoriaIngredientesService.CategoriaIngredientesService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping
public class CategoriaIngredientesController {

    @Autowired
    private CategoriaIngredientesService categoriaIngredientesService;

    // GET - Obtener todas las categorías de ingredientes
    @GetMapping("/categorias/ingredientes")
    public List<CategoriaIngredientes> obtenerTodasLasCategoriasIngredientes() {
        return categoriaIngredientesService.obtenerTodasLasCategoriasIngredientes();
    }

    // POST - Crear una nueva categoría de ingrediente
    @PostMapping("/nuevacategoriaingrediente")
    public ResponseEntity<String> crearCategoriaIngrediente(@RequestBody CategoriaIngredientes categoria) {
        categoriaIngredientesService.crearCategoriaIngrediente(categoria);
        return ResponseEntity.ok("Categoría " + categoria.getNombreCategoria() + " creada con éxito.");
    }

    // PUT - Actualizar una categoría existente
    @PutMapping("categoriaingrediente/{id}")
    public ResponseEntity<String> editarCategoriaIngrediente(@PathVariable int id, @RequestBody CategoriaIngredientes categoria) {
        categoria.setIdCategoriaIngrediente(id);
        int filas = categoriaIngredientesService.editarCategoriaIngrediente(categoria);
        if (filas > 0) {
            return ResponseEntity.ok("Categoría de ingrediente con ID " + id + " actualizada correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    // DELETE - Eliminar una categoría por ID
    @DeleteMapping("eliminarcategoria/{id}")
    public ResponseEntity<String> eliminarCategoriaIngrediente(@PathVariable int id) {
        int filas = categoriaIngredientesService.eliminarCategoriaIngrediente(id);
        if (filas > 0) {
            return ResponseEntity.ok("Categoría de ingrediente con ID " + id + " eliminada correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}
