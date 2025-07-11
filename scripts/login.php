<?php
session_start();
include "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $contraseña = $_POST["contraseña"];

    $sql = "SELECT id, nombre, contraseña FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($usuario = $result->fetch_assoc()) {
        if (password_verify($contraseña, $usuario["contraseña"])) {
            $_SESSION["usuario_id"] = $usuario["id"];
            $_SESSION["usuario_nombre"] = $usuario["nombre"];

            header("Location: ../views/tareas.php");
            exit;
        }
    }

    // Si no pasa login, redirige con error:
    $_SESSION["error"] = "Credenciales incorrectas.";
    header("Location: ../index.php");
    exit;
}