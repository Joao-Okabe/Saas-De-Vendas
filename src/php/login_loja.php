<?php
session_start();
include "conecta.php"; 

$cnpj  = $_POST["cnpj_loja"];
$email = $_POST["email_loja"];
$senha = $_POST["senha_loja"];

$sql = "SELECT * FROM LOJA WHERE cd_cnpj = '$cnpj' AND ds_email = '$email'  AND ds_senha = '$senha'";
$result = $conn->query($sql);


if ($result && $result->num_rows == 1) {
    $dados = $result->fetch_assoc();

    $_SESSION["logado"] = true;
    $_SESSION["usuario"] = $dados["nm_loja"];

    echo "OK";
} else {
    echo "Dados incorretos! Verifique CNPJ, e-mail e senha.";
}
?>
