<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'conecta.php';

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$cpf = $_POST['cpf']  ;
$usuario = $_POST['usuario']   ;
$email = $_POST['email']  ;
$senha = $_POST['senha']  ;
$telefone = $_POST['telefone']  ;
$nascimento = $_POST['nascimento'] ;

if ($cpf === '' || $usuario === '' || $email === '') {
    die("Campos obrigatórios não enviados. Dados recebidos: " . json_encode($_POST));
}

$cpf = preg_replace('/\D/', '', $cpf); // remove . e -

$stmt = $conn->prepare("INSERT INTO USUARIO (cd_cpf, nm_usuario, ds_email, ds_senha, ds_telefone, dt_nascimento)
                        VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Erro ao preparar SQL: " . $conn->error);
}

$stmt->bind_param("isssss", $cpf, $usuario, $email, $senha, $telefone, $nascimento);

if ($stmt->execute()) {
    echo "Cadastro inserido com sucesso!";
} else {
    echo "Erro MySQL: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>