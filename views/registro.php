<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <h2>Registro</h2>
<form method="POST" action="../scripts/register.php">
    <label>Nombre:</label>
    <input type="text" name="nombre" required>
    <label>Email:</label>
    <input type="email" name="email" required>
    <label>Contraseña:</label>
    <input type="password" name="contraseña" required minlength="6">
    <button type="submit">Registrarse</button>
</form>
<a href="../index.php">Ya tienes cuenta</a>
</body>
</html>