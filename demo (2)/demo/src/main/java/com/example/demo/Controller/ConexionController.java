package com.example.demo.Controller;

import com.example.demo.Model.Clientes;
import com.example.demo.Service.ConexionService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class ConexionController {

    @Autowired
    private ConexionService conexionService;

    @GetMapping("/Clientes")
    public List<Clientes> obtenerUsuarios() {
        return conexionService.obtenerUsuarios();
    }

    @PostMapping("/Clientes")
    public String crearUsuario(@RequestBody Clientes cliente) {//le dice a string que analice los datos enviados JSON y pasen a java
        conexionService.crearUsuario(cliente);
        System.out.println("Cliente recibido: " + cliente.getNOMBRE_CLI());//llamada al método en otro servicio
        return "Cliente " + cliente.getNOMBRE_CLI() + " creado con éxito.";
    }

    @PutMapping("/Clientes/{ID_CLIENTE}")
    public void actualizarUsuario(@PathVariable String ID_CLIENTE, @RequestBody Clientes cliente) {
        // Aquí llamas al método de tu servicio para actualizar el cliente
        conexionService.actualizarUsuario(ID_CLIENTE, cliente);
    }

    @DeleteMapping("Clientes/{ID_CLIENTE}")//asigna el método para manejar las solicitudes HTTP para la URL
    public ResponseEntity<Void> eliminarUsuario(@PathVariable("ID_CLIENTE") String id) {
        int filasAfectadas = conexionService.elimiarUsuario(id); //Llama al método que cree en conexion service
        if (filasAfectadas > 0) { //para comprobar si se han eliminado una o varias filas.
            return ResponseEntity.noContent().build();//si se pudo eliminar
        } else {
            return ResponseEntity.notFound().build();// si no se pudo
        }
    }
}
