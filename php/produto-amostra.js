// produto.js - ao informar o código do produto, mostra a imagem e o valor unitário

const campoCodigo = document.getElementById('codigo_produto');
const campoValor  = document.getElementById('valor');
const previa      = document.getElementById('previa');
const aviso       = document.getElementById('aviso-produto');

function limpar(mensagem) {
    previa.hidden = true;
    previa.removeAttribute('src');
    campoValor.value = '';

    aviso.hidden = !mensagem;
    aviso.textContent = mensagem || '';
}

async function buscarProduto() {
    const codigo = campoCodigo.value.trim();

    if (codigo === '') {
        limpar();
        return;
    }

    try {
        const resposta = await fetch('buscar_produto.php?codigo=' + encodeURIComponent(codigo));
        const produto  = await resposta.json();

        if (!produto.encontrado) {
            limpar('Produto não encontrado.');
            return;
        }

        aviso.hidden = true;
        campoValor.value = 'R$ ' + Number(produto.valor).toFixed(2).replace('.', ',');

        if (produto.imagem) {
            previa.src    = produto.imagem;
            previa.hidden = false;
        } else {
            previa.hidden = true;   // produto sem imagem: continua o bloco branco
            previa.removeAttribute('src');
        }
    } catch (erro) {
        limpar('Não foi possível buscar o produto.');
    }
}

campoCodigo.addEventListener('change', buscarProduto);
campoCodigo.addEventListener('blur', buscarProduto);

if (campoCodigo.value.trim() !== '') {
    buscarProduto();
}