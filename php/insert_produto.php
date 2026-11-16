<?php
include 'verifica_login.php';
include 'conexao.php';

$nome = $_POST['nome'];
$valor = $_POST['valor'];
$imagem = "";

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $extensao = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
    $permitidas = ["jpg", "jpeg", "png", "gif", "webp"];

    if (!in_array($extensao, $permitidas)) {
        aviso("A imagem precisa ser jpg, png, gif ou webp!");
    }

    if (!is_dir("../uploads")) {
        mkdir("../uploads");
    }

    $nomeArquivo = uniqid() . "." . $extensao;
    move_uploaded_file($_FILES['imagem']['tmp_name'], "../uploads/" . $nomeArquivo);
    $imagem = "uploads/" . $nomeArquivo;
}

$insert = $conexao->prepare("INSERT INTO tb_produto (nm_produto, vl_unitario, ds_imagem) VALUES (?, ?, ?)");
$insert->bind_param("sds", $nome, $valor, $imagem);
$insert->execute();

aviso("Produto cadastrado com sucesso!", "../home.html");
?>
