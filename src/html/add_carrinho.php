<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: telaInicial.php");
    exit;
}

$id = (int) $_GET['id'];

// Se o carrinho ainda não existe, cria:
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Se o produto já existe no carrinho, aumenta a quantidade
if (isset($_SESSION['carrinho'][$id])) {
    $_SESSION['carrinho'][$id]++;
} else {
    $_SESSION['carrinho'][$id] = 1;
}

header("Location: main_usuario.php");
exit;