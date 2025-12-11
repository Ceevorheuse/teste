<?php
$host = 'localhost';
$db   = 'librefunny';
$user = 'root';
$pass = ''; // No XAMPP a senha padrão costuma ser vazia

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>