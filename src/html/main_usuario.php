<?php
session_start();
require "conecta.php";

$sql = "SELECT p.cd_produto, p.nm_produto, p.ds_produto, p.vl_produto, p.ds_foto
        FROM PRODUTO p
        ORDER BY p.cd_produto DESC";


$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Meus Produtos</title>
</head>
<body>
<h1>Meus Produtos</h1>

<table border="1" cellpadding="8" cellspacing="0">
<tr><th>ID</th><th>Foto</th><th>Nome</th><th>Preço</th><th>Descrição</th><th>Ações</th></tr>
<?php
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $img = $row['ds_foto'] ? htmlspecialchars($row['ds_foto']) : '';
        echo "<tr>";
        echo "<td>{$row['cd_produto']}</td>";
        echo "<td>";
        if ($img) {
            echo "<img src='".htmlspecialchars($img)."' alt='' style='max-width:100px; max-height:80px;'>";
        } else {
            echo "—";
        }
        echo "</td>";
        echo "<td>".htmlspecialchars($row['nm_produto'])."</td>";
        echo "<td>R$ ".number_format($row['vl_produto'], 2, ',', '.')."</td>";
        echo "<td>".nl2br(htmlspecialchars($row['ds_produto']))."</td>";
        echo "<td><a href='add_carrinho.php?id={$row['cd_produto']}'>Adicionar ao Carrinho</a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>Nenhum produto cadastrado.</td></tr>";
}
?>
</table>
<br>
<a href="carrinho.php">Carrinho</a>
<script src="jquery-3.7.1.min.js"></script>
</body>
</html>
