<h2>Nueva Receta</h2>
<form method="POST" action="?accion=guardar">
    <label for="idProducto">Producto:</label>
    <input type="number" name="idProducto" required><br><br>

    <h3>Ingredientes</h3>
    <div id="ingredientes-container">
        <div class="ingrediente">
            <label>ID Ingrediente:</label>
            <input type="number" name="ingredientes[0][idIngrediente]" required>
            <label>Cantidad:</label>
            <input type="text" name="ingredientes[0][cantidadNecesaria]" required>
            <label>Unidad:</label>
            <input type="text" name="ingredientes[0][unidadMedida]" required>
        </div>
    </div>

    <button type="button" onclick="agregarIngrediente()">➕ Agregar Ingrediente</button>
    <br><br>
    <button type="submit">Guardar Receta</button>
</form>

<script>
let index = 1;
function agregarIngrediente() {
    const container = document.getElementById('ingredientes-container');
    const div = document.createElement('div');
    div.className = 'ingrediente';
    div.innerHTML = `
        <label>ID Ingrediente:</label>
        <input type="number" name="ingredientes[${index}][idIngrediente]" required>
        <label>Cantidad:</label>
        <input type="text" name="ingredientes[${index}][cantidadNecesaria]" required>
        <label>Unidad:</label>
        <input type="text" name="ingredientes[${index}][unidadMedida]" required>
    `;
    container.appendChild(div);
    index++;
}
</script>
