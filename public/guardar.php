<?php
include("../config/db.php");

if ($_POST) {

    $data = [
        "cliente" => $_POST['cliente'],
        "equipo" => $_POST['equipo'],
        "falla" => $_POST['falla'],
        "fecha" => date("Y-m-d H:i:s")
    ];

    firebase_post("equipos", $data);

    header("Location: equipos.php");
}