<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $tarea_id = $_POST["tarea_id"];
    $titulo = trim($_POST["titulo"]);
    $descripcion = trim($_POST["descripcion"]);
    $fecha_limite = !empty($_POST["fecha_limite"]) ? $_POST["fecha_limite"] : null;

    $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, fecha_limite = ? WHERE id = ? AND usuario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $titulo, $descripcion, $fecha_limite, $tarea_id, $usuario_id);
    $stmt->execute();
    $stmt->close();

    $_SESSION["task_success"] = "Tarea actualizada.";
}

header("Location: ../views/tareas.php");
exit;
?>
