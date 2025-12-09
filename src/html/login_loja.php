<?php
session_start();
include "conecta.php"; 

$cnpj  = $_POST["cnpj_loja"];
$email = $_POST["email_loja"];
$senha = $_POST["senha_loja"];

$sql = "SELECT cd_cnpj, nm_loja FROM LOJA 
        WHERE cd_cnpj = ? AND ds_email = ? AND ds_senha = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $cnpj, $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows == 1) {
    $dados = $result->fetch_assoc();

    $_SESSION["logado"] = true;
    $_SESSION["cnpj_loja"] = $dados["cd_cnpj"];
    $_SESSION["nome_loja"] = $dados["nm_loja"];

    echo "OK";
} else {
    echo "Dados incorretos! Verifique CNPJ, e-mail e senha.";
}
?>
