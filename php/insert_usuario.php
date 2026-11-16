<?php
include 'conexao.php';

$nomeCompleto = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$confirmarSenha = $_POST['confirmar_senha'];

if ($senha != $confirmarSenha) {
    aviso("As senhas não conferem!");
}

$busca = $conexao->prepare("SELECT id_usuario FROM tb_usuario WHERE email = ?");
$busca->bind_param("s", $email);
$busca->execute();
$busca->store_result();

if ($busca->num_rows > 0) {
    aviso("Este email já está cadastrado!");
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$insert = $conexao->prepare("INSERT INTO tb_usuario (nm_usuario, email, senha) VALUES (?, ?, ?)");
$insert->bind_param("sss", $nomeCompleto, $email, $senhaHash);
$insert->execute();

aviso("Cadastro Realizado com sucesso!", "../login.html");
?>
