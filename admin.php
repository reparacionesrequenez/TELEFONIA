<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.html");
    exit();
}
echo "<h2>Bienvenido, Admin ".$_SESSION['usuario']."</h2>";
echo "<a href='logout.php'>Cerrar sesión</a>";
?>
