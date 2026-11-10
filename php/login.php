<?php
include 'conexao.php';


$email = $_POST['email'];
$senha = $_POST['senha'];

$select = "SELECT * FROM TB_USUARIO WHERE EMAIL= '$email' AND senha = '$senha' ";

$query = $conexao->query($select);

$result = mysqli_fetch_assoc($query);
$email_banco = $result ['EMAIL'];
$senha_banco = $result['senha'];
if ( $email == $email_banco $$ $senha == $senha_banco) {

session_start();
$_SESSION['email'] = $senha_banco;
$_SESSION['nome'] = $result['NM_USUARIO']

header("Location:../home.html");
}

?>