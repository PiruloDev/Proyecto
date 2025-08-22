package com.example.demo.controller.Clientes;

import com.example.demo.Clientes;
import com.example.demo.Service.Clientes.ConexionService;
import com.example.demo.Service.Productos.ProductosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class ConexionController {

    @Autowired
    private ConexionService conexionService;

    @GetMapping("/Clientes")
    public List<String> obtenerUsuarios() {
        return conexionService.obtenerUsuarios();
    }

    @PostMapping("/Clientes")
    public String crearUsuario(@RequestBody Clientes cliente) {
        conexionService.crearUsuario(cliente);
        System.out.println("cliente recibido: " + cliente.getNOMBRE_CLI());
        return "Cliente " + cliente.getNOMBRE_CLI() + " creado con exito.";
    }

    @PutMapping("/Clientes/{ID_CLIENTE}")
    public ResponseEntity<?> actualizarUsuario(@PathVariable String ID_CLIENTE, @RequestBody Clientes clienteActualizado) {
        Clientes clienteActualizadoDB = conexionService.actualizarUsuario(ID_CLIENTE, clienteActualizado);

        if (clienteActualizadoDB == null) {
            return new ResponseEntity<String>("Cliente con ID " + ID_CLIENTE + " no encontrado.", HttpStatus.NOT_FOUND);
        }

        // Si la actualización fue exitosa
        return new ResponseEntity<Clientes>(clienteActualizadoDB, HttpStatus.OK);
    }



}