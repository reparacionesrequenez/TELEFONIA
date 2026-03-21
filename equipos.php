<?php
session_start();
$es_admin = isset($_SESSION['usuario']) && $_SESSION['usuario'] == 'admin';

if ($es_admin && (!isset($_SESSION['token']) || empty($_SESSION['token']))) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Reparaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        .container {
            margin-top: 50px;
            position: relative;
            z-index: 1;
        }
        .modal-content {
    background: transparent !important;
    border: none;
    box-shadow: none;
}
.modal-backdrop {
    display: none !important; 
}

.modal-body {
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.8); 
}

.carousel-item img {
    max-width: 90vw; 
    max-height: 90vh; 
    object-fit: contain; 
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.3);
    z-index: 10;
    position: relative;
    cursor: pointer; 
}

.carousel-control-prev,
.carousel-control-next {
    width: 10%; 
    z-index: 20;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: invert(1); 
}

        .card {
            background: rgba(93, 180, 43, 0.71);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }
        .btn-custom {
            background: #ff7eb3;
            border: none;
        }
        .btn-custom:hover {
            background: #ff4f81;
        }
        table {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 10px;
        }
        th, td {
            color: white;
        }
        .img-preview {
            width: 120px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <video autoplay muted loop class="video-background">
        <source src="https://cdn.pixabay.com/video/2020/08/27/48420-453832153_large.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>
    <div class="container">
        <h2 class="text-center">Registro de Reparaciones</h2>

        <?php if ($es_admin) : ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <h3 class="text-center">Agregar Registro</h3>
                    <form action="guardar.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">
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
                            <input type="number" name="precio" class="form-control" required>
                        </div>
                        <div class="mb-3">
    <label class="form-label">Fotografías</label>
    <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
</div>

                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" rows="2" class="form-control" required></textarea>
                        </div>
                        <button type="submit" name="guardar" class="btn btn-custom w-100">Guardar</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

            <div class="col-md-<?php echo $es_admin ? '8' : '12'; ?>">
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Descripción</th>
                <th>Fecha Recibe</th>
                <?php if ($es_admin) : ?>
                <th>Opciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            include('db.php');
            $query = "SELECT * FROM registro";
            $result_tasks = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result_tasks)) {
                $imagenes = json_decode($row['imagenes'], true);
                $imagenPrincipal = isset($imagenes[0]) ? "uploads/" . $imagenes[0] : "default.jpg";
            ?>
            <tr>
                <td><?php echo $row['marca']; ?></td>
                <td><?php echo $row['modelo']; ?></td>
                <td>$<?php echo number_format($row['precio'], 2); ?></td>
                <td>
                    <img src="<?php echo $imagenPrincipal; ?>" alt="Imagen" class="img-preview" 
                         style="cursor:pointer; width: 100px; height: auto;"
                         data-bs-toggle="modal" data-bs-target="#modal<?php echo $row['id']; ?>">
                </td>
                <td><?php echo $row['descripcion']; ?></td>
                <td><?php echo $row['fecha_recibe']; ?></td>
                <?php if ($es_admin) : ?>
                    <td>

    <a href="borrar.php?id=<?php echo $row['id']?>" class="btn btn-danger btn-lg d-flex align-items-center justify-content-center mt-2">
        <i class="fas fa-trash-alt me-2"></i> Borrar
    </a>
</td>

                <?php endif; ?>
            </tr>

            <div class="modal fade" id="modal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">Imágenes de <?php echo $row['marca'] . " " . $row['modelo']; ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center">
                <div id="carousel<?php echo $row['id']; ?>" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        <?php foreach ($imagenes as $index => $img) { ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="uploads/<?php echo $img; ?>" class="img-fluid mx-auto d-block">
                            </div>
                        <?php } ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $row['id']; ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $row['id']; ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
            <?php } ?>
        </tbody>
    </table>
</div>

        </div>

        <?php if ($es_admin) : ?>
        <div class="text-center">
            <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('footer.php'); ?> 
</body>
</html>
