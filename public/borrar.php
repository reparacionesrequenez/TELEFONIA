<?php
include("../config/db.php");

$id = $_GET['id'];

firebase_delete("equipos/" . $id);

header("Location: equipos.php");