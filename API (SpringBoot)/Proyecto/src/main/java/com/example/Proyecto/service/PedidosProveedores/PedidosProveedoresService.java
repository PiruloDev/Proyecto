package com.example.Proyecto.service.PedidosProveedores;

import com.example.Proyecto.model.PedidosProveedores;
import com.example.Proyecto.model.DetallePedidoProveedores;
import io.swagger.v3.oas.annotations.Operation;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.jdbc.core.JdbcTemplate;
import org.springframework.jdbc.core.RowMapper;
import org.springframework.jdbc.support.GeneratedKeyHolder;
import org.springframework.jdbc.support.KeyHolder;
import org.springframework.lang.NonNull;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.bind.annotation.PatchMapping;
import org.springframework.web.bind.annotation.PathVariable;

import java.math.BigDecimal;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.List;


@Service
public class PedidosProveedoresService {

    @Autowired
    private JdbcTemplate jdbcTemplate;

    // RowMapper para PedidosProveedores (Encabezado)
    private final RowMapper<PedidosProveedores> pedidosProveedoresRowMapper = new RowMapper<PedidosProveedores>() {
        @Override
        public PedidosProveedores mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
            PedidosProveedores pedido = new PedidosProveedores();
            // Mapeo utilizando los nombres exactos de las columnas de la tabla Pedidos_Proveedores
            pedido.setIdPedidoProv(rs.getInt("ID_PEDIDO_PROV"));
            pedido.setIdProveedor(rs.getInt("ID_PROVEEDOR"));
            pedido.setNumeroPedido(rs.getInt("NUMERO_PEDIDO"));
            pedido.setFechaPedido(rs.getDate("FECHA_PEDIDO"));
            pedido.setEstadoPedido(rs.getString("ESTADO_PEDIDO"));
            return pedido;
        }
    };

    // RowMapper para DetallePedidoProveedores con el nombre del ingrediente
    private final RowMapper<DetallePedidoProveedores> detallePedidoProveedoresRowMapper = new RowMapper<DetallePedidoProveedores>() {
        @Override
        public DetallePedidoProveedores mapRow(@NonNull ResultSet rs, int rowNum) throws SQLException {
            DetallePedidoProveedores detalle = new DetallePedidoProveedores();
            detalle.setIdDetalleProv(rs.getInt("ID_DETALLE_PROV"));
            detalle.setIdPedidoProv(rs.getInt("ID_PEDIDO_PROV"));
            detalle.setIdIngrediente(rs.getInt("ID_INGREDIENTE"));

            // Obtiene el nombre del ingrediente de la tabla Ingredientes (gracias al JOIN)
            detalle.setNombreIngrediente(rs.getString("NOMBRE_INGREDIENTE"));

            detalle.setCantidad(rs.getInt("CANTIDAD_ORDENADA"));

            detalle.setPrecioUnitario(rs.getBigDecimal("PRECIO_COMPRA"));

            // La columna SUBTOTAL NO existe en la BD. Se calcula en la aplicación.
            if (detalle.getPrecioUnitario() != null) {
                // Multiplica el precio por la cantidad para obtener el subtotal
                detalle.setSubtotal(detalle.getPrecioUnitario().multiply(new java.math.BigDecimal(detalle.getCantidad())));
            } else {
                detalle.setSubtotal(BigDecimal.ZERO);
            }

            return detalle;
        }
    };

    /**
     * Inserta un detalle de pedido de proveedor. Es una función auxiliar interna.
     */
    private void insertarDetallePedidoProveedor(int idPedidoProv, DetallePedidoProveedores detalle) {
        // *** CORRECCIÓN CRÍTICA DE ESCRITURA (AJUSTADO A LA BD) ***
        // 1. Nombre de la tabla: Detalle_Pedido_Proveedores -> detalle_pedidos_proveedores (usando minúsculas)
        // 2. Columnas: Se cambiaron CANTIDAD_INGREDIENTE, PRECIO_UNITARIO por CANTIDAD_ORDENADA, PRECIO_COMPRA
        // 3. Se eliminó 'SUBTOTAL' ya que NO existe en la tabla SQL
        String sql = "INSERT INTO detalle_pedidos_proveedores (ID_PEDIDO_PROV, ID_INGREDIENTE, CANTIDAD_ORDENADA, PRECIO_COMPRA) VALUES (?, ?, ?, ?)";
        jdbcTemplate.update(sql,
                idPedidoProv,
                detalle.getIdIngrediente(),
                detalle.getCantidad(), // Este valor proviene del objeto Java
                detalle.getPrecioUnitario()
                // Se eliminó detalle.getSubtotal() de los parámetros
        );
    }

    /**
     * Crea un pedido de proveedor completo (encabezado y detalles) de forma transaccional.
     * @param pedido El objeto de PedidosProveedores que contiene el encabezado y la lista de detalles.
     * @return El ID del pedido recién creado.
     */
    @Transactional // Asegura que si falla la inserción de detalles, el encabezado se revierta (rollback)
    public int crearPedidoProveedorCompleto(PedidosProveedores pedido) {
        KeyHolder keyHolder = new GeneratedKeyHolder();

        // 1. Insertar Encabezado y obtener el ID generado (ID_PEDIDO_PROV)
        // CORRECCIÓN: Se recomienda usar el nombre de tabla en minúsculas (pedidos_proveedores) para consistencia con el SQL
        String sqlHeader = "INSERT INTO pedidos_proveedores (ID_PROVEEDOR, NUMERO_PEDIDO, FECHA_PEDIDO, ESTADO_PEDIDO) VALUES (?, ?, ?, ?)";
        jdbcTemplate.update(connection -> {
            java.sql.PreparedStatement ps = connection.prepareStatement(sqlHeader, Statement.RETURN_GENERATED_KEYS);
            ps.setInt(1, pedido.getIdProveedor());
            ps.setInt(2, pedido.getNumeroPedido());
            // Conversión de java.util.Date a java.sql.Date para la inserción
            ps.setDate(3, new java.sql.Date(pedido.getFechaPedido().getTime()));
            ps.setString(4, pedido.getEstadoPedido());
            return ps;
        }, keyHolder);

        // Obtener el ID generado (clave primaria)
        int idPedidoProv = keyHolder.getKey().intValue();

        // 2. Insertar Detalles
        if (pedido.getDetalles() != null && !pedido.getDetalles().isEmpty()) {
            for (DetallePedidoProveedores detalle : pedido.getDetalles()) {
                // Llama al método auxiliar para insertar cada detalle
                insertarDetallePedidoProveedor(idPedidoProv, detalle);
            }
        }

        return idPedidoProv;
    }


    /**
     * Obtiene el encabezado de un pedido de proveedor y carga sus detalles de ingredientes.
     * @param idPedidoProv ID del pedido de proveedor.
     * @return PedidosProveedores con su lista de detalles o null si no se encuentra.
     */
    public PedidosProveedores obtenerPedidoConDetalles(int idPedidoProv) {
        String sqlEncabezado =
                "SELECT pp.*, p.NOMBRE_PROV " +
                        "FROM pedidos_proveedores pp " +
                        "LEFT JOIN proveedores p ON pp.ID_PROVEEDOR = p.ID_PROVEEDOR " +
                        "WHERE pp.ID_PEDIDO_PROV = ?";

        PedidosProveedores pedido;
        try {
            pedido = jdbcTemplate.queryForObject(sqlEncabezado, (rs, rowNum) -> {
                PedidosProveedores p = new PedidosProveedores();
                p.setIdPedidoProv(rs.getInt("ID_PEDIDO_PROV"));
                p.setIdProveedor(rs.getInt("ID_PROVEEDOR"));
                p.setNumeroPedido(rs.getInt("NUMERO_PEDIDO"));
                p.setFechaPedido(rs.getDate("FECHA_PEDIDO"));
                p.setEstadoPedido(rs.getString("ESTADO_PEDIDO"));
                p.setNombreProveedor(rs.getString("NOMBRE_PROV")); // ← nuevo
                return p;
            }, idPedidoProv);
        } catch (EmptyResultDataAccessException e) {
            return null;
        }

        String sqlDetalles =
                "SELECT dp.*, i.NOMBRE_INGREDIENTE FROM detalle_pedidos_proveedores dp " +
                        "JOIN ingredientes i ON dp.ID_INGREDIENTE = i.ID_INGREDIENTE " +
                        "WHERE dp.ID_PEDIDO_PROV = ?";

        pedido.setDetalles(jdbcTemplate.query(sqlDetalles, detallePedidoProveedoresRowMapper, idPedidoProv));

        return pedido;
    }

    // GET - Obtener todos los pedidos de proveedores
    public List<PedidosProveedores> obtenerTodosLosPedidosProveedores() {
        // CORRECCIÓN: Se recomienda usar el nombre de tabla en minúsculas (pedidos_proveedores) para consistencia con el SQL
        String sql = "SELECT * FROM pedidos_proveedores";
        return jdbcTemplate.query(sql, pedidosProveedoresRowMapper);
    }

    // Este metodo ya no es usado por el controlador, pero se mantiene para compatibilidad
    public void crearPedidoProveedor(PedidosProveedores pedido) {
        // CORRECCIÓN: Se recomienda usar el nombre de tabla en minúsculas (pedidos_proveedores) para consistencia con el SQL
        String sql = "INSERT INTO pedidos_proveedores (ID_PROVEEDOR, NUMERO_PEDIDO, FECHA_PEDIDO, ESTADO_PEDIDO) VALUES (?, ?, ?, ?)";
        jdbcTemplate.update(sql,
                pedido.getIdProveedor(),
                pedido.getNumeroPedido(),
                pedido.getFechaPedido(),
                pedido.getEstadoPedido()
        );
    }

    // PUT - Actualizar un pedido de proveedor existente
    public int editarPedidoProveedor(PedidosProveedores pedido) {
        // CORRECCIÓN: Se recomienda usar el nombre de tabla en minúsculas (pedidos_proveedores) para consistencia con el SQL
        String sql = "UPDATE pedidos_proveedores SET ID_PROVEEDOR=?, NUMERO_PEDIDO=?, FECHA_PEDIDO=?, ESTADO_PEDIDO=? WHERE ID_PEDIDO_PROV=?";
        return jdbcTemplate.update(sql,
                pedido.getIdProveedor(),
                pedido.getNumeroPedido(),
                pedido.getFechaPedido(),
                pedido.getEstadoPedido(),
                pedido.getIdPedidoProv()
        );
    }

    // DELETE - Eliminar un pedido de proveedor por ID
    public int eliminarPedidoProveedor(int idPedidoProv) {
        // En un escenario real, también se debería eliminar los detalles asociados aquí o mediante CASCADE.
        // CORRECCIÓN: Se recomienda usar el nombre de tabla en minúsculas (pedidos_proveedores) para consistencia con el SQL
        String sql = "DELETE FROM pedidos_proveedores WHERE ID_PEDIDO_PROV = ?";
        return jdbcTemplate.update(sql, idPedidoProv);
    }
    @Transactional
    public void marcarComoEntregado(int idPedidoProv) {
        // 1. Obtener el pedido completo con sus detalles
        PedidosProveedores pedido = obtenerPedidoConDetalles(idPedidoProv);

        if (pedido == null) {
            throw new RuntimeException("Pedido no encontrado");
        }

        // 2. Validar que no haya sido entregado previamente para evitar duplicar stock
        if ("ENTREGADO".equalsIgnoreCase(pedido.getEstadoPedido())) {
            throw new IllegalStateException("Este pedido ya ha sido marcado como ENTREGADO anteriormente.");
        }

        // 3. Actualizar el stock de cada ingrediente en el detalle
        String sqlUpdateStock = "UPDATE ingredientes SET CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE + ? WHERE ID_INGREDIENTE = ?";

        for (DetallePedidoProveedores detalle : pedido.getDetalles()) {
            jdbcTemplate.update(sqlUpdateStock,
                    detalle.getCantidad(),
                    detalle.getIdIngrediente()
            );
        }

        // 4. Cambiar el estado del pedido a 'ENTREGADO'
        String sqlUpdateEstado = "UPDATE pedidos-proveedores SET ESTADO_PEDIDO = 'ENTREGADO' WHERE ID_PEDIDO_PROV = ?";
        jdbcTemplate.update(sqlUpdateEstado, idPedidoProv);
    }
}