<?php
session_start();
include "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"]);
    $email = trim($_POST["email"]);
    $contraseña = password_hash($_POST["contraseña"], PASSWORD_DEFAULT);

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