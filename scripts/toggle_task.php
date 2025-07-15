<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tarea_id"])) {
    $tarea_id = $_POST["tarea_id"];
    $usuario_id = $_SESSION["usuario_id"];

    // Consultar estado actual
    $sql = "SELECT completada FROM tareas WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $tarea_id, $usuario_id);
    $stmt->execute();
    $stmt->bind_result($completada);
    $stmt->fetch();
    $stmt->close();

    if ($completada !== null) {
        $nuevo_estado = $completada ? 0 : 1;  // Alternar estado

        $sql = "UPDATE tareas SET completada = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $nuevo_estado, $tarea_id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION["task_success"] = $nuevo_estado ? "¡Tarea marcada como completada!" : "Tarea desmarcada correctamente.";
    } else {
        $_SESSION["task_error"] = "Tarea no encontrada.";
    }
}

header("Location: ../views/tareas.php");
exit;
?>