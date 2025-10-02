<h2>Editar Receta</h2>

<?php if (!empty($receta)): ?>
<form method="POST" action="?accion=actualizar&id=<?= $receta['idProducto'] ?>">
    <label for="idProducto">Producto:</label>
    <input type="number" name="idProducto" value="<?= $receta['idProducto'] ?>" readonly><br><br>

    <h3>Ingredientes</h3>
    <div id="ingredientes-container">
        <?php foreach ($receta['ingredientes'] as $i => $ing): ?>
            <div class="ingrediente">
                <label>ID Ingrediente:</label>
                <input type="number" name="ingredientes[<?= $i ?>][idIngrediente]" 
                       value="<?= $ing['idIngrediente'] ?>" required>
                <label>Cantidad:</label>
                <input type="text" name="ingredientes[<?= $i ?>][cantidadNecesaria]" 
                       value="<?= $ing['cantidadNecesaria'] ?>" required>
                <label>Unidad:</label>
                <input type="text" name="ingredientes[<?= $i ?>][unidadMedida]" 
                       value="<?= $ing['unidadMedida'] ?>" required>
            </div>
            <br>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="agregarIngrediente()">➕ Agregar Ingrediente</button>
    <br><br>
    <button type="submit">💾 Actualizar Receta</button>
</form>

<script>
let index = <?= count($receta['ingredientes']) ?>;
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
        <br>
    `;
    container.appendChild(div);
    index++;
}
</script>

<?php else: ?>
    <p>No se encontró la receta para editar.</p>
    <a href="?accion=listar">Volver</a>
<?php endif; ?>
