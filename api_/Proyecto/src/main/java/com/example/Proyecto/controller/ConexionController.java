package com.example.Proyecto.controller;

import com.example.Proyecto.model.PojoCliente;
import com.example.Proyecto.service.Clientes.ConexionClienteService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RestController
public class ConexionController {

    @Autowired
    private ConexionClienteService conexionClienteService;

    @GetMapping("/Clientes")
    public List<Map<String, Object>> obtenerUsuarios() {
        return conexionClienteService.obtenerDetallesCliente();
    }

    @PostMapping("/Clientes")
    public String crearUsuario(@RequestBody PojoCliente cliente) {
        boolean creado = conexionClienteService.crearCliente(cliente);
        System.out.println("Cliente recibido: " + cliente.getNombre());
        return creado ? "Cliente " + cliente.getNombre() + " creado con éxito." : "Error al crear cliente";
    }
    @PutMapping("/Clientes/{ID_CLIENTE}")
    public void actualizarUsuario(@PathVariable int ID_CLIENTE, @RequestBody PojoCliente cliente) {
        cliente.setId(ID_CLIENTE);
        conexionClienteService.actualizarCliente(cliente);
    }
    @DeleteMapping("Clientes/{ID_CLIENTE}")
    public ResponseEntity<Void> eliminarUsuario(@PathVariable("ID_CLIENTE") int id) {
        return ResponseEntity.status(501).build();
    }
}
