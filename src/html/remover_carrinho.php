<?php
session_start();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    unset($_SESSION['carrinho'][$id]);
}

header("Location: main_usuario.php");
exit;
