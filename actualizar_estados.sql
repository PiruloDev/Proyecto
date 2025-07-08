-- Script para actualizar estados de pedidos
-- Ejecutar solo si es necesario ajustar los estados

-- Verificar estados existentes
SELECT * FROM Estado_Pedidos;

-- Si necesitas ajustar los estados, puedes usar estas consultas:
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Pendiente' WHERE ID_ESTADO_PEDIDO = 1;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'En Preparación' WHERE ID_ESTADO_PEDIDO = 2;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Listo' WHERE ID_ESTADO_PEDIDO = 3;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Entregado' WHERE ID_ESTADO_PEDIDO = 4;
-- UPDATE Estado_Pedidos SET NOMBRE_ESTADO = 'Cancelado' WHERE ID_ESTADO_PEDIDO = 5;

-- O insertar si no existen:
-- INSERT INTO Estado_Pedidos (NOMBRE_ESTADO) VALUES 
-- ('Pendiente'),
-- ('En Preparación'),
-- ('Listo'),
-- ('Entregado'),
-- ('Cancelado');
