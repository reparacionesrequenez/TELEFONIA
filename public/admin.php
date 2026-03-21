<?php
session_start();
if (!isset($_SESSION['usuario'], $_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
    <div class="container py-5 text-center">
        <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
        <p>Has iniciado sesión como administrador.</p>
        <a class="btn btn-primary me-2" href="equipos.php">Ir al panel de equipos</a>
        <a class="btn btn-danger" href="logout.php">Cerrar sesión</a>
    </div>
</body>
</html>
