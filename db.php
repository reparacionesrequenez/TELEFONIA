<?php
if (session_status() === PHP_SESSION_NONE) {  
    session_start();
}

// 🔹 Detectar si estás en local o en el hosting
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    // ✅ Configuración para Local (XAMPP)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bd_reparaciones_requenez";
} else {
    // ✅ Configuración para InfinityFree
    $servername = "sql303.infinityfree.com";  
    $username = "if0_38204602";
    $password = "MwJS80w3x7m1qv";
    $dbname = "if0_38204602_bd_reparaciones_requenez";
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
