<?php
session_start();
require "conecta.php";

$sql = "SELECT p.cd_produto, p.nm_produto, p.ds_produto, p.vl_produto, p.ds_foto
        FROM PRODUTO p
        ORDER BY p.cd_produto DESC";

$res = $conn->query($sql);

function ListarProdutoCliente($res){
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
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Deméter - Lista de Produtos</title>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/main-loja.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <h1 class="text-white">Deméter</h1>
        <div class="navbar-btn-group">
            <button class="btn-premium" onclick="Sair()">
                    <span class="agrandir-light">Sair</span>
                    <span>Sair</span>
            </button>
        </div>
    </nav>
    <!-- Lista -->
    <div class="row offset-2 mt-5">
        <h1 class="agrandir-bold">Lista de Produtos</h1>
    </div>
    <div class="row offset-2 col-8">
        <table class="table table-striped table-hover shadow-sm">
            <thead class="table-primary"> 
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                listarProdutoCliente($res);
                ?>
            </tbody>
        </table>
    </div>
    <div class="row offset-2 mt-5">
        <h1 class="agrandir-bold">Carrinho</h1>
    </div>
    <div class="offset-2 col-8">
    <?php
        if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
            echo "<p>Seu carrinho está vazio.</p>";
            echo "<a href='telaInicial.php'>Voltar para produtos</a>";
            exit;
        }

        echo '<table class="table table-striped table-hover shadow-sm">
                <thead class="table-primary"> 
                    <tr>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';

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
            echo "</tr></tbody>";
        }
        echo "</table>";
        echo "<h2>Total Geral: R$ " . number_format($total_geral, 2, ',', '.') . "</h2>";
        echo "<button onclick=\"alert('Compra finalizada!')\">Finalizar Compra</button>";
    ?>
    </div>
    
    <div class="mt-5"></div>
    
    <script src="jquery-3.7.1.min.js"></script>
    <script>
        function Sair(){
            window.location.href =  "logout_usuario.php";
        }
    </script>
</body>
</html>
