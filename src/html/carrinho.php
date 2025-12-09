<?php
session_start();
require "conecta.php";
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho</title>
</head>
<body>

<h1>Meu Carrinho</h1>

<?php
if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
    echo "<p>Seu carrinho está vazio.</p>";
    echo "<a href='telaInicial.php'>Voltar para produtos</a>";
    exit;
}

echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Produto</th><th>Quantidade</th><th>Preço</th><th>Total</th><th>Ações</th></tr>";

$total_geral = 0;

foreach ($_SESSION['carrinho'] as $id => $qtd) {
    $sql = "SELECT nm_produto, vl_produto FROM PRODUTO WHERE cd_produto = $id";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();

    $nome = $row['nm_produto'];
    $preco = $row['vl_produto'];
    $total = $preco * $qtd;

    $total_geral += $total;

    echo "<tr>";
    echo "<td>$nome</td>";
    echo "<td>$qtd</td>";
    echo "<td>R$ " . number_format($preco, 2, ',', '.') . "</td>";
    echo "<td>R$ " . number_format($total, 2, ',', '.') . "</td>";
    echo "<td><a href='remover_carrinho.php?id=$id'>Remover</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Total Geral: R$ " . number_format($total_geral, 2, ',', '.') . "</h2>";

echo "<a href='main_usuario.php'>Continuar Comprando</a><br><br>";
echo "<button onclick=\"alert('Compra finalizada!')\">Finalizar Compra</button>";

?>
</body>
</html>