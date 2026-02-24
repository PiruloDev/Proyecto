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
import org.mockito.junit.jupiter.MockitoSettings;
import org.mockito.quality.Strictness;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.math.BigDecimal;
import java.util.List;

import static org.assertj.core.api.Assertions.assertThat;
import static org.assertj.core.api.Assertions.assertThatThrownBy;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@MockitoSettings(strictness = Strictness.LENIENT) // compatible con Mockito 5.x
class RecetasServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private RecetasService recetasService;

    private RecetaDetalleDTO detalleDTO;

    @BeforeEach
    void setUp() {
        detalleDTO = new RecetaDetalleDTO();
        detalleDTO.setIdReceta(10L);
        detalleDTO.setIdProducto(1L);
        detalleDTO.setNombreProducto("Postre de Tres Leches");
        detalleDTO.setIdIngrediente(1L);
        detalleDTO.setNombreIngrediente("Harina de Trigo");
        detalleDTO.setCantidadRequerida(new BigDecimal("2.0000"));
        detalleDTO.setIdUnidad(2L);
        detalleDTO.setNombreUnidad("Gramo");
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 1: obtenerRecetaOptimizadaPorProducto — producto existente
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe retornar receta optimizada con todos los campos para un producto existente")
    void obtenerRecetaOptimizadaPorProducto_cuandoExiste_retornaDatos() {
        // Arrange
        doReturn(List.of(detalleDTO))
                .when(jdbcTemplate)
                .query(anyString(), any(RowMapper.class), eq(1L));

        // Act
        List<RecetaDetalleDTO> resultado = recetasService.obtenerRecetaOptimizadaPorProducto(1L);

        // Assert
        assertThat(resultado).hasSize(1);
        assertThat(resultado.get(0).getNombreProducto()).isEqualTo("Postre de Tres Leches");
        assertThat(resultado.get(0).getNombreIngrediente()).isEqualTo("Harina de Trigo");
        assertThat(resultado.get(0).getIdUnidad()).isEqualTo(2L);
        assertThat(resultado.get(0).getNombreUnidad()).isEqualTo("Gramo");
        assertThat(resultado.get(0).getCantidadRequerida())
                .isEqualByComparingTo(new BigDecimal("2.0000"));
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 2: obtenerRecetaOptimizadaPorProducto — producto sin receta
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe retornar lista vacía cuando el producto no tiene receta registrada")
    void obtenerRecetaOptimizadaPorProducto_cuandoNoExiste_retornaVacio() {
        // Arrange
        doReturn(List.of())
                .when(jdbcTemplate)
                .query(anyString(), any(RowMapper.class), eq(99L));

        // Act
        List<RecetaDetalleDTO> resultado = recetasService.obtenerRecetaOptimizadaPorProducto(99L);

        // Assert
        assertThat(resultado).isEmpty();
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 3: actualizarReceta — producto inexistente lanza excepción
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe lanzar IllegalArgumentException al actualizar receta de producto inexistente")
    void actualizarReceta_cuandoProductoNoExiste_lanzaExcepcion() {
        // Arrange
        doThrow(new EmptyResultDataAccessException(1))
                .when(jdbcTemplate)
                .queryForObject(anyString(), eq(Long.class), eq(99L));

        RecetaRequest request = new RecetaRequest();
        request.setIdProducto(99L);
        request.setIngredientes(List.of());

        // Act & Assert
        assertThatThrownBy(() -> recetasService.actualizarReceta(99L, request))
                .isInstanceOf(IllegalArgumentException.class)
                .hasMessageContaining("99");
    }

    // ─────────────────────────────────────────────────────────────
    // PRUEBA 4: eliminarReceta — ejecuta DELETE en detalles y encabezado
    // ─────────────────────────────────────────────────────────────
    @Test
    @DisplayName("Debe eliminar detalles y encabezado de receta correctamente")
    void eliminarReceta_cuandoExiste_eliminaCorrectamente() {
        // Arrange — encuentra ID_RECETA = 10 para ID_PRODUCTO = 1
        doReturn(10L)
                .when(jdbcTemplate)
                .queryForObject(anyString(), eq(Long.class), eq(1L));

        // Simula DELETE exitoso (1 fila afectada)
        doReturn(1)
                .when(jdbcTemplate)
                .update(anyString(), eq(10L));

        // Act
        recetasService.eliminarReceta(1L);

        // Assert — delete de detalles + delete de encabezado = 2 llamadas
        verify(jdbcTemplate, times(2)).update(anyString(), eq(10L));
    }
}