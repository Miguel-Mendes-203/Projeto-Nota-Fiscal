<?php
include 'conexao.php';
$nomeCompleto = $_POST[ 'nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$confirmarSenha = $_POST['confirmar_senha'];

$insert = "INSERT INTO TB_USUARIO VALUES (NULL, '$nomeCompleto', '$email', '$senha'"


$query = $conexao->query($insert);

if ($query == true) {
    echo "<script> alert('Cadastro Realizado com sucesso!') history.back(); </script>";
} 




?>