<?php
session_start();
include('conecta.php');

if (isset($_POST['email']) && isset($_POST['senha'])) {

    if (strlen($_POST['email']) == 0) {
        echo "<script>alert('Preencha o campo e-mail'); history.back();</script>";
        exit;
    }

    if (strlen($_POST['senha']) == 0) {
        echo "<script>alert('Preencha o campo senha'); history.back();</script>";
        exit;
    }

    $email = $conn->real_escape_string($_POST['email']);
    $senha = $conn->real_escape_string($_POST['senha']);

    $sql = "SELECT * FROM USUARIO WHERE ds_email = '$email' AND ds_senha = '$senha'";
    $query = $conn->query($sql);

    if ($query->num_rows == 1) {

        $usuario = $query->fetch_assoc();

        $_SESSION['nome']  = $usuario['nm_usuario'];
        $_SESSION['email'] = $usuario['ds_email'];

        header("Location: main_usuario.php");
        exit;

    } else {
        echo "<script>alert('Email ou senha incorretos'); history.back();</script>";
        exit;
    }
}
?>