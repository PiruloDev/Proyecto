package com.example.demoJava1.Clientes.Controller;

import com.example.demoJava1.Clientes.clientes;
import com.example.demoJava1.Clientes.Service.ConexionService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class ConexionController {
    @Autowired
    private ConexionService conexionService;

    @GetMapping("/{id}")
    public List<String> obtenerClientes() {
        return conexionService.obtenerClientes();
    }

    @GetMapping("/detalles")
    public List<clientes> obtenerDetalles() {
        return conexionService.obtenerDetalles();
    }

    @DeleteMapping("/{id}")
    public String eliminarCliente(@PathVariable int id) {
        int result = conexionService.eliminarCliente(id);
        return result > 0 ? "Cliente eliminado." : "Error al eliminar al cliente.";
    }
}
