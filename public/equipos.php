<?php
include("../config/db.php");

$equipos = firebase_get("equipos");
?>

<h1>Equipos</h1>

<?php if ($equipos): ?>
    <?php foreach ($equipos as $id => $equipo): ?>
        <div>
            <strong><?php echo $equipo['cliente']; ?></strong> -
            <?php echo $equipo['equipo']; ?> -
            <?php echo $equipo['falla']; ?>

            <a href="editar.php?id=<?php echo $id; ?>">Editar</a>
            <a href="borrar.php?id=<?php echo $id; ?>">Borrar</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No hay registros</p>
<?php endif; ?>