<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <?php
session_start();
if (isset($_SESSION["error"])) {
    echo "<p style='color:red'>{$_SESSION["error"]}</p>";
    unset($_SESSION["error"]);
}
?>
<form method="POST" action="scripts/login.php">
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Contraseña:</label>
    <input type="password" name="contraseña" required>
    <button type="submit">Ingresar</button>
</form>
<a href="views/registro.php">Crear cuenta nueva</a>
</body>
</html>