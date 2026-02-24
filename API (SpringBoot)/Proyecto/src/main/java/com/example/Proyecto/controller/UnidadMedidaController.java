package com.example.Proyecto.controller;

import com.example.Proyecto.model.UnidadMedida;
import com.example.Proyecto.service.UnidadMedida.UnidadMedidaService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
@RequestMapping("/unidades-medida")
@Tag(name = "Unidades de Medida", description = "Catálogo de unidades de medida")
public class UnidadMedidaController {

    @Autowired
    private UnidadMedidaService unidadMedidaService;

    @Operation(summary = "Obtener todas las unidades de medida")
    @GetMapping
    public ResponseEntity<List<UnidadMedida>> obtenerTodas() {
        return ResponseEntity.ok(unidadMedidaService.obtenerTodas());
    }
}