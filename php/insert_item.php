<?php
include 'verifica_login.php';
include 'conexao.php';

$codProduto = (int) $_POST['cod_produto'];
$codNota = (int) $_POST['cod_nota'];
$quantidade = (int) $_POST['quantidade'];
$valorUnitario = $_POST['valor_unitario'];

$valorTotalItem = $quantidade * $valorUnitario;

$insert = $conexao->prepare("INSERT INTO tb_produto_has_tb_nota (fk_cd_produto, fk_cd_nota, qt_produto, vl_total_item) VALUES (?, ?, ?, ?)");
$insert->bind_param("iiid", $codProduto, $codNota, $quantidade, $valorTotalItem);

try {
    $insert->execute();
} catch (mysqli_sql_exception $e) {
    aviso("Não foi possível cadastrar. O produto e a nota existem?");
}

aviso("Item cadastrado com sucesso!", "../produto-amostra.html");
?>
