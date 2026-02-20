package com.example.Proyecto.service.Administrador;

import com.example.Proyecto.model.PojoAdmin;
import org.junit.jupiter.api.BeforeEach;
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

import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.*;
import static org.mockito.ArgumentMatchers.*;
import static org.mockito.Mockito.*;

@ExtendWith(MockitoExtension.class)
class ConexionAdminServiceTest {

    @Mock
    private JdbcTemplate jdbcTemplate;

    @Mock
    private PasswordEncoder passwordEncoder;

    @InjectMocks
    private ConexionAdminService service;

    private PojoAdmin crearPojoAdminValido() {
        PojoAdmin admin = new PojoAdmin();
        admin.setId(1);
        admin.setNombre("Carlos López");
        admin.setEmail("carlos@panaderia.com");
        admin.setTelefono("555-1234");
        admin.setContrasena("Password123");
        return admin;
    }

    // ========================================================================
    // TESTS PARA obtenerDetallesAdministrador()
    // ========================================================================
    @Nested
    @DisplayName("obtenerDetallesAdministrador()")
    class ObtenerDetallesAdministradorTests {

        @Test
        @DisplayName("Debe retornar lista con datos de administradores cuando existen registros")
        void debeRetornarListaConAdministradores() {
            // Arrange
            Map<String, Object> adminEsperado = new HashMap<>();
            adminEsperado.put("Id:", 1);
            adminEsperado.put("Nombre:", "Carlos López");
            adminEsperado.put("Correo Electronico:", "carlos@panaderia.com");
            adminEsperado.put("Telefono:", "555-1234");

            List<Map<String, Object>> listaEsperada = List.of(adminEsperado);

            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenReturn(listaEsperada);

            // Act
            List<Map<String, Object>> resultado = service.obtenerDetallesAdministrador();

            // Assert
            assertNotNull(resultado);
            assertEquals(1, resultado.size());
            assertEquals(1, resultado.get(0).get("Id:"));
            assertEquals("Carlos López", resultado.get(0).get("Nombre:"));
            assertEquals("carlos@panaderia.com", resultado.get(0).get("Correo Electronico:"));
            assertEquals("555-1234", resultado.get(0).get("Telefono:"));

            verify(jdbcTemplate).query(
                    eq("SELECT ID_ADMIN, NOMBRE_ADMIN, TELEFONO_ADMIN, EMAIL_ADMIN FROM Administradores"),
                    any(RowMapper.class)
            );
        }

        @Test
        @DisplayName("Debe retornar lista vacía cuando no hay administradores")
        void debeRetornarListaVaciaCuandoNoHayRegistros() {
            // Arrange
            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenReturn(Collections.emptyList());

            // Act
            List<Map<String, Object>> resultado = service.obtenerDetallesAdministrador();

            // Assert
            assertNotNull(resultado);
            assertTrue(resultado.isEmpty());
        }

        @Test
        @DisplayName("Debe propagar excepción cuando JdbcTemplate falla")
        void debePropararExcepcionCuandoFallaBaseDeDatos() {
            // Arrange
            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenThrow(new DataAccessException("Error de conexión") {});

            // Act & Assert
            assertThrows(DataAccessException.class, () -> service.obtenerDetallesAdministrador());
        }

        @Test
        @DisplayName("Debe retornar múltiples administradores correctamente")
        void debeRetornarMultiplesAdministradores() {
            // Arrange
            Map<String, Object> admin1 = new HashMap<>();
            admin1.put("Id:", 1);
            admin1.put("Nombre:", "Carlos");
            admin1.put("Correo Electronico:", "carlos@mail.com");
            admin1.put("Telefono:", "111");

            Map<String, Object> admin2 = new HashMap<>();
            admin2.put("Id:", 2);
            admin2.put("Nombre:", "Ana");
            admin2.put("Correo Electronico:", "ana@mail.com");
            admin2.put("Telefono:", "222");

            when(jdbcTemplate.query(anyString(), any(RowMapper.class)))
                    .thenReturn(List.of(admin1, admin2));

            // Act
            List<Map<String, Object>> resultado = service.obtenerDetallesAdministrador();

            // Assert
            assertEquals(2, resultado.size());
            assertEquals("Carlos", resultado.get(0).get("Nombre:"));
            assertEquals("Ana", resultado.get(1).get("Nombre:"));
        }
    }

    // ========================================================================
    // TESTS PARA crearAdmin()
    // ========================================================================
    @Nested
    @DisplayName("crearAdmin()")
    class CrearAdminTests {

        @Test
        @DisplayName("Debe crear admin exitosamente con datos válidos")
        void debeCrearAdminExitosamente() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            when(passwordEncoder.encode("Password123")).thenReturn("hashedPassword123");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any())).thenReturn(1);

            // Act
            boolean resultado = service.crearAdmin(admin);

            // Assert
            assertTrue(resultado);
            verify(passwordEncoder).encode("Password123");
            verify(jdbcTemplate).update(
                    eq("INSERT INTO Administradores (NOMBRE_ADMIN, EMAIL_ADMIN, TELEFONO_ADMIN, CONTRASENA_ADMIN) VALUES (?, ?, ?, ?)"),
                    eq("Carlos López"),
                    eq("carlos@panaderia.com"),
                    eq("555-1234"),
                    eq("hashedPassword123")
            );
        }

        @Test
        @DisplayName("Debe retornar false cuando la contraseña es null")
        void debeRetornarFalseCuandoContrasenaEsNull() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            admin.setContrasena(null);

            // Act
            boolean resultado = service.crearAdmin(admin);

            // Assert
            assertFalse(resultado);
            verify(passwordEncoder, never()).encode(any());
            verify(jdbcTemplate, never()).update(anyString(), any(), any(), any(), any());
        }

        @Test
        @DisplayName("Debe retornar false cuando la contraseña está vacía")
        void debeRetornarFalseCuandoContrasenaEstaVacia() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            admin.setContrasena("");

            // Act
            boolean resultado = service.crearAdmin(admin);

            // Assert
            assertFalse(resultado);
            verify(passwordEncoder, never()).encode(any());
        }

        @Test
        @DisplayName("Debe retornar false cuando la contraseña es solo espacios en blanco")
        void debeRetornarFalseCuandoContrasenaEsSoloEspacios() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            admin.setContrasena("   ");

            // Act
            boolean resultado = service.crearAdmin(admin);

            // Assert
            assertFalse(resultado);
            verify(passwordEncoder, never()).encode(any());
        }

        @Test
        @DisplayName("Debe retornar false cuando JdbcTemplate lanza DataAccessException")
        void debeRetornarFalseCuandoFallaBaseDeDatos() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            when(passwordEncoder.encode("Password123")).thenReturn("hashed");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any()))
                    .thenThrow(new DataAccessException("Duplicate entry") {});

            // Act
            boolean resultado = service.crearAdmin(admin);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("Debe retornar false cuando update afecta 0 filas")
        void debeRetornarFalseCuandoUpdateAfectaCeroFilas() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            when(passwordEncoder.encode("Password123")).thenReturn("hashed");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any())).thenReturn(0);

            // Act
            boolean resultado = service.crearAdmin(admin);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("Debe hashear la contraseña antes de guardarla")
        void debeHashearContrasenaAntesDeGuardar() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            admin.setContrasena("MiClaveSecreta");
            when(passwordEncoder.encode("MiClaveSecreta")).thenReturn("$2a$10$hashedValue");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any())).thenReturn(1);

            // Act
            service.crearAdmin(admin);

            // Assert - verificar que la contraseña hasheada es la que se pasa al update
            ArgumentCaptor<String> captor = ArgumentCaptor.forClass(String.class);
            verify(jdbcTemplate).update(anyString(), any(), any(), any(), captor.capture());
            assertEquals("$2a$10$hashedValue", captor.getValue());
        }
    }

    // ========================================================================
    // TESTS PARA actualizarAdministrador()
    // ========================================================================
    @Nested
    @DisplayName("actualizarAdministrador()")
    class ActualizarAdministradorTests {

        @Test
        @DisplayName("Debe actualizar administrador exitosamente con datos válidos")
        void debeActualizarExitosamente() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            when(passwordEncoder.encode("Password123")).thenReturn("newHashedPassword");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any(), any())).thenReturn(1);

            // Act
            boolean resultado = service.actualizarAdministrador(admin);

            // Assert
            assertTrue(resultado);
            verify(jdbcTemplate).update(
                    eq("UPDATE Administradores SET NOMBRE_ADMIN = ?, EMAIL_ADMIN = ?, TELEFONO_ADMIN=?, CONTRASENA_ADMIN = ? WHERE ID_ADMIN = ?"),
                    eq("Carlos López"),
                    eq("carlos@panaderia.com"),
                    eq("555-1234"),
                    eq("newHashedPassword"),
                    eq(1)
            );
        }

        @Test
        @DisplayName("Debe retornar false cuando el ID no existe (0 filas afectadas)")
        void debeRetornarFalseCuandoIdNoExiste() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            admin.setId(999);
            when(passwordEncoder.encode("Password123")).thenReturn("hashed");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any(), any())).thenReturn(0);

            // Act
            boolean resultado = service.actualizarAdministrador(admin);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("Debe retornar false cuando JdbcTemplate lanza DataAccessException")
        void debeRetornarFalseCuandoFallaBaseDeDatos() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            when(passwordEncoder.encode("Password123")).thenReturn("hashed");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any(), any()))
                    .thenThrow(new DataAccessException("Connection lost") {});

            // Act
            boolean resultado = service.actualizarAdministrador(admin);

            // Assert
            assertFalse(resultado);
        }

        @Test
        @DisplayName("BUG: Debe fallar si la contraseña es null (no hay validación)")
        void debeFallarSiContrasenaEsNull() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            admin.setContrasena(null);

            // Este test DOCUMENTA el bug: actualizarAdministrador no valida
            // contraseña nula como sí lo hace crearAdmin.
            // passwordEncoder.encode(null) lanzará una excepción.
            when(passwordEncoder.encode(null))
                    .thenThrow(new IllegalArgumentException("rawPassword cannot be null"));

            // Act & Assert
            // Actualmente lanza excepción no controlada en lugar de retornar false
            // Después de corregir el bug, este test debería verificar que retorna false
            assertThrows(IllegalArgumentException.class,
                    () -> service.actualizarAdministrador(admin));
        }

        @Test
        @DisplayName("Debe re-hashear la contraseña al actualizar")
        void debeRehashearContrasenaAlActualizar() {
            // Arrange
            PojoAdmin admin = crearPojoAdminValido();
            admin.setContrasena("NuevaPassword456");
            when(passwordEncoder.encode("NuevaPassword456")).thenReturn("$2a$10$nuevoHash");
            when(jdbcTemplate.update(anyString(), any(), any(), any(), any(), any())).thenReturn(1);

            // Act
            service.actualizarAdministrador(admin);

            // Assert
            ArgumentCaptor<Object> captors = ArgumentCaptor.forClass(Object.class);
            verify(jdbcTemplate).update(anyString(),
                    any(), any(), any(), captors.capture(), any());
            assertEquals("$2a$10$nuevoHash", captors.getValue());
        }
    }
}
