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

        // Actualizar estado
        $sql = "UPDATE tareas SET completada = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $nuevo_estado, $tarea_id, $usuario_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION["task_success"] = $nuevo_estado ? "¡Tarea marcada como completada!" : "Tarea desmarcada correctamente.";

        // Si la tarea se acaba de completar y es recurrente, clonar con nueva fecha
        if ($nuevo_estado === 1) {
            // Obtener toda la tarea
            $sql = "SELECT * FROM tareas WHERE id = ? AND usuario_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $tarea_id, $usuario_id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $tarea = $resultado->fetch_assoc();
            $stmt->close();

            if ($tarea && $tarea["repeticion"] !== "nunca") {
                $fecha_actual = $tarea["fecha_limite"];
                $nueva_fecha = $fecha_actual;

                // Calcular nueva fecha según tipo de repetición
                switch ($tarea["repeticion"]) {
                    case "diaria":
                        $nueva_fecha = date("Y-m-d", strtotime("+1 day", strtotime($fecha_actual)));
                        break;
                    case "semanal":
                        $nueva_fecha = date("Y-m-d", strtotime("+1 week", strtotime($fecha_actual)));
                        break;
                    case "mensual":
                        $nueva_fecha = date("Y-m-d", strtotime("+1 month", strtotime($fecha_actual)));
                        break;
                }

                // Insertar nueva tarea
                $sql = "INSERT INTO tareas (usuario_id, titulo, descripcion, fecha_limite, completada, importante, repeticion)
                        VALUES (?, ?, ?, ?, 0, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "isssis",
                    $usuario_id,
                    $tarea["titulo"],
                    $tarea["descripcion"],
                    $nueva_fecha,
                    $tarea["importante"],
                    $tarea["repeticion"]
                );
                $stmt->execute();
                $stmt->close();
            }
        }

    } else {
        $_SESSION["task_error"] = "Tarea no encontrada.";
    }
}

header("Location: ../views/tareas.php");
exit;
?>
