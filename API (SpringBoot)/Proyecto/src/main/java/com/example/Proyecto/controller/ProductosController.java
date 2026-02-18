package com.example.Proyecto.controller;

import com.example.Proyecto.model.PojoProductos;
import com.example.Proyecto.service.Productos.ProductosService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

@RequestMapping("/productos")
@RestController
@CrossOrigin(origins = "*")
@Tag(name = "Productos", description = "Gestión de productos de panadería")
public class ProductosController {

    @Autowired
    private ProductosService productosService;

    @Operation(summary = "Obtener todos los productos", description = "Retorna una lista con todos los productos y sus detalles")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Lista de productos obtenida exitosamente")
    })
    @GetMapping
    public List<Map<String, Object>> obtenerDetallesProductos() {
        return productosService.obtenerDetallesProducto();
    }

    @Operation(summary = "Obtener productos por categoría", description = "Retorna todos los productos que pertenecen a una categoría específica")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Productos encontrados"),
        @ApiResponse(responseCode = "404", description = "No se encontraron productos en esta categoría"),
        @ApiResponse(responseCode = "500", description = "Error interno del servidor")
    })
    @GetMapping("/categoria/{idCategoria}")
    public ResponseEntity<List<Map<String, Object>>> obtenerProductosPorCategoria(
        @Parameter(description = "ID de la categoría", required = true)
        @PathVariable int idCategoria) {
        try {
            List<Map<String, Object>> productos = productosService.obtenerProductosPorCategoria(idCategoria);
            if (productos != null && !productos.isEmpty()) {
                return ResponseEntity.ok(productos);
            } else {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(productos);
            }
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(null);
        }
    }

    @Operation(summary = "Obtener producto por ID", description = "Retorna los detalles de un producto específico")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Producto encontrado"),
        @ApiResponse(responseCode = "404", description = "Producto no encontrado"),
        @ApiResponse(responseCode = "500", description = "Error interno del servidor")
    })
    @GetMapping("/{id}")
    public ResponseEntity<Map<String, Object>> obtenerProductoPorId(
        @Parameter(description = "ID del producto", required = true)
        @PathVariable int id) {
        try {
            Map<String, Object> producto = productosService.obtenerProductoPorId(id);
            if (producto != null && !producto.isEmpty()) {
                return ResponseEntity.ok(producto);
            } else {
                Map<String, Object> error = new HashMap<>();
                error.put("error", "No se encontró producto con ID " + id);
                error.put("status", 404);
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(error);
            }
        } catch (Exception e) {
            e.printStackTrace();
            Map<String, Object> error = new HashMap<>();
            error.put("error", "Error al obtener el producto: " + e.getMessage());
            error.put("status", 500);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(error);
        }
    }

    @Operation(summary = "Crear nuevo producto", description = "Registra un nuevo producto en el sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Producto creado exitosamente"),
        @ApiResponse(responseCode = "400", description = "Datos inválidos"),
        @ApiResponse(responseCode = "500", description = "Error al crear el producto")
    })
    @PostMapping
    public ResponseEntity<Map<String, Object>> crearProducto(
        @Parameter(description = "Datos del producto a crear", required = true)
        @RequestBody PojoProductos pojoProductos) {
        Map<String, Object> response = new HashMap<>();

        try {
            System.out.println("=== DEBUG POST PRODUCTOS ===");
            System.out.println("Datos recibidos:");
            System.out.println("Nombre: " + pojoProductos.getNombreProducto());
            System.out.println("Stock Mínimo: " + pojoProductos.getStockMinimo());
            System.out.println("Precio: " + pojoProductos.getPrecio());
            System.out.println("Fecha Vencimiento RAW: " + pojoProductos.getFechaVencimiento());
            System.out.println("Fecha Ingreso RAW: " + pojoProductos.getFechaIngreso());
            System.out.println("Marca: " + pojoProductos.getMarcaProducto());
            System.out.println("ID Admin: " + pojoProductos.getIdAdmin());
            System.out.println("ID Categoria: " + pojoProductos.getIdCategoriaProducto());

            if (pojoProductos.getFechaVencimiento() != null) {
                System.out.println("Tipo de fecha vencimiento: " + pojoProductos.getFechaVencimiento().getClass().getSimpleName());
                System.out.println("Fecha vencimiento toString: " + pojoProductos.getFechaVencimiento().toString());
            } else {
                System.out.println("ALERTA: fechaVencimiento es NULL");
            }

            if (pojoProductos.getFechaIngreso() != null) {
                System.out.println("Tipo de fecha ingreso: " + pojoProductos.getFechaIngreso().getClass().getSimpleName());
                System.out.println("Fecha ingreso toString: " + pojoProductos.getFechaIngreso().toString());
            } else {
                System.out.println("ALERTA: fechaIngreso es NULL");
            }

            if (pojoProductos.getNombreProducto() == null || pojoProductos.getNombreProducto().trim().isEmpty()) {
                response.put("error", "El nombre del producto es obligatorio");
                response.put("status", 400);
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            }

            boolean creado = productosService.crearProducto(pojoProductos);
            System.out.println("Resultado del servicio: " + creado);

            if (creado) {
                response.put("mensaje", "Nuevo Producto creado e ingresado exitosamente");
                response.put("status", 201);
                response.put("producto", pojoProductos.getNombreProducto());
                return ResponseEntity.status(HttpStatus.CREATED).body(response);
            } else {
                response.put("error", "Error al crear un nuevo Producto en la base de datos");
                response.put("status", 500);
                return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            System.out.println("Excepción en crearProducto: " + e.getMessage());
            response.put("error", "Excepción al crear producto: " + e.getMessage());
            response.put("status", 500);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @Operation(summary = "Actualizar producto", description = "Actualiza los datos de un producto existente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Producto actualizado exitosamente"),
        @ApiResponse(responseCode = "400", description = "Error al actualizar"),
        @ApiResponse(responseCode = "500", description = "Error interno del servidor")
    })
    @PatchMapping("/{id}")
    public ResponseEntity<String> actualizarProducto(
        @Parameter(description = "ID del producto", required = true)
        @PathVariable("id") Long id, 
        @Parameter(description = "Datos a actualizar", required = true)
        @RequestBody PojoProductos pojoProductos) {
        try {
            // ← LOGS DE DEBUG
            System.out.println("=== DATOS RECIBIDOS EN PATCH ===");
            System.out.println("ID: " + id);
            System.out.println("Nombre: " + pojoProductos.getNombreProducto());
            System.out.println("Precio: " + pojoProductos.getPrecio());
            System.out.println("Stock: " + pojoProductos.getStockMinimo());
            System.out.println("Categoría: " + pojoProductos.getIdCategoriaProducto());
            System.out.println("Marca: " + pojoProductos.getMarcaProducto());
            System.out.println("Activo: " + pojoProductos.isActivo());
            System.out.println("Objeto completo: " + pojoProductos.toString());

            pojoProductos.setId(id.intValue());
            boolean actualizado = productosService.actualizarProducto(pojoProductos);

            if (actualizado) {
                return ResponseEntity.ok("El producto " + pojoProductos.getNombreProducto() + " ha sido actualizado correctamente");
            } else {
                return ResponseEntity.status(HttpStatus.BAD_REQUEST)
                        .body("Error al actualizar el producto");
            }
        } catch (Exception e) {
            e.printStackTrace();
            System.out.println("ERROR EN PATCH: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Excepción: " + e.getMessage());
        }
    }

    @Operation(summary = "Eliminar producto", description = "Elimina un producto del sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Producto eliminado exitosamente"),
        @ApiResponse(responseCode = "404", description = "Producto no encontrado"),
        @ApiResponse(responseCode = "500", description = "Error al eliminar")
    })
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarProducto(
        @Parameter(description = "ID del producto", required = true)
        @PathVariable("id") int id) {
        try {
            boolean eliminado = productosService.eliminarProducto(id);
            if (eliminado) {
                return ResponseEntity.ok("Producto con ID " + id + " eliminado correctamente");
            } else {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body("No se encontro producto con ID " + id);
            }
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body("Error al eliminar el producto: " + e.getMessage());
        }
    }

    @PostMapping("/test-fechas")
    public ResponseEntity<Map<String, Object>> testFechas(@RequestBody PojoProductos pojoProductos) {
        Map<String, Object> response = new HashMap<>();

        try {
            System.out.println("=== TEST FECHAS ===");
            System.out.println("Fecha Vencimiento recibida: " + pojoProductos.getFechaVencimiento());
            System.out.println("Fecha Ingreso recibida: " + pojoProductos.getFechaIngreso());

            response.put("mensaje", "Test de fechas ejecutado");
            response.put("fechaVencimiento", pojoProductos.getFechaVencimiento());
            response.put("fechaIngreso", pojoProductos.getFechaIngreso());
            response.put("fechaVencimientoEsNull", pojoProductos.getFechaVencimiento() == null);
            response.put("fechaIngresoEsNull", pojoProductos.getFechaIngreso() == null);
            response.put("nombreProducto", pojoProductos.getNombreProducto());
            response.put("precio", pojoProductos.getPrecio());

            return ResponseEntity.ok(response);
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Error en test de fechas: " + e.getMessage());
            return ResponseEntity.status(500).body(response);
        }
    }
}