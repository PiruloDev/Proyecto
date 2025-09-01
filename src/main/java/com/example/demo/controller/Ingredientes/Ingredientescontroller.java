package com.example.demo.controller.Ingredientes;

import com.example.demo.Ingredientes;
import com.example.demo.Service.Ingredientes.IngredientesService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;

@RestController
public class Ingredientescontroller {

    @Autowired
    private IngredientesService ingredientesService;

    // GET → obtener todos los ingredientes
    @GetMapping("/ingredientes")
    public List<String> obtenerIngredientes() {
        return ingredientesService.obtenerIngredientes();
    }

    @GetMapping("ingredientes/lista")
    public List<Ingredientes> obtenerIngredientesListas() {
        return ingredientesService.obtenerTodosLosIngredientes();
    }

    // Metodo post  crear un nuevo ingrediente
    @PostMapping("/crearingrediente")
    public String crearIngrediente(@RequestBody Ingredientes ingrediente) {
        ingredientesService.crearIngrediente(ingrediente);
        System.out.println("Ingrediente recibido: " + ingrediente.getNombreIngrediente());
        return "Ingrediente " + ingrediente.getNombreIngrediente() + " creado con éxito.";
    }

    // PUT → editar un ingrediente existente
    @PutMapping("ingrediente/{id}")
    public String editarIngrediente(@PathVariable int id, @RequestBody Ingredientes ingrediente) {
        ingrediente.setIdIngrediente(id); // asigna el ID de la URL al objeto

        int filas = ingredientesService.editarIngrediente(ingrediente);

        if (filas > 0) {
            return "Ingrediente con ID " + id + " actualizado correctamente.";
        } else {
            return "No se encontró el ingrediente con ID " + id;
        }
    }

    // Metodo PATCH
    @PatchMapping("/{id}/cantidad")
    public String patchCantidad(@PathVariable int id, @RequestBody Map<String, Object> updates) {
        if (updates.containsKey("cantidadIngrediente")) {
            int cantidad = (int) updates.get("cantidadIngrediente");
            int filas = ingredientesService.actualizarCantidad(id, cantidad);
            return filas > 0 ? "Cantidad actualizada" : "Ingrediente no encontrado";
        }
        return "No se envió la cantidad";
    }


    // DELETE → eliminar un ingrediente por ID
    @DeleteMapping("ingrediente/{id}")
    public String eliminarIngrediente(@PathVariable int id) {
        int filas = ingredientesService.eliminarIngrediente(id);
        if (filas > 0) {
            return "Ingrediente con ID " + id + " eliminado correctamente.";
        } else {
            return "No se encontró el ingrediente con ID " + id;
        }
    }






}
