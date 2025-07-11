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

$sql = "INSERT INTO tareas (usuario_id, titulo, descripcion, fecha_limite) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $usuario_id, $titulo, $descripcion, $fecha_limite);
$stmt->execute();

header("Location: ../views/tareas.php");
exit;
?>
