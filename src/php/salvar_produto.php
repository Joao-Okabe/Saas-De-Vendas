<?php
session_start();


require "conecta.php";

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Erro: conexão com o banco não encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Requisição inválida.");
}


$nome      = isset($_POST['nome_produto']) ? trim($_POST['nome_produto'])
           : (isset($_POST['nm_produto']) ? trim($_POST['nm_produto']) : '');
$descricao = isset($_POST['descricao_produto']) ? trim($_POST['descricao_produto'])
           : (isset($_POST['ds_produto']) ? trim($_POST['ds_produto']) : '');
$preco     = isset($_POST['preco_produto']) ? trim($_POST['preco_produto'])
           : (isset($_POST['vl_produto']) ? trim($_POST['vl_produto']) : '');
$estoque   = isset($_POST['estoque_produto']) ? trim($_POST['estoque_produto']) : null;
$tipo      = isset($_POST['ds_tipo']) ? trim($_POST['ds_tipo']) : '';


if ($nome === '' || $preco === '') {
    die("Nome e preço são obrigatórios.");
}


$cnpj = $_SESSION['cnpj_loja'];
if (!$cnpj) {
    die("SEM CNPJ NA SESSÃO");
}


$fotoBD = null;
$fileField = null;
if (isset($_FILES['imagem'])) $fileField = 'imagem';
elseif (isset($_FILES['ds_foto'])) $fileField = 'ds_foto';
elseif (isset($_FILES['foto'])) $fileField = 'foto';

if ($fileField && isset($_FILES[$fileField]) && $_FILES[$fileField]['error'] !== UPLOAD_ERR_NO_FILE) {
    $arquivo = $_FILES[$fileField];

    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        die("Erro no upload da imagem (código {$arquivo['error']}).");
    }

    $ext = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg','jpeg','png','gif'];
    if (!in_array($ext, $permitidas)) {
        die("Formato inválido. Envie JPG, PNG ou GIF.");
    }

    $uploadDir = __DIR__ . '/uploads';
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            die("Falha ao criar pasta de uploads.");
        }
    }

    $novoNome = uniqid('p_') . '.' . $ext;
    $destinoAbs = $uploadDir . '/' . $novoNome;
    if (!move_uploaded_file($arquivo['tmp_name'], $destinoAbs)) {
        die("Erro ao mover o arquivo.");
    }

    $fotoBD = 'uploads/' . $novoNome; 
}


$nome_e = $conn->real_escape_string($nome);
$desc_e = $conn->real_escape_string($descricao);
$preco_e = $conn->real_escape_string($preco);
$tipo_e = $conn->real_escape_string($tipo);
$foto_e = $fotoBD ? $conn->real_escape_string($fotoBD) : 'NULL';

$sql = "INSERT INTO PRODUTO (nm_produto, ds_produto, vl_produto, ds_foto, ds_tipo)
        VALUES ('$nome_e', '$desc_e', '$preco_e', " . ($fotoBD ? "'$foto_e'" : "NULL") . ", '$tipo_e')";

if (!$conn->query($sql)) {
    die("Erro ao salvar produto: " . $conn->error);
}

$cd_produto = $conn->insert_id;
$cnpj_e = $conn->real_escape_string($cnpj);
$sql2 = "INSERT INTO LOJA_PRODUTO (cd_cnpj, cd_produto) VALUES ('$cnpj_e', $cd_produto)";
if (!$conn->query($sql2)) {
    
    $conn->query("DELETE FROM PRODUTO WHERE cd_produto = $cd_produto");
    die("Erro ao vincular produto à loja: " . $conn->error);
}

echo "OK";
?>
