<?php
// cadastrar_produto.php - SOMENTE lógica PHP (valida, grava e chama a view HTML)

require_once 'verifica_login.php';
require_once 'conexao.php';

$erro    = '';
$sucesso = '';
$dados   = ['nome' => '', 'valor' => ''];

$tiposPermitidos = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'image/webp' => 'webp',
];
$tamanhoMaximo = 2 * 1024 * 1024;
$pastaUploads  = __DIR__ . '/uploads/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados['nome']  = trim($_POST['nome'] ?? '');
    $dados['valor'] = trim($_POST['valor'] ?? '');

    // Aceita vírgula ou ponto no valor (ex.: 12,50)
    $valor = str_replace(',', '.', $dados['valor']);

    $temImagem = isset($_FILES['imagem']) && $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($dados['nome'] === '' || $dados['valor'] === '') {
        $erro = 'Preencha o nome e o valor unitário.';
    } elseif (mb_strlen($dados['nome']) > 25) {
        $erro = 'O nome do produto deve ter no máximo 25 caracteres.';
    } elseif (!is_numeric($valor) || $valor <= 0 || $valor > 999.99) {
        $erro = 'Valor unitário inválido (use um valor entre 0,01 e 999,99).';
    } elseif ($temImagem && $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Erro ao enviar a imagem. Tente novamente.';
    } else {
        $nomeImagem = null;

        // Trata o upload da imagem (opcional)
        if ($temImagem) {
            $arquivo = $_FILES['imagem'];
            $mime    = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);

            if (!isset($tiposPermitidos[$mime])) {
                $erro = 'Envie uma imagem JPG, PNG, GIF ou WEBP.';
            } elseif ($arquivo['size'] > $tamanhoMaximo) {
                $erro = 'A imagem deve ter no máximo 2 MB.';
            } else {
                if (!is_dir($pastaUploads)) {
                    mkdir($pastaUploads, 0755, true);
                }
                $nomeImagem = bin2hex(random_bytes(8)) . '.' . $tiposPermitidos[$mime];

                if (!move_uploaded_file($arquivo['tmp_name'], $pastaUploads . $nomeImagem)) {
                    $erro = 'Não foi possível salvar a imagem.';
                    $nomeImagem = null;
                }
            }
        }

        if ($erro === '') {
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO tb_produto (nm_produto, vl_unitario, nm_imagem)
                     VALUES (:nome, :valor, :imagem)"
                );
                $stmt->execute([
                    ':nome'   => $dados['nome'],
                    ':valor'  => number_format((float) $valor, 2, '.', ''),
                    ':imagem' => $nomeImagem,
                ]);

                $sucesso = 'Produto cadastrado com sucesso!';
                $dados   = ['nome' => '', 'valor' => ''];
            } catch (PDOException $e) {
                // Se o banco falhar, remove a imagem que acabou de ser salva
                if ($nomeImagem !== null) {
                    @unlink($pastaUploads . $nomeImagem);
                }
                $erro = 'Erro ao cadastrar o produto. Tente novamente.';
            }
        }
    }
}

// Carrega o HTML (separado da lógica)
require 'cadastrar_produto_view.php';