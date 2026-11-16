<?php
include 'verifica_login.php';
include 'conexao.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$razaoSocial = $_POST['razao_social'];

$cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']);

if (strlen($cnpj) != 14) {
    aviso("O CNPJ precisa ter 14 números!");
}

$busca = $conexao->prepare("SELECT nr_cnpj FROM tb_cliente WHERE nr_cnpj = ?");
$busca->bind_param("s", $cnpj);
$busca->execute();
$busca->store_result();

if ($busca->num_rows > 0) {
    aviso("Este CNPJ já está cadastrado!");
}

$insert = $conexao->prepare("INSERT INTO tb_cliente (nr_cnpj, nm_cliente, nm_razao_social, nm_email) VALUES (?, ?, ?, ?)");
$insert->bind_param("ssss", $cnpj, $nome, $razaoSocial, $email);
$insert->execute();

aviso("Cliente cadastrado com sucesso!", "../home.html");
?>
