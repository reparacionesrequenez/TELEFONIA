<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$name = $_ENV['DB_NAME'] ?? 'bd_reparaciones_requenez';

if (!in_array($_SERVER['HTTP_HOST'] ?? 'localhost', ['localhost', '127.0.0.1'], true)) {
    $host = $_ENV['DB_HOST'] ?? $host;
    $user = $_ENV['DB_USER'] ?? $user;
    $pass = $_ENV['DB_PASS'] ?? $pass;
    $name = $_ENV['DB_NAME'] ?? $name;
}

$conn = new mysqli($host, $user, $pass, $name);

if ($conn->connect_error) {
    die('Error de conexión a la base de datos: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
