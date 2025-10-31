    package com.example.Proyecto.controller;

    import com.example.Proyecto.dto.RecetaRequest;
    import com.example.Proyecto.model.RecetaProducto;
    import com.example.Proyecto.service.Inventario.RecetasService;
    import org.springframework.beans.factory.annotation.Autowired;
    import org.springframework.http.HttpStatus;
    import org.springframework.http.ResponseEntity;
    import org.springframework.web.bind.annotation.*;

    import java.util.HashMap;
    import java.util.List;
    import java.util.Map;

    @RestController
    @RequestMapping("/inventario/recetas")
    public class RecetasController {
    
        @Autowired
        private RecetasService recetasService;
    
        // URL: POST http://localhost:8080/inventario/recetas
        // Crea una nueva receta para un producto
        @PostMapping
        public ResponseEntity<Map<String, Object>> crearReceta(@RequestBody RecetaRequest request) {
            Map<String, Object> response = new HashMap<>();
            try {
                // Se asume que el metodo crearReceta insertará los datos en la tabla de la base de datos
                recetasService.crearReceta(request);
                response.put("mensaje", "Receta creada con éxito para el producto ID " + request.getIdProducto() + ".");
                return ResponseEntity.status(HttpStatus.CREATED).body(response);
            } catch (IllegalArgumentException e) {
                response.put("error", e.getMessage());
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            } catch (Exception e) {
                response.put("error", "Error interno al crear la receta: " + e.getMessage());
                return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
            }
        }
    
        // URL: GET http://localhost:8080/inventario/recetas
        // Obtiene todas las recetas (o puedes modificar a /recetas/{idProducto} si solo quieres una)
        @GetMapping
        public ResponseEntity<List<RecetaProducto>> obtenerTodasLasRecetas() {
            // Asumiendo que RecetasService tiene un método para obtener todas
            List<RecetaProducto> todasLasRecetas = recetasService.obtenerTodasLasRecetas();
            return ResponseEntity.ok(todasLasRecetas);
        }
    
        // URL: PUT http://localhost:8080/inventario/recetas/{idProducto}
        // Reemplaza completamente una receta existente (normalmente borra la anterior e inserta la nueva)
        @PutMapping("/{idProducto}")
        public ResponseEntity<Map<String, Object>> actualizarReceta(@PathVariable Long idProducto, @RequestBody RecetaRequest request) {
            Map<String, Object> response = new HashMap<>();
            try {
                recetasService.actualizarReceta(idProducto, request);
                response.put("mensaje", "Receta para el producto ID " + idProducto + " actualizada con éxito.");
                return ResponseEntity.ok(response);
            } catch (IllegalArgumentException e) {
                response.put("error", e.getMessage());
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(response);
            } catch (Exception e) {
                response.put("error", "Error interno al actualizar la receta: " + e.getMessage());
                return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
            }
        }
    
        // URL: DELETE http://localhost:8080/inventario/recetas/{idProducto}
        // Elimina la receta de un producto
        @DeleteMapping("/{idProducto}")
        public ResponseEntity<Map<String, Object>> eliminarReceta(@PathVariable Long idProducto) {
            Map<String, Object> response = new HashMap<>();
            try {
                recetasService.eliminarReceta(idProducto);
                response.put("mensaje", "Receta para el producto ID " + idProducto + " eliminada con éxito.");
                return ResponseEntity.ok(response);
            } catch (IllegalArgumentException e) {
                response.put("error", e.getMessage());
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(response);
            } catch (Exception e) {
                response.put("error", "Error interno al eliminar la receta: " + e.getMessage());
                return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
            }
        }
    }