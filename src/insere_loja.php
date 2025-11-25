<?php
include 'conecta.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome_loja"];
    $senha_loja = $_POST["senha_loja"];
    $cnpj = $_POST["cod_cnpj"];
    $telefone = $_POST["telefone_loja"];
    $email = $_POST["email_loja"];
    $sg_estado = strtoupper(trim($_POST["estado"]));
    $nm_cidade = ucfirst(trim($_POST["cidade"]));
    $nm_rua = ucfirst(trim($_POST["rua"]));
    $categoria = ucfirst(trim($_POST["categoria"]));
    $formato = ucfirst(trim($_POST["formato"]));

    // Estado
    $sql = "SELECT cd_estado FROM ESTADO WHERE sg_estado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sg_estado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cd_estado = $result->fetch_assoc()["cd_estado"];
    } else {
        $sql = "INSERT INTO ESTADO (sg_estado) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sg_estado);
        $stmt->execute();
        $cd_estado = $stmt->insert_id;
    }

    // Cidade
    $sql = "SELECT cd_cidade FROM CIDADE WHERE nm_cidade = ? AND cd_estado = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nm_cidade, $cd_estado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cd_cidade = $result->fetch_assoc()["cd_cidade"];
    } else {
        $sql = "INSERT INTO CIDADE (nm_cidade, cd_estado) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nm_cidade, $cd_estado);
        $stmt->execute();
        $cd_cidade = $stmt->insert_id;
    }

    // Rua
    $sql = "SELECT cd_rua FROM RUA WHERE nm_rua = ? AND cd_cidade = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nm_rua, $cd_cidade);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cd_rua = $result->fetch_assoc()["cd_rua"];
    } else {
        $sql = "INSERT INTO RUA (nm_rua, cd_cidade) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nm_rua, $cd_cidade);
        $stmt->execute();
        $cd_rua = $stmt->insert_id;
    }

    // Loja
    $sql = "INSERT INTO LOJA (cd_cnpj, ds_senha, nm_loja, ds_telefone, ds_email, cd_estado, cd_cidade, cd_rua, ds_categoria, ds_formato)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiiss", $cnpj, $senha_loja, $nome, $telefone, $email, $cd_estado, $cd_cidade, $cd_rua, $categoria, $formato);


    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Erro ao cadastrar loja: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}