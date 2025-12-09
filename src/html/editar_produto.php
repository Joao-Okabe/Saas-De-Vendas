<?php
session_start();
require "conecta.php";

header("Content-Type: application/json");

if (!isset($_SESSION["logado"]) || !isset($_SESSION["cnpj_loja"])) {
    echo json_encode(["erro" => true, "msg" => "Você precisa estar logado."]);
    exit;
}

$cnpj = $conn->real_escape_string($_SESSION["cnpj_loja"]);

if (!isset($_POST["cd_produto"])) {
    echo json_encode(["erro" => true, "msg" => "Produto não informado."]);
    exit;
}

$cd = (int) $_POST["cd_produto"];

$nome      = $conn->real_escape_string($_POST["nm_produto"] ?? "");
$descricao = $conn->real_escape_string($_POST["ds_produto"] ?? "");
$preco     = $conn->real_escape_string($_POST["vl_produto"] ?? "");
$tipo      = $conn->real_escape_string($_POST["ds_tipo"] ?? "");

$sql = "SELECT p.ds_foto FROM PRODUTO p 
        JOIN LOJA_PRODUTO lp ON lp.cd_produto = p.cd_produto
        WHERE p.cd_produto = $cd AND lp.cd_cnpj = '$cnpj'
        LIMIT 1";
$res = $conn->query($sql);

if (!$res || $res->num_rows == 0) {
    echo json_encode(["erro" => true, "msg" => "Produto não encontrado."]);
    exit;
}

$dados = $res->fetch_assoc();
$fotoAtual = $dados["ds_foto"];

$fotoNova = $fotoAtual;

if (isset($_FILES["ds_foto"]) && $_FILES["ds_foto"]["error"] !== UPLOAD_ERR_NO_FILE) {

    $img = $_FILES["ds_foto"];

    if ($img["error"] !== UPLOAD_ERR_OK) {
        echo json_encode(["erro" => true, "msg" => "Erro no upload da imagem."]);
        exit;
    }

    $ext = strtolower(pathinfo($img["name"], PATHINFO_EXTENSION));
    $permitidas = ["jpg","jpeg","png","gif"];

    if (!in_array($ext, $permitidas)) {
        echo json_encode(["erro" => true, "msg" => "Formato inválido de imagem."]);
        exit;
    }

    $uploadDir = __DIR__ . "/uploads";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $novoNome = uniqid("p_") . "." . $ext;
    $destino = $uploadDir . "/" . $novoNome;

    if (!move_uploaded_file($img["tmp_name"], $destino)) {
        echo json_encode(["erro" => true, "msg" => "Falha ao salvar imagem."]);
        exit;
    }

    // deletar imagem antiga se existir
    if ($fotoAtual && file_exists(__DIR__ . "/" . $fotoAtual)) {
        unlink(__DIR__ . "/" . $fotoAtual);
    }

    $fotoNova = "uploads/" . $novoNome;
}

$sqlUp = "UPDATE PRODUTO SET 
            nm_produto = '$nome',
            ds_produto = '$descricao',
            vl_produto = '$preco',
            ds_foto = " . ($fotoNova ? "'" . $conn->real_escape_string($fotoNova) . "'" : "NULL") . ",
            ds_tipo = '$tipo'
         WHERE cd_produto = $cd
         LIMIT 1";

if (!$conn->query($sqlUp)) {
    echo json_encode(["erro" => true, "msg" => "Erro ao atualizar produto."]);
    exit;
}

echo json_encode(["erro" => false, "msg" => "Produto atualizado com sucesso!"]);
