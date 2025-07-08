<?php
$host = "localhost";         // Servidor (normalmente localhost en XAMPP)
$user = "root";              // Usuario de MySQL (por defecto root en XAMPP)
$password = "";              // Contraseña (vacía por defecto en XAMPP)
$database = "todo_minimal";  // Nombre de la base de datos que creaste

// Crear conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Opcional: Forzar UTF-8 (muy recomendado)
$conn->set_charset("utf8");