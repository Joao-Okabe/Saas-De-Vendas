<?php
$servername = "db";
$username = "root";
$password = "root";
$dbname = "db_saas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>
