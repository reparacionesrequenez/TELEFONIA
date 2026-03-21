<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = mysqli_real_escape_string($conn, $_POST['usuario']);
    $contrasena = hash('sha256', $_POST['contrasena']);

    $query = "SELECT * FROM usuarios WHERE usuario='$usuario' AND contrasena='$contrasena'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];

        if ($user['rol'] == 'admin') {
            header("Location: equipos.php"); 
        } else {
            header("Location: equipos.php");
        }
        exit();
    } else {
        echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='index.html';</script>";
    }
}
?>