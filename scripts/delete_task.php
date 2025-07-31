<?php
session_start();
include "../includes/db.php";

$tarea_id = $_POST["tarea_id"];
$sql = "DELETE FROM tareas WHERE id = ? AND usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $tarea_id, $_SESSION["usuario_id"]);
$stmt->execute();
$stmt->close();

$_SESSION["task_success"] = "Tarea eliminada exitosamente."; // <- Mensaje
header("Location: ../views/tareas.php");
exit;
?>
