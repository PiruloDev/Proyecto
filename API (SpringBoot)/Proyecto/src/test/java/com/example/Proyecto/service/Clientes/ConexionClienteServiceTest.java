package com.example.Proyecto.service.Clientes;

import com.example.Proyecto.model.PojoCliente;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Nested;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.extension.ExtendWith;
import org.mockito.ArgumentCaptor;
import org.mockito.InjectMocks;
import org.mockito.Mock;
import org.mockito.junit.jupiter.MockitoExtension;
import org.springframework.dao.DataAccessException;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.security.crypto.password.PasswordEncoder;

import java.util.*;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;
import static org.mockito.Mockito.doReturn;
import static org.mockito.Mockito.doThrow;

@ExtendWith(MockitoExtension.class)
class ConexionClienteServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @Mock
    private PasswordEncoder passwordEncoder;

    @InjectMocks
    private ConexionClienteService service;

    private PojoCliente crearClienteValido() {
        PojoCliente cliente = new PojoCliente();
        cliente.setId(1);
        cliente.setNombre("María García");
        cliente.setEmail("maria@correo.com");
        cliente.setTelefono("555-9876");
        cliente.setContrasena("ClaveSegura123");
        return cliente;
    }

    // ========================================================================
    // TESTS PARA obtenerDetallesCliente()
    // ========================================================================
    @Nested
    @DisplayName("obtenerDetallesCliente()")
    class ObtenerDetallesClienteTests {

        @Test
        @DisplayName("Debe retornar lista con datos de clientes cuando existen registros")
        void debeRetornarListaConClientes() {
            // Arrange
            Map<String, Object> clienteEsperado = new HashMap<>();
            clienteEsperado.put("Nombre:", "María García");
            clienteEsperado.put("Correo Electronico:", "maria@correo.com");
            clienteEsperado.put("Telefono:", "555-9876");

            List<Map<String, Object>> listaEsperada = List.of(clienteEsperado);

            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenReturn(listaEsperada);

            // Act
            List<Map<String, Object>> resultado = service.obtenerDetallesCliente();

            // Assert
            assertNotNull(resultado);
            assertEquals(1, resultado.size());
            assertEquals("María García", resultado.get(0).get("Nombre:"));
            assertEquals("maria@correo.com", resultado.get(0).get("Correo Electronico:"));
            assertEquals("555-9876", resultado.get(0).get("Telefono:"));

            verify(jdbcTemplate).query(
                    eq("SELECT TELEFONO_CLI, EMAIL_CLI, NOMBRE_CLI FROM Clientes"),
                    any(RowMapper.class)
            );
        }

        @Test
        @DisplayName("Debe retornar lista vacía cuando no hay clientes")
        void debeRetornarListaVaciaSinRegistros() {
            // Arrange
            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenReturn(Collections.emptyList());

            // Act
            List<Map<String, Object>> resultado = service.obtenerDetallesCliente();

            // Assert
            assertNotNull(resultado);
            assertTrue(resultado.isEmpty());
        }

        @Test
        @DisplayName("Debe propagar excepción cuando JdbcTemplate falla")
        void debePropagarExcepcionCuandoFallaBD() {
            // Arrange
            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenThrow(new DataAccessException("Error de conexión") {});

            // Act & Assert
            assertThrows(DataAccessException.class, () -> service.obtenerDetallesCliente());
        }

        @Test
        @DisplayName("Debe retornar múltiples clientes correctamente")
        void debeRetornarMultiplesClientes() {
            // Arrange
            Map<String, Object> cliente1 = new HashMap<>();
            cliente1.put("Nombre:", "María");
            cliente1.put("Correo Electronico:", "maria@mail.com");
            cliente1.put("Telefono:", "111");

            Map<String, Object> cliente2 = new HashMap<>();
            cliente2.put("Nombre:", "Pedro");
            cliente2.put("Correo Electronico:", "pedro@mail.com");
            cliente2.put("Telefono:", "222");

            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenReturn(List.of(cliente1, cliente2));

            // Act
            List<Map<String, Object>> resultado = service.obtenerDetallesCliente();

            // Assert
            assertEquals(2, resultado.size());
            assertEquals("María", resultado.get(0).get("Nombre:"));
            assertEquals("Pedro", resultado.get(1).get("Nombre:"));
        }
    }

    // ========================================================================
    // TESTS PARA crearCliente()
    // ========================================================================
    @Nested
    @DisplayName("crearCliente()")
    class CrearClienteTests {

        @Test
        @DisplayName("Debe crear cliente exitosamente con datos válidos")
        void debeCrearClienteExitosamente() {
            // Arrange
            PojoCliente cliente = crearClienteValido();
            when(passwordEncoder.encode("ClaveSegura123")).thenReturn("hashedPassword");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any())).thenReturn(1);

            // Act
            boolean resultado = service.crearCliente(cliente);

            // Assert
            assertTrue(resultado);
            verify(passwordEncoder).encode("ClaveSegura123");
            verify(jdbcTemplate).update(
                    eq("INSERT INTO Clientes (NOMBRE_CLI, EMAIL_CLI, TELEFONO_CLI, CONTRASENA_CLI) VALUES (?, ?, ?, ?)"),
                    eq("María García"),
                    eq("maria@correo.com"),
                    eq("555-9876"),
                    eq("hashedPassword")
            );
        }

        @Test
        @DisplayName("Debe retornar false cuando la contraseña es null")
        void debeRetornarFalseConContrasenaNull() {
            // Arrange
            PojoCliente cliente = crearClienteValido();
            cliente.setContrasena(null);

            // Act
            boolean resultado = service.crearCliente(cliente);

            // Assert
            assertFalse(resultado);
            verify(passwordEncoder, never()).encode(any());
            verify(jdbcTemplate, never()).update(anyString(), any(), any(), any(), any());
        }

        @Test
        @DisplayName("Debe retornar false cuando la contraseña está vacía")
        void debeRetornarFalseConContrasenaVacia() {
            // Arrange
            PojoCliente cliente = crearClienteValido();
            cliente.setContrasena("");

            // Act
            boolean resultado = service.crearCliente(cliente);

            // Assert
            assertFalse(resultado);
            verify(passwordEncoder, never()).encode(any());
        }

        @Test
        @DisplayName("Debe retornar false cuando la contraseña es solo espacios")
        void debeRetornarFalseConContrasenaSoloEspacios() {
            // Arrange
            PojoCliente cliente = crearClienteValido();
            cliente.setContrasena("   ");

            // Act
            boolean resultado = service.crearCliente(cliente);

            // Assert
            assertFalse(resultado);
            verify(passwordEncoder, never()).encode(any());
        }

        @Test
        @DisplayName("Debe retornar false cuando JdbcTemplate lanza DataAccessException")
        void debeRetornarFalseCuandoFallaBD() {
            // Arrange
            PojoCliente cliente = crearClienteValido();
            when(passwordEncoder.encode("ClaveSegura123")).thenReturn("hashed");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any()))
                    .thenThrow(new DataAccessException("Duplicate entry") {});

            // Act
            boolean resultado = service.crearCliente(cliente);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("Debe retornar false cuando update afecta 0 filas")
        void debeRetornarFalseCuandoUpdateAfectaCeroFilas() {
            // Arrange
            PojoCliente cliente = crearClienteValido();
            when(passwordEncoder.encode("ClaveSegura123")).thenReturn("hashed");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any())).thenReturn(0);

            // Act
            boolean resultado = service.crearCliente(cliente);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("Debe hashear la contraseña antes de guardarla")
        void debeHashearContrasena() {
            // Arrange
            PojoCliente cliente = crearClienteValido();
            cliente.setContrasena("MiClave");
            when(passwordEncoder.encode("MiClave")).thenReturn("$2a$10$hashedClave");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any())).thenReturn(1);

            // Act
            service.crearCliente(cliente);

            // Assert
            ArgumentCaptor<String> captor = ArgumentCaptor.forClass(String.class);
            verify(jdbcTemplate).update(anyString(), any(), any(), any(), captor.capture());
            assertEquals("$2a$10$hashedClave", captor.getValue());
        }
    }

    // ========================================================================
    // TESTS PARA actualizarCliente()
    // ========================================================================
    @Nested
    @DisplayName("actualizarCliente()")
    class ActualizarClienteTests {

        @Test
        @DisplayName("Debe retornar false cuando el mapa de campos está vacío")
        void debeRetornarFalseConMapaVacio() {
            // Arrange
            Map<String, Object> campos = new HashMap<>();

            // Act
            boolean resultado = service.actualizarCliente(1, campos);

            // Assert
            assertFalse(resultado);
            verify(jdbcTemplate, never()).update(anyString(), any(Object[].class));
        }

        @Test
        @DisplayName("Debe actualizar solo el nombre correctamente")
        void debeActualizarSoloNombre() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("nombre", "Nuevo Nombre");

            doReturn(1).when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(5, campos);

            // Assert
            assertTrue(resultado);

            ArgumentCaptor<String> sqlCaptor = ArgumentCaptor.forClass(String.class);
            ArgumentCaptor<Object[]> paramsCaptor = ArgumentCaptor.forClass(Object[].class);
            verify(jdbcTemplate).update(sqlCaptor.capture(), paramsCaptor.capture());

            assertEquals("UPDATE Clientes SET NOMBRE_CLI = ? WHERE ID_CLIENTE = ?", sqlCaptor.getValue());
            assertArrayEquals(new Object[]{"Nuevo Nombre", 5}, paramsCaptor.getValue());
        }

        @Test
        @DisplayName("Debe actualizar solo el email correctamente")
        void debeActualizarSoloEmail() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("email", "nuevo@correo.com");

            doReturn(1).when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(3, campos);

            // Assert
            assertTrue(resultado);

            ArgumentCaptor<String> sqlCaptor = ArgumentCaptor.forClass(String.class);
            ArgumentCaptor<Object[]> paramsCaptor = ArgumentCaptor.forClass(Object[].class);
            verify(jdbcTemplate).update(sqlCaptor.capture(), paramsCaptor.capture());

            assertEquals("UPDATE Clientes SET EMAIL_CLI = ? WHERE ID_CLIENTE = ?", sqlCaptor.getValue());
            assertArrayEquals(new Object[]{"nuevo@correo.com", 3}, paramsCaptor.getValue());
        }

        @Test
        @DisplayName("Debe actualizar solo el teléfono correctamente")
        void debeActualizarSoloTelefono() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("telefono", "999-0000");

            doReturn(1).when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(2, campos);

            // Assert
            assertTrue(resultado);

            ArgumentCaptor<String> sqlCaptor = ArgumentCaptor.forClass(String.class);
            ArgumentCaptor<Object[]> paramsCaptor = ArgumentCaptor.forClass(Object[].class);
            verify(jdbcTemplate).update(sqlCaptor.capture(), paramsCaptor.capture());

            assertEquals("UPDATE Clientes SET TELEFONO_CLI = ? WHERE ID_CLIENTE = ?", sqlCaptor.getValue());
            assertArrayEquals(new Object[]{"999-0000", 2}, paramsCaptor.getValue());
        }

        @Test
        @DisplayName("Debe actualizar solo la contraseña y hashearla")
        void debeActualizarSoloContrasenaHasheada() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("contrasena", "NuevaClave456");

            when(passwordEncoder.encode("NuevaClave456")).thenReturn("$2a$10$nuevoHash");
            doReturn(1).when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(4, campos);

            // Assert
            assertTrue(resultado);
            verify(passwordEncoder).encode("NuevaClave456");

            ArgumentCaptor<Object[]> paramsCaptor = ArgumentCaptor.forClass(Object[].class);
            verify(jdbcTemplate).update(anyString(), paramsCaptor.capture());

            assertEquals("$2a$10$nuevoHash", paramsCaptor.getValue()[0]);
            assertEquals(4, paramsCaptor.getValue()[1]);
        }

        @Test
        @DisplayName("Debe actualizar múltiples campos con comas correctas en SQL")
        void debeActualizarMultiplesCampos() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("nombre", "Ana López");
            campos.put("email", "ana@nuevo.com");
            campos.put("telefono", "777-1234");

            doReturn(1).when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(10, campos);

            // Assert
            assertTrue(resultado);

            ArgumentCaptor<String> sqlCaptor = ArgumentCaptor.forClass(String.class);
            ArgumentCaptor<Object[]> paramsCaptor = ArgumentCaptor.forClass(Object[].class);
            verify(jdbcTemplate).update(sqlCaptor.capture(), paramsCaptor.capture());

            assertEquals(
                    "UPDATE Clientes SET NOMBRE_CLI = ?, EMAIL_CLI = ?, TELEFONO_CLI = ? WHERE ID_CLIENTE = ?",
                    sqlCaptor.getValue()
            );
            assertArrayEquals(new Object[]{"Ana López", "ana@nuevo.com", "777-1234", 10}, paramsCaptor.getValue());
        }

        @Test
        @DisplayName("Debe actualizar los 4 campos incluyendo contraseña hasheada")
        void debeActualizarTodosLosCampos() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("nombre", "Luis");
            campos.put("email", "luis@mail.com");
            campos.put("telefono", "000");
            campos.put("contrasena", "Pass789");

            when(passwordEncoder.encode("Pass789")).thenReturn("hashedPass789");
            doReturn(1).when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(7, campos);

            // Assert
            assertTrue(resultado);

            ArgumentCaptor<String> sqlCaptor = ArgumentCaptor.forClass(String.class);
            ArgumentCaptor<Object[]> paramsCaptor = ArgumentCaptor.forClass(Object[].class);
            verify(jdbcTemplate).update(sqlCaptor.capture(), paramsCaptor.capture());

            assertEquals(
                    "UPDATE Clientes SET NOMBRE_CLI = ?, EMAIL_CLI = ?, TELEFONO_CLI = ?, CONTRASENA_CLI = ? WHERE ID_CLIENTE = ?",
                    sqlCaptor.getValue()
            );
            assertArrayEquals(new Object[]{"Luis", "luis@mail.com", "000", "hashedPass789", 7}, paramsCaptor.getValue());
        }

        @Test
        @DisplayName("Debe retornar false cuando el ID no existe (0 filas afectadas)")
        void debeRetornarFalseCuandoIdNoExiste() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("nombre", "Test");

            doReturn(0).when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(999, campos);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("Debe retornar false cuando JdbcTemplate lanza DataAccessException")
        void debeRetornarFalseCuandoFallaBD() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("nombre", "Test");

            doThrow(new DataAccessException("Connection lost") {})
                    .when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(1, campos);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("BUG: Campos no reconocidos generan SQL inválido sin columnas SET")
        void bugCamposNoReconocidosGeneranSqlInvalido() {
            // Arrange
            Map<String, Object> campos = new LinkedHashMap<>();
            campos.put("direccion", "Calle Falsa 123");

            // El SQL generado será: "UPDATE Clientes SET  WHERE ID_CLIENTE = ?"
            // Esto es SQL inválido y JdbcTemplate lanzará una excepción
            doThrow(new DataAccessException("SQL syntax error") {})
                    .when(jdbcTemplate).update(anyString(), any(Object[].class));

            // Act
            boolean resultado = service.actualizarCliente(1, campos);

            // Assert
            assertFalse(resultado);
        }
    }
}
