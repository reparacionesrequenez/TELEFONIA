<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['token']) || $_POST['token'] !== $_SESSION['token']) {
        die("Error: Token inválido.");
    }

    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $precio = mysqli_real_escape_string($conn, $_POST['precio']);
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);

    $imagenesGuardadas = [];

    if (!empty($_FILES['imagenes']['name'][0])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmpName) {
            $nombreOriginal = $_FILES['imagenes']['name'][$key];
            $ext = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
            $nuevoNombre = uniqid("img_") . "." . $ext;
            $rutaDestino = $uploadDir . $nuevoNombre;

            if (move_uploaded_file($tmpName, $rutaDestino)) {
                $imagenesGuardadas[] = $nuevoNombre;
            }
        }
    }

    $imagenesJson = json_encode($imagenesGuardadas);
    $query = "INSERT INTO registro (marca, modelo, precio, imagenes, descripcion) VALUES ('$marca', '$modelo', '$precio', '$imagenesJson', '$descripcion')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Guardado correctamente';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Error al guardar en la base de datos.';
        $_SESSION['message_type'] = 'danger';
    }

    unset($_SESSION['token']);
    header("Location: equipos.php");
    exit();
}
?>
