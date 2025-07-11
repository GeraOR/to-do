<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Obtener las tareas del usuario
$sql = "SELECT * FROM tareas WHERE usuario_id = ? ORDER BY fecha_limite IS NULL, fecha_limite";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
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
</head>
<body>
    <h2>Tus Tareas</h2>

<form method="POST" action="../scripts/add_task.php">
    <input type="text" name="titulo" placeholder="Título" required>
    <textarea name="descripcion" placeholder="Descripción" required></textarea>
    <input type="date" name="fecha_limite">
    <button type="submit">Agregar Tarea</button>
</form>

<ul>
    <?php foreach ($tareas as $tarea): ?>
        <li>
            <strong><?php echo htmlspecialchars($tarea["titulo"]); ?></strong>
            <p><?php echo htmlspecialchars($tarea["descripcion"]); ?></p>
            <?php if ($tarea["fecha_limite"]): ?>
                <small>Fecha límite: <?php echo $tarea["fecha_limite"]; ?></small>
            <?php endif; ?>
            <form method="POST" action="../scripts/complete_task.php" style="display:inline;">
                <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
                <?php if ($tarea["completada"]): ?>
                    <span style="color:green; font-weight:bold;">Completada</span>
                <?php else: ?>
                    <button type="submit">Marcar como completada</button>
                <?php endif; ?>
            </form>
            <form method="POST" action="../scripts/delete_task.php" style="display:inline;">
            <input type="hidden" name="tarea_id" value="<?php echo $tarea["id"]; ?>">
            <button type="submit" onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</button>
        </form>
        </li>
        <hr>
    <?php endforeach; ?>
</ul>

<a href="../scripts/logout.php">Cerrar sesión</a>

</body>
</html>