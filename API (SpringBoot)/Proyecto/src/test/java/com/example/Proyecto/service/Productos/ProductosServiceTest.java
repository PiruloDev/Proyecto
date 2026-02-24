package com.example.Proyecto.service.Productos;

import com.example.Proyecto.model.PojoProductos;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.*;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.math.BigDecimal;
import java.time.LocalDate;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class ProductosServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private ProductosService productosService;

    private PojoProductos productoEjemplo;

    @BeforeEach
    void setUp() {
        productoEjemplo = new PojoProductos();
        productoEjemplo.setId(1);
        productoEjemplo.setNombreProducto("Pan Integral");
        productoEjemplo.setIdCategoriaProducto(2);
        productoEjemplo.setStockMinimo(10);
        productoEjemplo.setPrecio(new BigDecimal("5500.00"));
        productoEjemplo.setMarcaProducto("Bimbo");
        productoEjemplo.setDescripcionProducto("Pan integral de trigo");
        productoEjemplo.setFechaVencimiento(LocalDate.of(2025, 12, 31));
        productoEjemplo.setFechaIngreso(LocalDate.of(2024, 1, 1));
        productoEjemplo.setActivo(true);
        productoEjemplo.setIdAdmin(1);
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 1: obtenerProductosPorCategoria - retorna lista de productos
    // ─────────────────────────────────────────────────────────────
    @Test
    void testObtenerProductosPorCategoria_retornaListaConProductos() {
        // Arrange
        Map<String, Object> productoMock = new HashMap<>();
        productoMock.put("idProducto", 1);
        productoMock.put("nombreProducto", "Pan Integral");
        productoMock.put("precio", new BigDecimal("5500.00"));

        List<Map<String, Object>> listaEsperada = List.of(productoMock);

        when(jdbcTemplate.query(anyString(), any(Object[].class), any(RowMapper.class)))
                .thenReturn(listaEsperada);

        // Act
        List<Map<String, Object>> resultado =
                productosService.obtenerProductosPorCategoria(2);

        // Assert
        assertNotNull(resultado, "La lista no debe ser nula");
        assertEquals(1, resultado.size(), "Debe retornar exactamente un producto");
        assertEquals("Pan Integral", resultado.get(0).get("nombreProducto"),
                "El nombre del producto debe coincidir");
        assertEquals(new BigDecimal("5500.00"), resultado.get(0).get("precio"),
                "El precio debe coincidir");

        verify(jdbcTemplate, times(1))
                .query(anyString(), any(Object[].class), any(RowMapper.class));
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 2: crearProducto - inserción exitosa retorna true
    // ─────────────────────────────────────────────────────────────
    @Test
    void testCrearProducto_insercionExitosaRetornaTrue() {
        // Arrange
        when(jdbcTemplate.update(anyString(),
                any(), any(), any(), any(), any(),
                any(), any(), any(), any(), any()))
                .thenReturn(1); // 1 fila afectada = éxito

        // Act
        boolean resultado = productosService.crearProducto(productoEjemplo);

        // Assert
        assertTrue(resultado, "Debe retornar true cuando la inserción es exitosa");
        verify(jdbcTemplate, times(1)).update(anyString(),
                any(), any(), any(), any(), any(),
                any(), any(), any(), any(), any());
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 3: crearProducto - falla por excepción retorna false
    // ─────────────────────────────────────────────────────────────
    @Test
    void testCrearProducto_excepcionEnBDRetornaFalse() {
        // Arrange
        when(jdbcTemplate.update(anyString(),
                any(), any(), any(), any(), any(),
                any(), any(), any(), any(), any()))
                .thenThrow(new DataAccessException("Error al insertar producto") {});

        // Act
        boolean resultado = productosService.crearProducto(productoEjemplo);

        // Assert
        assertFalse(resultado,
                "Debe retornar false cuando ocurre un DataAccessException");
        verify(jdbcTemplate, times(1)).update(anyString(),
                any(), any(), any(), any(), any(),
                any(), any(), any(), any(), any());
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 4: eliminarProducto - eliminación exitosa retorna true
    // ─────────────────────────────────────────────────────────────
    @Test
    void testEliminarProducto_exitosoRetornaTrue() {
        // Arrange
        when(jdbcTemplate.update(anyString(), eq(1)))
                .thenReturn(1); // 1 fila eliminada

        // Act
        boolean resultado = productosService.eliminarProducto(1);

        // Assert
        assertTrue(resultado,
                "Debe retornar true cuando la eliminación es exitosa");
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1));
    }
}