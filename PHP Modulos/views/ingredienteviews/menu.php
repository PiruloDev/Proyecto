<!DOCTYPE html>
<html>
<head>
    <title>Menú Principal</title>
</head>
<body>
    <h1>Bienvenido al Sistema</h1>
    <p>Selecciona un módulo:</p>

    <form action="Ingredienteindex.php" method="get">
        <input type="hidden" name="modulo" value="ingredientes">
        <input type="hidden" name="accion" value="listar">
        <button type="submit">Ingredientes</button>
    </form>

    <form action="Ingredienteindex.php" method="get">
        <input type="hidden" name="modulo" value="categoria">
        <input type="hidden" name="accion" value="listar">
        <button type="submit">Categorías</button>
    </form>

    <form action="Ingredienteindex.php" method="get">
        <input type="hidden" name="modulo" value="proveedores">
        <input type="hidden" name="accion" value="listar">
        <button type="submit">Proveedores</button>
    </form>

    <form action="Ingredienteindex.php" method="get">
    <input type="hidden" name="modulo" value="detallePedidos">  <!-- ojo aquí -->
    <input type="hidden" name="accion" value="listar">
    <button type="submit">Detalle Pedidos</button>
</form>

</body>
</html>
