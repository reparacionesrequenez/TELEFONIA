<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['usuario'], $_SESSION['rol'])) {
    header('Location: index.html');
    exit();
}

$esAdmin = $_SESSION['rol'] === 'admin';
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

$result = $conn->query('SELECT * FROM registro ORDER BY fecha_recibe DESC, id DESC');
$registros = [];
while ($result && $row = $result->fetch_assoc()) {
    $registros[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Reparaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(to right, #6a11cb, #2575fc); color: white; position: relative; overflow-x: hidden; }
        .video-background { position: fixed; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2; }
        .overlay { position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: -1; }
        .container { margin-top: 40px; position: relative; z-index: 1; }
        .card { background: rgba(93, 180, 43, 0.71); backdrop-filter: blur(10px); border-radius: 15px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,.2); }
        .btn-custom { background: #ff7eb3; border: none; color: white; }
        .btn-custom:hover { background: #ff4f81; color: white; }
        table { background: rgba(255,255,255,.15); backdrop-filter: blur(5px); }
        th, td { color: white !important; vertical-align: middle; }
        .img-preview { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer; }
        .modal-content { background: transparent; border: none; box-shadow: none; }
        .modal-body { background: rgba(0,0,0,.8); }
        .carousel-item img { max-width: 90vw; max-height: 80vh; object-fit: contain; border-radius: 10px; }
    </style>
</head>
<body>
    <video autoplay muted loop class="video-background">
        <source src="https://cdn.pixabay.com/video/2020/08/27/48420-453832153_large.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>

    <div class="container pb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h2 class="mb-1">Registro de Reparaciones</h2>
                <p class="mb-0">Usuario: <?php echo htmlspecialchars($_SESSION['usuario']); ?> | Rol: <?php echo htmlspecialchars($_SESSION['rol']); ?></p>
            </div>
            <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
        </div>

        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'] ?? 'info'); ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <?php if ($esAdmin): ?>
            <div class="col-lg-4">
                <div class="card">
                    <h3 class="text-center">Agregar registro</h3>
                    <form action="guardar.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']); ?>">
                        <div class="mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" step="0.01" name="precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fotografías</label>
                            <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" rows="3" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-custom w-100">Guardar</button>
                    </form>
                </div>
            </div>
            <?php endif; ?>

            <div class="col-lg-<?php echo $esAdmin ? '8' : '12'; ?>">
                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            <tr>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Precio</th>
                                <th>Imagen</th>
                                <th>Descripción</th>
                                <th>Fecha recibe</th>
                                <?php if ($esAdmin): ?><th>Opciones</th><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($registros)): ?>
                            <tr>
                                <td colspan="<?php echo $esAdmin ? '7' : '6'; ?>">No hay registros todavía.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registros as $row): ?>
                                <?php $imagenes = json_decode($row['imagenes'] ?? '[]', true) ?: []; ?>
                                <?php $imagenPrincipal = !empty($imagenes) ? 'uploads/' . rawurlencode($imagenes[0]) : ''; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['marca']); ?></td>
                                    <td><?php echo htmlspecialchars($row['modelo']); ?></td>
                                    <td>$<?php echo number_format((float) $row['precio'], 2); ?></td>
                                    <td>
                                        <?php if ($imagenPrincipal !== ''): ?>
                                            <img src="<?php echo $imagenPrincipal; ?>" alt="Imagen" class="img-preview" data-bs-toggle="modal" data-bs-target="#modal<?php echo (int) $row['id']; ?>">
                                        <?php else: ?>
                                            <span>Sin imagen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo nl2br(htmlspecialchars($row['descripcion'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['fecha_recibe']); ?></td>
                                    <?php if ($esAdmin): ?>
                                        <td>
                                            <a href="editar.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-warning btn-sm mb-2">Editar</a>
                                            <a href="borrar.php?id=<?php echo (int) $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas borrar este registro?');">Borrar</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>

                                <?php if (!empty($imagenes)): ?>
                                <div class="modal fade" id="modal<?php echo (int) $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
                                        <div class="modal-content">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title text-white"><?php echo htmlspecialchars($row['marca'] . ' ' . $row['modelo']); ?></h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body d-flex align-items-center justify-content-center">
                                                <div id="carousel<?php echo (int) $row['id']; ?>" class="carousel slide" data-bs-ride="false">
                                                    <div class="carousel-inner">
                                                        <?php foreach ($imagenes as $index => $img): ?>
                                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                                <img src="uploads/<?php echo rawurlencode($img); ?>" class="img-fluid mx-auto d-block" alt="Imagen del equipo">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <?php if (count($imagenes) > 1): ?>
                                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo (int) $row['id']; ?>" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo (int) $row['id']; ?>" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
