<?php
session_start();
include "../includes/db.php";

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../index.php");
    exit;
}

$tarea_id = $_POST["tarea_id"];
$usuario_id = $_SESSION["usuario_id"];

$sql = "UPDATE tareas SET completada = 1 WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $tarea_id, $usuario_id);
$stmt->execute();

header("Location: ../views/tareas.php");
exit;
?>
