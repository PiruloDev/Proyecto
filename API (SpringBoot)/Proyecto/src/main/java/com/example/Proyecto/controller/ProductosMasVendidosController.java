package com.example.demoJava1.ProductosMasVendidos;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
public class ProductosMasVendidosController {

        @Autowired
        private ProductosMasVendidosService productosMasVendidosService;

        @GetMapping("/productos/mas-vendidos")
        public ResponseEntity<List<ProductosMasVendidos>> getProductosMasVendidos(
                @RequestParam(defaultValue = "10") int limite) {

                List<ProductosMasVendidos> productos = productosMasVendidosService.obtenerProductosMasVendidos(limite);
                return ResponseEntity.ok(productos);
        }
}

