package com.example.demo.controller.Ingredientes;

import com.example.demo.Ingredientes;
import com.example.demo.Service.Ingredientes.IngredientesService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
public class Ingredientescontroller {

    @Autowired
    private IngredientesService ingredientesService;

    // GET → obtener todos los ingredientes
    @GetMapping("ingrediente")
    public List<Ingredientes> obtenerIngredientes() {
        return ingredientesService.obtenerTodosLosIngredientes();
    }

    // POST → crear un nuevo ingrediente
    @PostMapping("ingrediente")
    public String crearIngrediente(@RequestBody Ingredientes ingrediente) {
        ingredientesService.crearIngrediente(ingrediente);
        System.out.println("Ingrediente recibido: " + ingrediente.getNombreIngrediente());
        return "Ingrediente " + ingrediente.getNombreIngrediente() + " creado con éxito.";
    }
}
