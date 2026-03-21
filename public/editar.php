<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['usuario'], $_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header('Location: index.html');
    exit();
}

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die('ID no válido.');
}

function obtenerRegistro(mysqli $conn, int $id): ?array {
    $stmt = $conn->prepare('SELECT * FROM registro WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

function redirigirEditar(int $id): void {
    header('Location: editar.php?id=' . $id);
    exit();
}

$registro = obtenerRegistro($conn, $id);
if (!$registro) {
    die('Registro no encontrado.');
}

$permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['token'] ?? '', $_POST['token'] ?? '')) {
        die('Token inválido.');
    }

    if (isset($_POST['actualizar'])) {
        $marca = trim($_POST['marca'] ?? '');
        $modelo = trim($_POST['modelo'] ?? '');
        $precio = trim($_POST['precio'] ?? '0');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $imagenes = json_decode($registro['imagenes'] ?? '[]', true) ?: [];

        if (!empty($_FILES['imagenes']['name'][0])) {
            foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['imagenes']['error'][$key] !== UPLOAD_ERR_OK) {
                    continue;
                }

                $extension = strtolower(pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION));
                if (!in_array($extension, $permitidas, true)) {
                    continue;
                }

                $nuevoNombre = uniqid('img_', true) . '.' . $extension;
                if (move_uploaded_file($tmpName, $uploadDir . $nuevoNombre)) {
                    $imagenes[] = $nuevoNombre;
                }
            }
        }

        $imagenesJson = json_encode(array_values($imagenes), JSON_UNESCAPED_UNICODE);
        $stmt = $conn->prepare('UPDATE registro SET marca = ?, modelo = ?, precio = ?, descripcion = ?, imagenes = ? WHERE id = ?');
        $stmt->bind_param('ssdssi', $marca, $modelo, $precio, $descripcion, $imagenesJson, $id);
        $stmt->execute();
        redirigirEditar($id);
    }

    if (isset($_POST['borrar_imagen'])) {
        $imagenABorrar = basename($_POST['imagen'] ?? '');
        $imagenes = json_decode($registro['imagenes'] ?? '[]', true) ?: [];
        $imagenes = array_values(array_filter($imagenes, static fn($img) => $img !== $imagenABorrar));

        $ruta = $uploadDir . $imagenABorrar;
        if (is_file($ruta)) {
            unlink($ruta);
        }

        $imagenesJson = json_encode($imagenes, JSON_UNESCAPED_UNICODE);
        $stmt = $conn->prepare('UPDATE registro SET imagenes = ? WHERE id = ?');
        $stmt->bind_param('si', $imagenesJson, $id);
        $stmt->execute();
        redirigirEditar($id);
    }
}

$registro = obtenerRegistro($conn, $id);
$imagenes = json_decode($registro['imagenes'] ?? '[]', true) ?: [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(to right, #6a11cb, #2575fc); color: white; }
        .container { margin-top: 50px; }
        .card { background: rgba(255,255,255,.15); border-radius: 15px; padding: 20px; }
        .img-preview { width: 120px; height: 120px; object-fit: cover; border-radius: 5px; display: block; }
        .img-container { position: relative; display: inline-block; margin: 5px; }
        .btn-delete { position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,.85); border: none; color: white; border-radius: 50%; width: 28px; height: 28px; }
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
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
                    <div class="mb-3">
                        <label class="form-label">Marca</label>
                        <input type="text" name="marca" class="form-control" value="<?php echo htmlspecialchars($registro['marca']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="modelo" class="form-control" value="<?php echo htmlspecialchars($registro['modelo']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Precio</label>
                        <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo htmlspecialchars($registro['precio']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" required><?php echo htmlspecialchars($registro['descripcion']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subir imágenes</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                    </div>
                    <div class="mb-3">
                        <h5>Imágenes actuales:</h5>
                        <?php if (empty($imagenes)): ?>
                            <p class="mb-0">No hay imágenes cargadas.</p>
                        <?php else: ?>
                            <?php foreach ($imagenes as $img): ?>
                                <div class="img-container">
                                    <img src="uploads/<?php echo htmlspecialchars($img); ?>" class="img-preview" alt="Imagen del registro">
                                    <form action="editar.php?id=<?php echo $id; ?>" method="POST">
                                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
                                        <input type="hidden" name="imagen" value="<?php echo htmlspecialchars($img); ?>">
                                        <button type="submit" name="borrar_imagen" class="btn-delete">×</button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" name="actualizar" class="btn btn-success">Guardar cambios</button>
                        <a href="equipos.php" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
