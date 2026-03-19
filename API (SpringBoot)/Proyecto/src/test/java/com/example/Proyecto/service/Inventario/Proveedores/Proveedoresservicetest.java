package com.example.Proyecto.service.Inventario.Proveedores;

import com.example.Proyecto.service.Proveedores.Proveedores;
import com.example.Proyecto.service.Proveedores.ProveedoresService;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;

import java.util.Collections;
import java.util.List;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
@DisplayName("ProveedoresService - Pruebas Unitarias")
class ProveedoresServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @InjectMocks
    private ProveedoresService proveedoresService;

    private Proveedores proveedorActivo;
    private Proveedores proveedorInactivo;

    @BeforeEach
    void setUp() {
        proveedorActivo = new Proveedores();
        proveedorActivo.setIdProveedor(1);
        proveedorActivo.setNombreProv("Harinera del Valle");
        proveedorActivo.setTelefonoProv("3001234567");
        proveedorActivo.setActivoProv(true);
        proveedorActivo.setEmailProv("contacto@harinera.com");
        proveedorActivo.setDireccionProv("Calle 10 #5-23, Cali");

        proveedorInactivo = new Proveedores();
        proveedorInactivo.setIdProveedor(2);
        proveedorInactivo.setNombreProv("Lácteos El Campo");
        proveedorInactivo.setTelefonoProv("3119876543");
        proveedorInactivo.setActivoProv(false);
        proveedorInactivo.setEmailProv("ventas@lacteos.com");
        proveedorInactivo.setDireccionProv("Carrera 8 #12-45, Bogotá");
    }

    // ─────────────────────────────────────────────
    //  obtenerTodosLosProveedores()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("obtenerTodos - retorna lista con múltiples proveedores")
    void obtenerTodos_retornaListaConProveedores() {
        List<Proveedores> listaEsperada = List.of(proveedorActivo, proveedorInactivo);

        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(listaEsperada);

        List<Proveedores> resultado = proveedoresService.obtenerTodosLosProveedores();

        assertNotNull(resultado);
        assertEquals(2, resultado.size());

        assertEquals(1, resultado.get(0).getIdProveedor());
        assertEquals("Harinera del Valle", resultado.get(0).getNombreProv());
        assertEquals("3001234567", resultado.get(0).getTelefonoProv());
        assertTrue(resultado.get(0).getActivoProv());
        assertEquals("contacto@harinera.com", resultado.get(0).getEmailProv());
        assertEquals("Calle 10 #5-23, Cali", resultado.get(0).getDireccionProv());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodos - retorna lista vacía cuando no hay proveedores")
    void obtenerTodos_retornaListaVacia_cuandoNoHayProveedores() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(Collections.emptyList());

        List<Proveedores> resultado = proveedoresService.obtenerTodosLosProveedores();

        assertNotNull(resultado);
        assertTrue(resultado.isEmpty());

        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    @Test
    @DisplayName("obtenerTodos - todos los campos de cada proveedor no son nulos")
    void obtenerTodos_camposDeCadaProveedorNoSonNulos() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenReturn(List.of(proveedorActivo, proveedorInactivo));

        List<Proveedores> resultado = proveedoresService.obtenerTodosLosProveedores();

        resultado.forEach(p -> {
            assertNotNull(p.getNombreProv(),     "El nombre no debe ser nulo");
            assertNotNull(p.getTelefonoProv(),   "El teléfono no debe ser nulo");
            assertNotNull(p.getActivoProv(),     "El estado activo no debe ser nulo");
            assertNotNull(p.getEmailProv(),      "El email no debe ser nulo");
            assertNotNull(p.getDireccionProv(),  "La dirección no debe ser nula");
        });
    }

    @Test
    @DisplayName("obtenerTodos - lanza excepción cuando falla la consulta a BD")
    void obtenerTodos_lanzaExcepcion_cuandoFallaJdbc() {
        when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                .thenThrow(new RuntimeException("Error de conexión a la base de datos"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> proveedoresService.obtenerTodosLosProveedores());

        assertEquals("Error de conexión a la base de datos", excepcion.getMessage());
        verify(jdbcTemplate, times(1)).query(anyString(), any(RowMapper.class));
    }

    // ─────────────────────────────────────────────
    //  crearProveedor()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("crearProveedor - ejecuta INSERT con todos los campos correctamente")
    void crearProveedor_ejecutaInsertCorrectamente() {
        when(jdbcTemplate.update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString()))
                .thenReturn(1);

        assertDoesNotThrow(() -> proveedoresService.crearProveedor(proveedorActivo));

        verify(jdbcTemplate, times(1)).update(
                anyString(),
                eq("Harinera del Valle"),
                eq("3001234567"),
                eq(true),
                eq("contacto@harinera.com"),
                eq("Calle 10 #5-23, Cali")
        );
    }

    @Test
    @DisplayName("crearProveedor - ejecuta INSERT con proveedor inactivo")
    void crearProveedor_ejecutaInsert_conProveedorInactivo() {
        when(jdbcTemplate.update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString()))
                .thenReturn(1);

        assertDoesNotThrow(() -> proveedoresService.crearProveedor(proveedorInactivo));

        verify(jdbcTemplate, times(1)).update(
                anyString(),
                eq("Lácteos El Campo"),
                eq("3119876543"),
                eq(false),
                eq("ventas@lacteos.com"),
                eq("Carrera 8 #12-45, Bogotá")
        );
    }

    @Test
    @DisplayName("crearProveedor - lanza excepción cuando falla el INSERT")
    void crearProveedor_lanzaExcepcion_cuandoFallaInsert() {
        when(jdbcTemplate.update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString()))
                .thenThrow(new RuntimeException("Error al insertar proveedor"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> proveedoresService.crearProveedor(proveedorActivo));

        assertEquals("Error al insertar proveedor", excepcion.getMessage());
    }

    @Test
    @DisplayName("crearProveedor - solo interactúa con jdbcTemplate una vez")
    void crearProveedor_soloUnaInteraccionConJdbc() {
        when(jdbcTemplate.update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString()))
                .thenReturn(1);

        proveedoresService.crearProveedor(proveedorActivo);

        verify(jdbcTemplate, times(1)).update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString());
        verifyNoMoreInteractions(jdbcTemplate);
    }

    // ─────────────────────────────────────────────
    //  editarProveedor()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("editarProveedor - retorna 1 cuando la actualización es exitosa")
    void editarProveedor_retorna1_cuandoActualizaCorrectamente() {
        when(jdbcTemplate.update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString(), anyInt()))
                .thenReturn(1);

        int resultado = proveedoresService.editarProveedor(proveedorActivo);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(
                anyString(),
                eq("Harinera del Valle"),
                eq("3001234567"),
                eq(true),
                eq("contacto@harinera.com"),
                eq("Calle 10 #5-23, Cali"),
                eq(1)
        );
    }

    @Test
    @DisplayName("editarProveedor - retorna 0 cuando el ID no existe")
    void editarProveedor_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString(), anyInt()))
                .thenReturn(0);

        int resultado = proveedoresService.editarProveedor(proveedorActivo);

        assertEquals(0, resultado);
    }

    @Test
    @DisplayName("editarProveedor - lanza excepción cuando falla el UPDATE")
    void editarProveedor_lanzaExcepcion_cuandoFallaUpdate() {
        when(jdbcTemplate.update(anyString(),
                anyString(), anyString(), anyBoolean(), anyString(), anyString(), anyInt()))
                .thenThrow(new RuntimeException("Error al actualizar proveedor"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> proveedoresService.editarProveedor(proveedorActivo));

        assertEquals("Error al actualizar proveedor", excepcion.getMessage());
    }

    // ─────────────────────────────────────────────
    //  eliminarProveedor()
    // ─────────────────────────────────────────────

    @Test
    @DisplayName("eliminarProveedor - retorna 1 cuando la eliminación es exitosa")
    void eliminarProveedor_retorna1_cuandoEliminaCorrectamente() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(1);

        int resultado = proveedoresService.eliminarProveedor(1);

        assertEquals(1, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1));
    }

    @Test
    @DisplayName("eliminarProveedor - retorna 0 cuando el ID no existe")
    void eliminarProveedor_retorna0_cuandoIdNoExiste() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(0);

        int resultado = proveedoresService.eliminarProveedor(99);

        assertEquals(0, resultado);
        verify(jdbcTemplate, times(1)).update(anyString(), eq(99));
    }

    @Test
    @DisplayName("eliminarProveedor - lanza excepción cuando falla el DELETE")
    void eliminarProveedor_lanzaExcepcion_cuandoFallaDelete() {
        when(jdbcTemplate.update(anyString(), anyInt()))
                .thenThrow(new RuntimeException("Error al eliminar proveedor"));

        RuntimeException excepcion = assertThrows(RuntimeException.class,
                () -> proveedoresService.eliminarProveedor(1));

        assertEquals("Error al eliminar proveedor", excepcion.getMessage());
        verify(jdbcTemplate, times(1)).update(anyString(), eq(1));
    }

    @Test
    @DisplayName("eliminarProveedor - solo interactúa con jdbcTemplate una vez")
    void eliminarProveedor_soloUnaInteraccionConJdbc() {
        when(jdbcTemplate.update(anyString(), anyInt())).thenReturn(1);

        proveedoresService.eliminarProveedor(1);

        verify(jdbcTemplate, times(1)).update(anyString(), anyInt());
        verifyNoMoreInteractions(jdbcTemplate);
    }
}
