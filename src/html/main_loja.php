<?php
    session_start();
    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header("Location: login_loja.html");
        exit;
    }
    require __DIR__ . "/conecta.php";
    require __DIR__ . "/listar_produtos.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/main-loja.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Deméter</title>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <h1 class="text-white offset-2">Deméter</h1>
        <div class="navbar-btn-group">
            <button class="btn-premium" onclick="">
                    <span class="agrandir-light">Editar Perfil</span>
                    <span>Editar Perfil</span>
            </button>
            <button class="btn-premium" onclick="Sair()">
                    <span class="agrandir-light">Sair</span>
                    <span>Sair</span>
            </button>
        </div>
    </nav>
    <!-- Lista -->
    <div class="row offset-2 mt-5">
        <h1>Meus Produtos</h1>
    </div>
    <div class="row offset-2 col-8">
        <table class="table table-striped table-hover shadow-sm mt-4">
            <thead class="table-primary"> 
                <tr>
                    <th>Identificador</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    listarProduto($conn);
                ?>
            </tbody>
        </table>
    </div>
    <!-- Adicionar -->
     <div class="row offset-2 mt-5">
        <h1>Adicionar</h1>
     </div>
    <div class="row offset-2">
        <div class="card col-md-3 col-sm-10" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Adicionar Produto</h5>
            <p class="card-text">Adicione seu produto com preço, nome e imagem.</p> 
            <!-- Button trigger adicionarProdutoModal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarProdutoModal">
                Adicionar produto!
            </button>
        </div>
        </div>
        <div class="card col-md-3 col-sm-10" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Adicionar Cupom</h5>
            <p class="card-text">Adicione seu cumpom</p> 
            <!-- Button trigger adicionarProdutoModal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarCupomModal">
                Adicionar cumpom!
            </button>
        </div>
        </div>
        <div class="card col-md-3 col-sm-10" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title">Adicionar Cardápio</h5>
            <p class="card-text">Adicione seu Cardápio Digital!</p> 
            <!-- Button trigger adicionarProdutoModal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarCardápio">
                Adicionar cardápio!
            </button>
        </div>
        </div>
    </div>
    <!-- Modal Adicionar Produto -->
    <div class="modal fade" id="adicionarProdutoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Adicionar Produto</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formProduto" enctype="multipart/form-data">
                    <label>Nome:</label><br>
                    <input type="text" name="nm_produto" required><br><br>

                    <label>Descrição:</label><br>
                    <textarea name="ds_produto"></textarea><br><br>

                    <label>Preço (R$):</label><br>
                    <input type="number" step="0.01" name="vl_produto" required><br><br>

                    <label>Tipo:</label><br>
                    <input type="text" name="ds_tipo"><br><br>

                    <label>Foto (jpg/png/gif) - opcional:</label><br>
                    <input type="file" name="ds_foto" accept="image/*">
                    <br>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Cadastrar Produto</button>
                    </div>  
                </form>
            </div>
            </div>
        </div>
    </div>
    <div id="msg"></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="../js/jquery-3.7.1.min.js"></script>
<script>

function Sair(){
    window.location.href =  "logout.php";
}

$('#formProduto').on('submit', function(e) {
    e.preventDefault();
    var fd = new FormData(this);

    $.ajax({
        url: 'salvar_produto.php',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(res) {
            $('#msg').html(res);
            if (res.indexOf('sucesso') !== -1) {
                $('#formProduto')[0].reset();
                $('#formProduto input[type="file"]').val('');
            }
        },
        error: function() {
            alert('Erro na comunicação com o servidor.');
        }
    });
});

$(document).on('click', '.del', function(){
    if (!confirm('Excluir este produto?')) return;

    var id = $(this).data('id');

    $.post('excluir_produtos.php', { cd_produto: id }, function(resp){
        alert(resp);
        location.reload();
    });
});
</script>
</body>
</html>