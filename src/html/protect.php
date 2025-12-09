<?php
if (!isset($_SESSION)) {
    session_start();
}

if(!isset($_SESSION['email'])){
    die("Você precisa estar logado para acessar está área <p><a href=\"Index.php\">Sair</a></p>");
}

?>