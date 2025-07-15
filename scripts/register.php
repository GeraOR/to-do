<?php
session_start();
include "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $contraseña_raw = $_POST["contraseña"];

    // Validar longitud mínima de la contraseña
    if (strlen($contraseña_raw) < 6) {
        $_SESSION["error"] = "La contraseña debe tener al menos 6 caracteres.";
        header("Location: ../views/registro.php");
        exit;
    }

    // Hashear la contraseña después de validarla
    $contraseña = password_hash($contraseña_raw, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, contraseña) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $email, $contraseña);

    if ($stmt->execute()) {
        $_SESSION["success"] = "Registro exitoso, inicia sesión.";
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION["error"] = "Error al registrar usuario.";
        header("Location: ../views/registro.php");
        exit;
    }
}
