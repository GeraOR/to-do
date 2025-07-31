<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Obtener las tareas del usuario
$estado = isset($_GET["estado"]) ? $_GET["estado"] : "pendiente";

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
    <link rel="stylesheet" href="../styles/tareas.css?v=1.5">
</head>
<body>
    <?php if (isset($_SESSION["task_success"])): ?>
    <p style="color: green;"><?php echo $_SESSION["task_success"]; ?></p>
    <?php unset($_SESSION["task_success"]); ?>
<?php endif; ?>

    <header class="app-header">
  <div class="logo">
      <img src="../icons/lista-de-verificacion.png" alt="Logo" />
      <span>TO DO</span>
  </div>
      <a href="../scripts/logout.php" class="logout-btn" title="Cerrar sesión">
          <img src="../icons/cerrar-sesion.png" alt="Cerrar sesión" />
      </a>
</header>


    <h2>Tus Tareas</h2>

<!-- Modal para nueva tarea -->
<div id="modalNuevaTarea" class="modal">
    <div style="background:white; padding:20px; max-width:400px; margin:100px auto; border-radius:10px; position:relative;">
        <button onclick="cerrarFormulario()" style="position:absolute; top:10px; right:10px;">&times;</button>
        <h3>Agregar Tarea</h3>
        <form method="POST" action="../scripts/add_task.php">
            <label>Título:</label>
            <input type="text" name="titulo" required>
            <label>Descripción:</label>
            <textarea name="descripcion" required></textarea>
            <label>Fecha límite:</label>
            <input type="date" name="fecha_limite">
            <button type="submit">Agregar</button>
        </form>
    </div>
</div>

<?php if (isset($_SESSION["task_success"])): ?>
    <p style="color: green;"><?php echo $_SESSION["task_success"]; ?></p>
    <?php unset($_SESSION["task_success"]); ?>
<?php endif; ?>
<div style="display: flex; justify-content: flex-end; margin: 10px 20px;">
<form method="GET" action="" class="filtro-form-barra">
          <select name="estado" onchange="this.form.submit()">
              <option value="pendiente" <?= (isset($_GET['estado']) && $_GET['estado'] === 'pendiente') ? 'selected' : '' ?>>Pendientes</option>
<option value="completada" <?= ($estado === 'completada') ? 'selected' : '' ?>>Completadas</option>
          </select>
      </form>
      </div>
<ul>
    <?php if (empty($tareas)): ?>
    <p style="text-align:center; color:#777;">No hay tareas para mostrar.</p>
<?php endif; ?>
    <?php foreach ($tareas as $tarea): ?>
        <li>
    <strong><?= htmlspecialchars($tarea["titulo"]); ?></strong>
    <p><?= htmlspecialchars($tarea["descripcion"]); ?></p>
    <?php if ($tarea["fecha_limite"]): ?>
        <small>Fecha límite: <?= $tarea["fecha_limite"]; ?></small>
    <?php endif; ?>
    <div class="menu-container">
        <button class="menu-toggle">⋮</button>
        <div class="menu-dropdown">
    <form method="POST" action="../scripts/toggle_task.php" style="display:inline;">
        <input type="hidden" name="tarea_id" value="<?= $tarea["id"]; ?>">
        <button type="submit">
            <?= $tarea["completada"] ? "Desmarcar" : "Completar"; ?>
        </button>
    </form>

    <form method="POST" action="../scripts/delete_task.php" style="display:inline;">
        <input type="hidden" name="tarea_id" value="<?= $tarea["id"]; ?>">
        <button onclick="return confirm('¿Eliminar esta tarea?')">Eliminar</button>
    </form>

        <?php
$titulo_esc = htmlspecialchars(addslashes($tarea['titulo']));
$descripcion_esc = htmlspecialchars(addslashes(str_replace(["\r", "\n"], ['\r', '\n'], $tarea['descripcion'])));
?>
<button type="button" onclick="abrirModal(<?= $tarea['id']; ?>, '<?= $titulo_esc; ?>', '<?= $descripcion_esc; ?>', '<?= $tarea['fecha_limite']; ?>')">
    Editar</button>
    </div>
    </div>
        </li>
        <hr>
    <?php endforeach; ?>
</ul>

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
    
    // Reemplazar los caracteres "\r" y "\n" literales por saltos de línea reales
    descripcion = descripcion.replace(/\\r\\n|\\n|\\r/g, "\n");
    
    document.getElementById('modalDescripcion').value = descripcion;
    document.getElementById('modalFecha').value = fecha;
    document.getElementById('modalEditar').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalEditar').style.display = 'none';
}
</script>
<script>
function mostrarFormulario() {
    document.getElementById('modalNuevaTarea').style.display = 'block';
}

function cerrarFormulario() {
    document.getElementById('modalNuevaTarea').style.display = 'none';
}
</script>
<script>
document.querySelectorAll('.menu-toggle').forEach(button => {
    button.addEventListener('click', e => {
        e.stopPropagation(); // No propaga el clic
        const container = button.parentElement;
        // Cierra otros menús
        document.querySelectorAll('.menu-container').forEach(el => {
            if (el !== container) el.classList.remove('active');
        });
        // Alterna visibilidad del menú actual
        container.classList.toggle('active');
    });
});

// Cerrar menú al hacer clic fuera
document.addEventListener('click', () => {
    document.querySelectorAll('.menu-container').forEach(el => {
        el.classList.remove('active');
    });
});
</script>

<!-- Botón flotante -->
<button onclick="mostrarFormulario()" class="boton-flotante">+</button>

</body>
</html>