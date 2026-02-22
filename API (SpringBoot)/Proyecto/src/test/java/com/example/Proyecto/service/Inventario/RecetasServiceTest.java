package com.example.Proyecto.service.Inventario;

import com.example.Proyecto.dto.RecetaDetalleDTO;
import com.example.Proyecto.dto.RecetaRequest;
import com.example.Proyecto.model.RecetaProducto;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.math.BigDecimal;
import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class RecetasServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private RecetasService recetasService;

    private RecetaDetalleDTO detalleDTO;
    private RecetaProducto recetaProducto;

    @BeforeEach
    void setUp() {
        // DTO optimizado de prueba
        detalleDTO = new RecetaDetalleDTO();
        detalleDTO.setIdReceta(10L);
        detalleDTO.setIdProducto(1L);
        detalleDTO.setNombreProducto("Postre de Tres Leches");
        detalleDTO.setIdIngrediente(1L);
        detalleDTO.setNombreIngrediente("Harina de Trigo");
        detalleDTO.setCantidadRequerida(new BigDecimal("2.0000"));
        detalleDTO.setIdUnidad(2L);
        detalleDTO.setNombreUnidad("Gramo");

        // Modelo plano de prueba
        recetaProducto = new RecetaProducto();
        recetaProducto.setIdReceta(10L);
        recetaProducto.setIdProducto(1L);
        recetaProducto.setIdIngrediente(1L);
        recetaProducto.setCantidadRequerida(new BigDecimal("2.0000"));
        recetaProducto.setIdUnidad(2L);
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 1: obtenerRecetaOptimizadaPorProducto devuelve datos correctos
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe retornar receta optimizada con todos los campos para un producto existente")
    void obtenerRecetaOptimizadaPorProducto_cuandoExiste_retornaDatos() {
        // Arrange
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), eq(1L)))
                .thenReturn(List.of(detalleDTO));

        // Act
        List<RecetaDetalleDTO> resultado = recetasService.obtenerRecetaOptimizadaPorProducto(1L);

        // Assert
        assertThat(resultado).hasSize(1);
        assertThat(resultado.get(0).getNombreProducto()).isEqualTo("Postre de Tres Leches");
        assertThat(resultado.get(0).getNombreIngrediente()).isEqualTo("Harina de Trigo");
        assertThat(resultado.get(0).getIdUnidad()).isEqualTo(2L);
        assertThat(resultado.get(0).getNombreUnidad()).isEqualTo("Gramo");
        assertThat(resultado.get(0).getCantidadRequerida()).isEqualByComparingTo("2.0000");
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 2: obtenerRecetaOptimizadaPorProducto retorna lista vacía
    //           cuando el producto no tiene receta
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe retornar lista vacía cuando el producto no tiene receta registrada")
    void obtenerRecetaOptimizadaPorProducto_cuandoNoExiste_retornaVacio() {
        // Arrange
        when(jdbcTemplate.query(anyString(), any(RowMapper.class), eq(99L)))
                .thenReturn(List.of());

        // Act
        List<RecetaDetalleDTO> resultado = recetasService.obtenerRecetaOptimizadaPorProducto(99L);

        // Assert
        assertThat(resultado).isEmpty();
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 3: actualizarReceta lanza excepción si el producto no existe
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe lanzar IllegalArgumentException al actualizar receta de producto inexistente")
    void actualizarReceta_cuandoProductoNoExiste_lanzaExcepcion() {
        // Arrange
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), eq(99L)))
                .thenThrow(new org.springframework.dao.EmptyResultDataAccessException(1));

        RecetaRequest request = new RecetaRequest();
        request.setIdProducto(99L);
        request.setIngredientes(List.of());

        // Act & Assert
        assertThatThrownBy(() -> recetasService.actualizarReceta(99L, request))
                .isInstanceOf(IllegalArgumentException.class)
                .hasMessageContaining("99");
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 4: eliminarReceta ejecuta DELETE en encabezado y detalles
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe eliminar detalles y encabezado de receta correctamente")
    void eliminarReceta_cuandoExiste_eliminaCorrectamente() {
        // Arrange — simula que encuentra ID_RECETA = 10 para ID_PRODUCTO = 1
        when(jdbcTemplate.queryForObject(anyString(), eq(Long.class), eq(1L)))
                .thenReturn(10L);

        // Simula que el DELETE del encabezado afecta 1 fila
        when(jdbcTemplate.update(anyString(), eq(10L)))
                .thenReturn(1);

        // Act
        recetasService.eliminarReceta(1L);

        // Assert — verifica que se llamó update al menos 2 veces (detalles + encabezado)
        verify(jdbcTemplate, atLeast(2)).update(anyString(), eq(10L));
    }
}