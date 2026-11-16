<?php
include 'verifica_login.php';
include 'conexao.php';

$emissao = $_POST['emissao'];
$envio = $_POST['envio'];
$valor = $_POST['valor'];
$cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']);

$codigo = null;
if ($_POST['codigo'] != "") {
    $codigo = (int) $_POST['codigo'];
}

$busca = $conexao->prepare("SELECT nr_cnpj FROM tb_cliente WHERE nr_cnpj = ?");
$busca->bind_param("s", $cnpj);
$busca->execute();
$busca->store_result();

if ($busca->num_rows == 0) {
    aviso("Esse CNPJ não está cadastrado como cliente!");
}

$insert = $conexao->prepare("INSERT INTO tb_nota (cd_nota, dt_emissao, dt_envio, vl_total, fk_nr_cnpj) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("issds", $codigo, $emissao, $envio, $valor, $cnpj);

try {
    $insert->execute();
} catch (mysqli_sql_exception $e) {
    aviso("Não foi possível cadastrar. Esse código de nota já existe?");
}

aviso("Nota cadastrada com sucesso!", "../home.html");
?>
