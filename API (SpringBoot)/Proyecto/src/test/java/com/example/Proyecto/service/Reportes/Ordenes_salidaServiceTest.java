package com.example.Proyecto.service.Reportes;

import com.example.Proyecto.model.Ordenes_salida;
import com.example.Proyecto.service.Ordenes_salida.Ordenes_salidaService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.*;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.time.LocalDateTime;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class Ordenes_salidaServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private Ordenes_salidaService ordenesSalidaService;

    private Ordenes_salida ordenEjemplo;

    @BeforeEach
    void setUp() {
        ordenEjemplo = new Ordenes_salida();
        ordenEjemplo.setIdFactura(1);
        ordenEjemplo.setIdCliente(10);
        ordenEjemplo.setIdPedido(5);
        ordenEjemplo.setFechaFacturacion(LocalDateTime.of(2024, 6, 15, 10, 0));
        ordenEjemplo.setTotalFactura(250.00);
        ordenEjemplo.setNombreCliente("Juan Pérez");
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 1: obtenerOrdenesSalida - retorna lista con registros
    // ─────────────────────────────────────────────────────────────
    @Test
    void testObtenerOrdenesSalida_retornaListaConOrdenes() {
        // Arrange
        List<Ordenes_salida> listaEsperada = List.of(ordenEjemplo);

        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(listaEsperada);

        // Act
        List<Ordenes_salida> resultado = ordenesSalidaService.obtenerOrdenesSalida();

        // Assert
        assertNotNull(resultado, "La lista no debe ser nula");
        assertEquals(1, resultado.size(), "Debe retornar exactamente un registro");
        assertEquals("Juan Pérez", resultado.get(0).getNombreCliente(),
                "El nombre del cliente debe coincidir");
        assertEquals(250.00, resultado.get(0).getTotalFactura(),
                "El total de la factura debe coincidir");

        // ── Imprimir resultados en consola ──
        System.out.println("=== TEST 1: obtenerOrdenesSalida ===");
        System.out.println("Cantidad de órdenes obtenidas: " + resultado.size());
        System.out.println("ID Factura     : " + resultado.get(0).getIdFactura());
        System.out.println("ID Cliente     : " + resultado.get(0).getIdCliente());
        System.out.println("ID Pedido      : " + resultado.get(0).getIdPedido());
        System.out.println("Nombre Cliente : " + resultado.get(0).getNombreCliente());
        System.out.println("Total Factura  : " + resultado.get(0).getTotalFactura());
        System.out.println("Fecha Factura  : " + resultado.get(0).getFechaFacturacion());
        System.out.println("Resultado      : ✔ PRUEBA PASADA");
        System.out.println("====================================");

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 2: agregarVenta - inserción exitosa retorna true
    // ─────────────────────────────────────────────────────────────
    @Test
    void testAgregarVenta_insercionExitosaRetornaTrue() {
        // Arrange
        when(jdbcTemplate.update(anyString(), any(), any(), any(), any()))
                .thenReturn(1);

        // Act
        boolean resultado = ordenesSalidaService.agregarVenta(ordenEjemplo);

        // Assert
        assertTrue(resultado, "Debe retornar true cuando la inserción es exitosa");

        // ── Imprimir resultados en consola ──
        System.out.println("=== TEST 2: agregarVenta (Exitoso) ===");
        System.out.println("Datos de la orden insertada:");
        System.out.println("ID Cliente     : " + ordenEjemplo.getIdCliente());
        System.out.println("ID Pedido      : " + ordenEjemplo.getIdPedido());
        System.out.println("Total Factura  : " + ordenEjemplo.getTotalFactura());
        System.out.println("Fecha Factura  : " + ordenEjemplo.getFechaFacturacion());
        System.out.println("Resultado      : " + resultado + " → ✔ PRUEBA PASADA");
        System.out.println("======================================");

        verify(jdbcTemplate, times(1))
                .update(anyString(), any(), any(), any(), any());
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 3: agregarVenta - falla por excepción retorna false
    // ─────────────────────────────────────────────────────────────
    @Test
    void testAgregarVenta_excepcionEnBDRetornaFalse() {
        // Arrange
        when(jdbcTemplate.update(anyString(), any(), any(), any(), any()))
                .thenThrow(new DataAccessException("Error de conexión") {});

        // Act
        boolean resultado = ordenesSalidaService.agregarVenta(ordenEjemplo);

        // Assert
        assertFalse(resultado, "Debe retornar false cuando ocurre un DataAccessException");

        // ── Imprimir resultados en consola ──
        System.out.println("=== TEST 3: agregarVenta (Excepción) ===");
        System.out.println("Escenario      : Error de conexión simulado en BD");
        System.out.println("ID Cliente     : " + ordenEjemplo.getIdCliente());
        System.out.println("ID Pedido      : " + ordenEjemplo.getIdPedido());
        System.out.println("Resultado      : " + resultado + " → ✔ PRUEBA PASADA (el catch funcionó correctamente)");
        System.out.println("========================================");

        verify(jdbcTemplate, times(1))
                .update(anyString(), any(), any(), any(), any());
    }

    // ─────────────────────────────────────────────────────────────
    // TEST 4: eliminarVenta - retorna número de filas eliminadas
    // ─────────────────────────────────────────────────────────────
    @Test
    void testEliminarVenta_retornaFilasAfectadas() {
        // Arrange
        int idFactura = 1;
        when(jdbcTemplate.update(anyString(), eq(idFactura)))
                .thenReturn(1);

        // Act
        int resultado = ordenesSalidaService.eliminarVenta(idFactura);

        // Assert
        assertEquals(1, resultado,
                "Debe retornar 1 cuando la eliminación afecta exactamente una fila");

        // ── Imprimir resultados en consola ──
        System.out.println("=== TEST 4: eliminarVenta ===");
        System.out.println("ID Factura eliminada : " + idFactura);
        System.out.println("Filas afectadas      : " + resultado);
        System.out.println("Resultado            : ✔ PRUEBA PASADA");
        System.out.println("============================");

        verify(jdbcTemplate, times(1)).update(anyString(), eq(idFactura));
    }
}