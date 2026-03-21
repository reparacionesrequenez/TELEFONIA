<?php
include("../config/db.php");

$id = $_GET['id'];

if ($_POST) {

    $data = [
        "cliente" => $_POST['cliente'],
        "equipo" => $_POST['equipo'],
        "falla" => $_POST['falla']
    ];

    firebase_put("equipos/" . $id, $data);

    header("Location: equipos.php");
}

$equipo = firebase_get("equipos/" . $id);
?>

<form method="POST">
    <input type="text" name="cliente" value="<?php echo $equipo['cliente']; ?>">
    <input type="text" name="equipo" value="<?php echo $equipo['equipo']; ?>">
    <input type="text" name="falla" value="<?php echo $equipo['falla']; ?>">
    <button>Actualizar</button>
</form>