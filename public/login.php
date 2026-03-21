<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (isset($_SESSION['usuario'], $_SESSION['rol'])) {
    header('Location: equipos.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $contrasena = hash('sha256', $_POST['contrasena'] ?? '');

    if ($usuario === '' || $contrasena === hash('sha256', '')) {
        echo "<script>alert('Completa usuario y contraseña'); window.location.href='index.html';</script>";
        exit();
    }

    $stmt = $conn->prepare('SELECT usuario, rol FROM usuarios WHERE usuario = ? AND contrasena = ? LIMIT 1');
    $stmt->bind_param('ss', $usuario, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['token'] = bin2hex(random_bytes(32));
        header('Location: equipos.php');
        exit();
    }

    echo "<script>alert('Usuario o contraseña incorrectos'); window.location.href='index.html';</script>";
    exit();
}

header('Location: index.html');
exit();
