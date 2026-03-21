<?php
include("db.php");

$marca = '';
$modelo = '';
$precio = '';
$descripcion = '';
$imagenes = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
} else {
    die("Error: ID no válido o no definido.");
}

$query = "SELECT * FROM Registro WHERE id=$id";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result);
    $marca = isset($row['marca']) ? $row['marca'] : ''; 
    $modelo = isset($row['modelo']) ? $row['modelo'] : '';
    $precio = isset($row['precio']) ? $row['precio'] : '0.00';
    $descripcion = isset($row['descripcion']) ? $row['descripcion'] : '';
    $imagenes = isset($row['imagenes']) && !empty($row['imagenes']) ? json_decode($row['imagenes'], true) : [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $precio = isset($_POST['precio']) ? mysqli_real_escape_string($conn, $_POST['precio']) : '0.00';
    $descripcion = isset($_POST['descripcion']) ? mysqli_real_escape_string($conn, $_POST['descripcion']) : '';
    
    $query = "SELECT imagenes FROM Registro WHERE id=$id";
    $result = mysqli_query($conn, $query);
    if ($result) {
        $row = mysqli_fetch_array($result);
        $imagenes = json_decode($row['imagenes'], true) ?? [];
    }

    if (!empty($_FILES['imagenes']['tmp_name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
                $file_name = time() . "_" . basename($_FILES['imagenes']['name'][$key]);
                $file_path = "uploads/" . $file_name;
                if (move_uploaded_file($tmp_name, $file_path)) {
                    $imagenes[] = $file_name;
                }
            }
        }
    }
    

    $imagenes_json = json_encode($imagenes);
    $query = "UPDATE Registro SET marca='$marca', modelo='$modelo', precio='$precio', descripcion='$descripcion', imagenes='$imagenes_json' WHERE id=$id";

    if (mysqli_query($conn, $query)) {
        echo "<script>
            Swal.fire({
                title: '¡Guardado!',
                text: 'Los cambios se han guardado correctamente.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'editar.php?id=$id';
            });
        </script>";
        exit();
    }
    
  
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrar_imagen']) && isset($_POST['imagen'])) {
    $imagen_a_borrar = $_POST['imagen'];

    if (file_exists("uploads/" . $imagen_a_borrar)) {
        unlink("uploads/" . $imagen_a_borrar);
    }

    $imagenes = array_diff($imagenes, [$imagen_a_borrar]);
    $imagenes_json = json_encode(array_values($imagenes));

    $query = "UPDATE Registro SET imagenes='$imagenes_json' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        echo "<script>
            Swal.fire({
                title: 'Imagen eliminada',
                text: 'La imagen ha sido eliminada correctamente.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'editar.php?id=$id';
            });
        </script>";
        exit();
    }
     else {
        die("Error al eliminar la imagen: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 20px;
        }
        .img-preview {
    width: 120px;
    height: 120px;
    object-fit: cover; 
    border-radius: 5px;
    display: block;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
}

        .img-container {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        .btn-delete {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.8);
            border: none;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Editar Registro</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <h3 class="text-center">Modificar Datos</h3>
                    <form action="editar.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
    <label class="form-label">Marca</label>
    <input type="text" name="marca" class="form-control" value="<?php echo $marca; ?>" required>
</div>
<div class="mb-3">
    <label class="form-label">Modelo</label>
    <input type="text" name="modelo" class="form-control" value="<?php echo $modelo; ?>" required>
</div>
<div class="mb-3">
    <label class="form-label">Precio</label>
    <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $precio; ?>" required>
</div>
<div class="mb-3">
    <label class="form-label">Descripción</label>
    <textarea name="descripcion" class="form-control" required><?php echo $descripcion; ?></textarea>
</div>

                        <div class="mb-3">
                            <label class="form-label">Subir Imágenes</label>
                            <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        </div>

                        <div class="mb-3">
                            <h5>Imágenes Actuales:</h5>
                            <?php foreach ($imagenes as $img) { ?>
                                <div class="img-container">
                                    <img src="uploads/<?php echo $img; ?>" class="img-preview">
                                    <form action="editar.php?id=<?php echo $id; ?>" method="POST">
                                        <input type="hidden" name="imagen" value="<?php echo $img; ?>">
                                        <button type="submit" name="borrar_imagen" class="btn-delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>

                        <button type="submit" name="actualizar" class="btn btn-success w-100">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>