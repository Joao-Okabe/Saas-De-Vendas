<?php
    function listarProduto($conn){
        require "conecta.php";

        if (!isset($_SESSION['logado']) || !isset($_SESSION['cnpj_loja'])) {
            echo "Você precisa estar logado.";
            exit;
        }

        $cnpj = $conn->real_escape_string($_SESSION['cnpj_loja']);


        $sql = "SELECT p.cd_produto, p.nm_produto, p.ds_produto, p.vl_produto, p.ds_foto
                FROM PRODUTO p
                JOIN LOJA_PRODUTO lp ON lp.cd_produto = p.cd_produto
                WHERE lp.cd_cnpj = '$cnpj' ORDER BY p.cd_produto DESC";

        $res = $conn->query($sql);

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
                echo "<td>
                    <button class='del btn btn-danger' data-id='{$row['cd_produto']}'>
                        Excluir
                    </button>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Nenhum produto cadastrado.</td></tr>";
        }
    }