<?php
session_start();
require "conecta.php";

if (!isset($_SESSION['logado']) || !isset($_SESSION['cnpj_loja'])) {
    echo "Você precisa estar logado.";
    exit;
}

if (!isset($_POST['cd_produto'])) {
    echo "ID do produto não informado.";
    exit;
}

$cd = (int)$_POST['cd_produto'];
$cnpj = $conn->real_escape_string($_SESSION['cnpj_loja']);


$sql = "SELECT p.ds_foto FROM PRODUTO p
        JOIN LOJA_PRODUTO lp ON lp.cd_produto = p.cd_produto
        WHERE p.cd_produto = $cd AND lp.cd_cnpj = '$cnpj' LIMIT 1";
$res = $conn->query($sql);
if (!$res || $res->num_rows == 0) {
    echo "Produto não encontrado ou não pertence à sua loja.";
    exit;
}
$row = $res->fetch_assoc();
$foto = $row['ds_foto'];


$sql1 = "DELETE FROM LOJA_PRODUTO WHERE cd_produto = $cd AND cd_cnpj = '$cnpj'";
$conn->query($sql1);


$sqlCheck = "SELECT COUNT(*) as cnt FROM LOJA_PRODUTO WHERE cd_produto = $cd";
$r2 = $conn->query($sqlCheck);
$cnt = 0;
if ($r2 && $r2->num_rows > 0) {
    $cnt = $r2->fetch_assoc()['cnt'];
}

if ($cnt == 0) {

    $conn->query("DELETE FROM PRODUTO WHERE cd_produto = $cd");
    
    if ($foto) {
        $path = __DIR__ . '/' . $foto;
        if (file_exists($path)) @unlink($path);
    }
}

echo "Produto excluído.";
?>
