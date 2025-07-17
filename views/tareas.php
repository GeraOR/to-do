<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Obtener las tareas del usuario
$estado = isset($_GET["estado"]) ? $_GET["estado"] : null;

if ($estado === "pendiente" || $estado === "completada") {
    $completada = ($estado === "completada") ? 1 : 0;
    $sql = "SELECT * FROM tareas WHERE usuario_id = ? AND completada = ? ORDER BY fecha_limite IS NULL, fecha_limite";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $completada);
} else {
    $sql = "SELECT * FROM tareas WHERE usuario_id = ? ORDER BY fecha_limite IS NULL, fecha_limite";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
}

$stmt->execute();
$result = $stmt->get_result();
$tareas = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas</title>
    <link rel="stylesheet" href="../styles/tareas.css">
</head>
<body>
    <h2>Tus Tareas</h2>

<form method="POST" action="../scripts/add_task.php">
    <input type="text" name="titulo" placeholder="Título" required>
    <textarea name="descripcion" placeholder="Descripción" required></textarea>
    <input type="date" name="fecha_limite">
    <button type="submit">Agregar Tarea</button>
</form>
<?php if (isset($_SESSION["task_success"])): ?>
    <p style="color: green;"><?php echo $_SESSION["task_success"]; ?></p>
    <?php unset($_SESSION["task_success"]); ?>
<?php endif; ?>
<form method="GET" action="">
    <label for="filtro_estado">Filtrar por estado:</label>
    <select name="estado" id="filtro_estado" onchange="this.form.submit()">
        <option value="">-- Todas --</option>
        <option value="pendiente" <?= (isset($_GET['estado']) && $_GET['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendientes</option>
        <option value="completada" <?= (isset($_GET['estado']) && $_GET['estado'] === 'completada') ? 'selected' : '' ?>>Completadas</option>
    </select>
</form>
<br>

<ul>
    <?php foreach ($tareas as $tarea): ?>
        <li>
            <strong><?php echo htmlspecialchars($tarea["titulo"]); ?></strong>
            <p><?php echo htmlspecialchars($tarea["descripcion"]); ?></p>
            <?php if ($tarea["fecha_limite"]): ?>
                <small>Fecha límite: <?php echo $tarea["fecha_limite"]; ?></small>
            <?php endif; ?>
            <form method="POST" action="../scripts/toggle_task.php" style="display:inline;">
    <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
    <?php if ($tarea["completada"]): ?>
        <button type="submit">Desmarcar</button>
    <?php else: ?>
        <button type="submit">Marcar como completada</button>
    <?php endif; ?>
</form>

            <form method="POST" action="../scripts/delete_task.php" style="display:inline;">
            <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
            <button type="submit" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</button>
        </form>
        <button type="button" onclick="abrirModal(<?php echo $tarea['id']; ?>, '<?php echo htmlspecialchars(addslashes($tarea['titulo'])); ?>', '<?php echo htmlspecialchars(addslashes($tarea['descripcion'])); ?>', '<?php echo $tarea['fecha_limite']; ?>')">
    Editar
</button>

        </li>
        <hr>
    <?php endforeach; ?>
</ul>

<a href="../scripts/logout.php">Cerrar sesión</a>

<!-- Modal -->
<div id="modalEditar" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div style="background:white; padding:20px; max-width:400px; margin:100px auto; border-radius:10px; position:relative;">
        <button onclick="cerrarModal()" style="position:absolute; top:10px; right:10px;">&times;</button>
        <h3>Editar Tarea</h3>
        <form method="POST" action="../scripts/edit_task.php">
            <input type="hidden" name="tarea_id" id="modalTareaId">
            <label>Título:</label>
            <input type="text" name="titulo" id="modalTitulo" required>
            <label>Descripción:</label>
            <textarea name="descripcion" id="modalDescripcion" required></textarea>
            <label>Fecha límite:</label>
            <input type="date" name="fecha_limite" id="modalFecha">
            <button type="submit">Guardar Cambios</button>
        </form>
    </div>
</div>
<script>
function abrirModal(id, titulo, descripcion, fecha) {
    document.getElementById('modalTareaId').value = id;
    document.getElementById('modalTitulo').value = titulo;
    document.getElementById('modalDescripcion').value = descripcion;
    document.getElementById('modalFecha').value = fecha;
    document.getElementById('modalEditar').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalEditar').style.display = 'none';
}
</script>

</body>
</html>