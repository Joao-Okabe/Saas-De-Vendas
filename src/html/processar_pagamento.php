<?php
session_start();

require "conecta.php";
date_default_timezone_set('America/Sao_Paulo');

// Verificar login
if (!isset($_SESSION['cpf'])) {
    die("Usuário não está logado.");
}

$cpf = $_SESSION['cpf'];

// Verificar carrinho
if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
    die("Carrinho vazio.");
}

// Verificar método de pagamento
if (!isset($_POST['metodo'])) {
    die("Nenhum método de pagamento selecionado.");
}

$metodo = $_POST['metodo'];

// Buscar dados do usuário
$stmtUser = $conn->prepare("SELECT nm_usuario, ds_telefone FROM USUARIO WHERE cd_cpf = ?");
$stmtUser->bind_param("s", $cpf);
$stmtUser->execute();
$resUser = $stmtUser->get_result();

if ($resUser->num_rows == 0) {
    die("Usuário não encontrado.");
}

$user = $resUser->fetch_assoc();
$nomeUser = $user['nm_usuario'];
$telefone = $user['ds_telefone'];

// Validação do método
if ($metodo === "cartao") {

    if (
        empty($_POST['numero_cartao']) ||
        empty($_POST['validade']) ||
        empty($_POST['cvv']) ||
        empty($_POST['nome_cartao'])
    ) {
        die("Preencha todos os campos do cartão.");
    }

    $statusPagamento = "APROVADO ✔";

} elseif ($metodo === "pix") {

    if (!isset($_POST['confirmar_pix'])) {
        die("Você precisa confirmar o pagamento do Pix.");
    }

    $statusPagamento = "PAGAMENTO PIX CONFIRMADO ✔";

} else {
    die("Método de pagamento inválido.");
}

// Gerar cupom
$totalGeral = 0;

$cupom  = "🛒 *Cupom de Compra*\n";
$cupom .= "👤 Cliente: $nomeUser\n";
$cupom .= "📅 Data: " . date("d/m/Y H:i") . "\n";
$cupom .= "💳 Pagamento: " . strtoupper($metodo) . "\n";
$cupom .= "📌 Status: $statusPagamento\n";
$cupom .= "-------------------------------------\n";

foreach ($_SESSION['carrinho'] as $idProd => $qtd) {

    // Buscar produto
    $stmtProd = $conn->prepare("SELECT nm_produto, vl_produto FROM PRODUTO WHERE cd_produto = ?");
    $stmtProd->bind_param("i", $idProd);
    $stmtProd->execute();
    $resProd = $stmtProd->get_result();

    if ($resProd->num_rows == 0) continue;

    $produto = $resProd->fetch_assoc();

    $nomeProd = $produto['nm_produto'];
    $valorUnit = $produto['vl_produto'];
    $subtotal = $valorUnit * $qtd;

    $totalGeral += $subtotal;

    // Inserir na tabela COMPRA
    $sqlCompra = "
        INSERT INTO COMPRA (cd_cpf, cd_produto, quantidade, dt_compra)
        VALUES (?, ?, ?, NOW())
    ";
    $stmtCompra = $conn->prepare($sqlCompra);
    $stmtCompra->bind_param("sii", $cpf, $idProd, $qtd);

    if (!$stmtCompra->execute()) {
        die("Erro ao registrar compra: " . $stmtCompra->error);
    }

    // Cupom
    $cupom .= "• $nomeProd\n";
    $cupom .= "  Quantidade: $qtd\n";
    $cupom .= "  Valor un.: R$ " . number_format($valorUnit, 2, ',', '.') . "\n";
    $cupom .= "  Subtotal: R$ " . number_format($subtotal, 2, ',', '.') . "\n";
    $cupom .= "-------------------------------------\n";
}

$cupom .= "TOTAL: *R$ " . number_format($totalGeral, 2, ',', '.') . "*\n";
$cupom .= "Obrigado pela compra no DeMarket! 😄";

// Registrar pagamento
$stmtPay = $conn->prepare("
    INSERT INTO pagamento (cd_cpf, valor, dt_pagamento)
    VALUES (?, ?, ?)
");

$dataPagamento = date("Y-m-d H:i:s");
$stmtPay->bind_param("sds", $cpf, $totalGeral, $dataPagamento);

if (!$stmtPay->execute()) {
    die("Erro ao registrar pagamento: " . $stmtPay->error);
}

// Envio do cupom via WhatsApp
$apiURL = "https://api.z-api.io/instances/3EB7924D78C7D092823B46ED925AFEF2/token/3B473E5C5943A1C4B825FC4C/send-text";

$payload = json_encode([
    "phone" => $telefone,
    "message" => $cupom
]);

$ch = curl_init($apiURL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "client-token: F332ee38a876741269c334e4cfc0aa7dbS"
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
curl_close($ch);

// Limpar carrinho
unset($_SESSION['carrinho']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Pagamento Concluído</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f3f3f3;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 450px;
    margin: 40px auto;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 0 12px rgba(0,0,0,0.15);
    text-align: center;
}
.sucesso {
    font-size: 80px;
    color: #2ecc71;
}
h1 {
    margin-top: 0;
    font-size: 28px;
}
.detalhes {
    background: #f8f8f8;
    padding: 15px;
    border-radius: 10px;
    text-align: left;
    margin-top: 15px;
    font-size: 16px;
}
.btn {
    margin-top: 25px;
    display: inline-block;
    padding: 12px 20px;
    background: #2ecc71;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 18px;
    transition: 0.2s;
}
.btn:hover {
    background: #27ae60;
}
</style>
</head>
<body>

<div class="container">
    <div class="sucesso">✔</div>
    <h1>Pagamento Concluído!</h1>
    <p>Seu pedido foi registrado e o cupom já foi enviado para seu WhatsApp.</p>

    <div class="detalhes">
        <p><b>Método de pagamento:</b> <?= strtoupper($metodo) ?></p>
        <p><b>Status:</b> <?= $statusPagamento ?></p>
        <p><b>Total pago:</b> R$ <?= number_format($totalGeral, 2, ',', '.') ?></p>
        <p><b>Data:</b> <?= date("d/m/Y H:i") ?></p>
    </div>

    <a href="main_usuario.php" class="btn">Voltar para o início</a>
</div>

</body>
</html>
