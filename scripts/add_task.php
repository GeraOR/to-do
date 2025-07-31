<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit;
}

$titulo = trim($_POST["titulo"]);
$descripcion = trim($_POST["descripcion"]);
$fecha_limite = !empty($_POST["fecha_limite"]) ? $_POST["fecha_limite"] : null;
$usuario_id = $_SESSION["usuario_id"];
$importante = isset($_POST["importante"]) ? 1 : 0;

$sql = "INSERT INTO tareas (usuario_id, titulo, descripcion, fecha_limite, completada, importante)
        VALUES (?, ?, ?, ?, 0, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isssi", $usuario_id, $titulo, $descripcion, $fecha_limite, $importante);
$stmt->execute();

header("Location: ../views/tareas.php");
exit;
?>
