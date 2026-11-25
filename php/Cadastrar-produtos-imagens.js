
const inputImagem = document.getElementById('imagem');
const previa      = document.getElementById('previa');
const caixa       = document.getElementById('caixa-imagem');

inputImagem.addEventListener('change', () => {
    const arquivo = inputImagem.files[0];

    // Sem arquivo (ou não é imagem): volta para o bloco branco
    if (!arquivo || !arquivo.type.startsWith('image/')) {
        previa.hidden = true;
        previa.removeAttribute('src');
        caixa.classList.remove('com-imagem');
        return;
    }

    const leitor = new FileReader();
    leitor.onload = (e) => {
        previa.src    = e.target.result;
        previa.hidden = false;
        caixa.classList.add('com-imagem');
    };
    leitor.readAsDataURL(arquivo);
});