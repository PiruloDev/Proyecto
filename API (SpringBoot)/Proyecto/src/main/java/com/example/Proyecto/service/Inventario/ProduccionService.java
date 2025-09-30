package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.dto.ProduccionRequest;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import java.math.BigDecimal; // Necesario para la validación

@Service
public class ProduccionService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    @Transactional
    public void registrarProduccion(ProduccionRequest request) {
        // Validación de stock de ingredientes (Paso Añadido)
        validarStockIngredientes(request);

        // 1. Aumentar el stock del producto terminado (tabla 'productos')
        String sqlUpdateProducto = "UPDATE productos SET stock = stock + ? WHERE ID_PRODUCTO = ?";
        int updatedRowsProducto = jdbcTemplate.update(
                sqlUpdateProducto,
                request.getCantidadProducida(),
                request.getIdProducto()
        );

        if (updatedRowsProducto == 0) {
            throw new RuntimeException("Error: No se encontró el producto con ID " + request.getIdProducto() + " para aumentar el stock.");
        }

        // 2. Descontar el stock de cada ingrediente (tabla 'ingredientes')
        String sqlUpdateIngrediente = "UPDATE ingredientes SET cantidadIngrediente = cantidadIngrediente - ? WHERE idIngrediente = ?";

        for (ProduccionRequest.IngredienteDescontado detalle : request.getIngredientesDescontados()) {

            // La validación anterior asegura que hay suficiente stock, procedemos al descuento.
            int updatedRowsIngrediente = jdbcTemplate.update(
                    sqlUpdateIngrediente,
                    detalle.getCantidadUsada(),
                    detalle.getIdIngrediente()
            );

            // Esto es una verificación de seguridad adicional para el rollback
            if (updatedRowsIngrediente == 0) {
                throw new RuntimeException("Error al actualizar el stock del ingrediente ID " + detalle.getIdIngrediente() + ". Posible ID incorrecto.");
            }
        }
    }

    // Método para validar el stock disponible antes de la producción
    private void validarStockIngredientes(ProduccionRequest request) {
        String sqlCheckStock = "SELECT cantidadIngrediente FROM ingredientes WHERE idIngrediente = ?";

        for (ProduccionRequest.IngredienteDescontado detalle : request.getIngredientesDescontados()) {
            BigDecimal cantidadRequerida = detalle.getCantidadUsada();
            Long idIngrediente = detalle.getIdIngrediente();

            try {
                // Se asume que la columna en la DB es NUMERIC/DECIMAL
                BigDecimal stockActual = jdbcTemplate.queryForObject(
                        sqlCheckStock,
                        new Object[]{idIngrediente},
                        BigDecimal.class
                );

                if (stockActual == null || stockActual.compareTo(cantidadRequerida) < 0) {
                    throw new RuntimeException(
                            "Stock insuficiente para el ingrediente ID " + idIngrediente +
                                    ". Stock Actual: " + stockActual + ", Requerido: " + cantidadRequerida
                    );
                }
            } catch (Exception e) {
                // Manejo de error si el ingrediente no existe en la base de datos
                throw new RuntimeException("Error al verificar stock del ingrediente ID " + idIngrediente + ". Causa: " + e.getMessage());
            }
        }
    }
}