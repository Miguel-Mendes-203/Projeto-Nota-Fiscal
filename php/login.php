<?php
include 'conexao.php';
session_start();

$email = $_POST['email'];
$senha = $_POST['senha'];

$select = $conexao->prepare("SELECT * FROM tb_usuario WHERE email = ?");
$select->bind_param("s", $email);
$select->execute();
$result = $select->get_result()->fetch_assoc();

if ($result && password_verify($senha, $result['senha'])) {
    $_SESSION['id'] = $result['id_usuario'];
    $_SESSION['email'] = $result['email'];
    $_SESSION['nome'] = $result['nm_usuario'];

    header("Location: ../home.html");
} else {
    aviso("Email ou senha incorretos!");
}
?>
