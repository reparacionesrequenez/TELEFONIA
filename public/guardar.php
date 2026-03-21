<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['usuario'], $_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.html');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: equipos.php');
    exit();
}

if (!hash_equals($_SESSION['token'] ?? '', $_POST['token'] ?? '')) {
    die('Error: token inválido.');
}

$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$precio = trim($_POST['precio'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');

if ($marca === '' || $modelo === '' || $precio === '' || $descripcion === '') {
    $_SESSION['message'] = 'Completa todos los campos.';
    $_SESSION['message_type'] = 'danger';
    header('Location: equipos.php');
    exit();
}

$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$imagenesGuardadas = [];
$permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!empty($_FILES['imagenes']['name'][0])) {
    foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['imagenes']['error'][$key] !== UPLOAD_ERR_OK) {
            continue;
        }

        $nombreOriginal = $_FILES['imagenes']['name'][$key];
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if (!in_array($extension, $permitidas, true)) {
            continue;
        }

        $nuevoNombre = uniqid('img_', true) . '.' . $extension;
        $rutaDestino = $uploadDir . $nuevoNombre;

        if (move_uploaded_file($tmpName, $rutaDestino)) {
            $imagenesGuardadas[] = $nuevoNombre;
        }
    }
}

$imagenesJson = json_encode($imagenesGuardadas, JSON_UNESCAPED_UNICODE);
$stmt = $conn->prepare('INSERT INTO registro (marca, modelo, precio, imagenes, descripcion) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('ssdss', $marca, $modelo, $precio, $imagenesJson, $descripcion);

if ($stmt->execute()) {
    $_SESSION['message'] = 'Registro guardado correctamente.';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Error al guardar en la base de datos.';
    $_SESSION['message_type'] = 'danger';
}

$_SESSION['token'] = bin2hex(random_bytes(32));
header('Location: equipos.php');
exit();
