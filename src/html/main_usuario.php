<?php
require "conecta.php";
session_start();

if (!isset($_SESSION['cpf'])) {
    header("Location: login_usuario.php");
    exit;
}


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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <?php listarProdutoCliente($res); ?>
            </tbody>
        </table>
    </div>

    <!-- Carrinho -->
    <div class="row offset-2 mt-5">
        <h1 class="agrandir-bold">Carrinho</h1>
    </div>
    <div class="offset-2 col-8">
    <?php
        if (!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
            echo "<p>Seu carrinho está vazio.</p>";
            exit;
        }

        echo '<form method="POST" action="processar_pagamento.php">';
        echo '<table class="table table-striped table-hover shadow-sm">
                <thead class="table-primary"> 
                    <tr>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Preço</th>
                        <th>Total</th>
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
            echo "</tr>";
        }

        echo "</tbody></table>";
        echo "<h2>Total Geral: R$ " . number_format($total_geral, 2, ',', '.') . "</h2>";

        echo '<h3 class="mt-4">Selecione o Método de Pagamento</h3>';
        echo '
        <div class="card p-3 mb-4">
            <label><input type="radio" name="metodo" value="cartao" required> Cartão de Crédito</label><br>
            <label><input type="radio" name="metodo" value="pix" required> PIX</label>
        </div>

        <!-- CAMPOS DO CARTÃO -->
        <div id="pag_cartao" style="display:none;" class="card p-3 mb-4">
            <h5>Pagamento com Cartão</h5>
            <label>Número do cartão:</label>
            <input type="text" class="form-control mb-2" name="numero_cartao">

            <label>Validade:</label>
            <input type="month" class="form-control mb-2" name="validade">

            <label>CVV:</label>
            <input type="text" class="form-control mb-2" name="cvv">

            <label>Nome impresso:</label>
            <input type="text" class="form-control mb-2" name="nome_cartao">
        </div>

        <!-- PIX -->
        <div id="pag_pix" style="display:none;" class="card p-3 mb-4">
            <h5>Pagamento via PIX</h5>
            <p>Use a chave abaixo:</p>
            <input class="form-control" value="11.222.333/0001-99 (Chave CNPJ)" readonly>
            <label class="mt-2"><input type="checkbox" name="confirmar_pix"> Confirmo que realizei o pagamento</label>
        </div>

        <button class="btn btn-success mt-3" type="submit">Finalizar Compra</button>
        </form>

        <script>
        document.querySelectorAll("input[name=metodo]").forEach(radio => {
            radio.addEventListener("change", function() {
                document.getElementById("pag_cartao").style.display = this.value === "cartao" ? "block" : "none";
                document.getElementById("pag_pix").style.display = this.value === "pix" ? "block" : "none";
            });
        });
        </script>
        ';
    ?>
    </div>

    <script>
        function Sair(){
            window.location.href = "logout_usuario.php";
        }
    </script>
</body>
</html>