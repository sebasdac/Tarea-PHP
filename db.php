<?php
$dsn = "sqlsrv:Server=localhost;Database=ControlTareas";
$user = "Daniel";
$pass = "123";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
