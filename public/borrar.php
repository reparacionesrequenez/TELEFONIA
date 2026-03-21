<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['usuario'], $_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.html');
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: equipos.php');
    exit();
}

$stmt = $conn->prepare('SELECT imagenes FROM registro WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $imagenes = json_decode($row['imagenes'] ?? '[]', true) ?: [];
    $uploadDir = __DIR__ . '/uploads/';

    foreach ($imagenes as $img) {
        $ruta = $uploadDir . basename($img);
        if (is_file($ruta)) {
            unlink($ruta);
        }
    }

    $delete = $conn->prepare('DELETE FROM registro WHERE id = ?');
    $delete->bind_param('i', $id);
    $delete->execute();
}

header('Location: equipos.php');
exit();
