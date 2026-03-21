<?php
session_start();
include('db.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT imagenes FROM registro WHERE id = $id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $imagenes = json_decode($row['imagenes'], true);

        if (!empty($imagenes)) {
            foreach ($imagenes as $img) {
                $rutaImagen = "uploads/" . $img;
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
            }
        }

        $queryDelete = "DELETE FROM registro WHERE id = $id";
        mysqli_query($conn, $queryDelete);
    }
}

header("Location: equipos.php");
exit();
?>
