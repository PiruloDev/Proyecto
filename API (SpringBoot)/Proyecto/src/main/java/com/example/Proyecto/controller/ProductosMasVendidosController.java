package com.example.Proyecto.controller;

import com.example.Proyecto.model.ProductosMasVendidos;
import com.example.Proyecto.service.ProductosMasVendidos.ProductosMasVendidosService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
@Tag(name = "Reportes - Productos Más Vendidos", description = "Consulta de productos más vendidos")
public class ProductosMasVendidosController {

        @Autowired
        private ProductosMasVendidosService productosMasVendidosService;

        @Operation(summary = "Obtener productos más vendidos", description = "Retorna un ranking de los productos más vendidos con un límite configurable")
        @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
        @GetMapping("/productos/mas-vendidos")
        public ResponseEntity<List<ProductosMasVendidos>> getProductosMasVendidos(
                @Parameter(description = "Límite de resultados", example = "10")
                @RequestParam(defaultValue = "10") int limite) {

                List<ProductosMasVendidos> productos = productosMasVendidosService.obtenerProductosMasVendidos(limite);
                return ResponseEntity.ok(productos);
        }
}

