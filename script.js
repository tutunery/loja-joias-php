document.addEventListener('DOMContentLoaded', function() {
    const listaProdutosContainer = document.getElementById('lista-produtos-js');

    // Função para buscar os produtos via AJAX
    function carregarProdutos() {
        fetch('api_produtos.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    // Limpa o "Carregando produtos..."
                    listaProdutosContainer.innerHTML = ''; 
                    
                    data.data.forEach(produto => {
                        // Cria o elemento HTML para o produto
                        const card = document.createElement('div');
                        card.className = 'card-produto';

                        // Formata o preço para o padrão brasileiro
                        const precoFormatado = parseFloat(produto.preco).toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });

                        card.innerHTML = `
                            <img src="${produto.imagem_url}" alt="${produto.nome}">
                            <h3>${produto.nome}</h3>
                            <p class="categoria">${produto.nome_categoria}</p>
                            <p class="preco">${precoFormatado}</p>
                            <button class="btn-comprar" data-nome="${produto.nome}">Comprar</button>
                        `;

                        listaProdutosContainer.appendChild(card);
                    });

                    // Adiciona o evento de compra APÓS criar os botões
                    adicionarEventosDeCompra();

                } else {
                    listaProdutosContainer.innerHTML = '<p>Nenhum produto encontrado.</p>';
                }
            })
            .catch(error => {
                listaProdutosContainer.innerHTML = `<p>Erro ao carregar os dados: ${error.message}</p>`;
                console.error('Erro na requisição AJAX:', error);
            });
    }

    // Função para adicionar o listener de compra
    function adicionarEventosDeCompra() {
        const botoesComprar = document.querySelectorAll('.btn-comprar');
        botoesComprar.forEach(botao => {
            botao.addEventListener('click', function() {
                const nomeProduto = this.getAttribute('data-nome');
                alert(`Produto ${nomeProduto} adicionado ao carrinho (simulação).`);
                // Aqui você faria a chamada AJAX real para o carrinho
            });
        });
    }

    // Inicia o carregamento dos produtos
    carregarProdutos();
});

// --- Funções Auxiliares de Validação (Opcional, mas útil) ---

// Remove formatação para validar, como: 123.456.789-00 -> 12345678900
function limparFormato(valor) {
    return valor.replace(/\D/g, '');
}

// --- Lógica da API de CEP ---

function buscarCep(cep) {
    const cepLimpo = limparFormato(cep);
    
    // Verifica se o CEP tem 8 dígitos
    if (cepLimpo.length !== 8) {
        alert("O CEP deve ter 8 dígitos.");
        return;
    }

    // Limpa campos de endereço enquanto carrega
    document.getElementById('logradouro').value = '...';
    document.getElementById('bairro').value = '...';
    document.getElementById('cidade').value = '...';
    document.getElementById('uf').value = '...';

    // Requisição à API ViaCEP
    const url = `https://viacep.com.br/ws/${cepLimpo}/json/`;

    fetch(url)
        .then(response => response.json())
        .then(dados => {
            if (!("erro" in dados)) {
                // Preenche os campos do formulário
                document.getElementById('logradouro').value = dados.logradouro;
                document.getElementById('bairro').value = dados.bairro;
                document.getElementById('cidade').value = dados.localidade;
                document.getElementById('uf').value = dados.uf;
                document.getElementById('numero').focus(); // Move o foco para o número
            } else {
                alert("CEP não encontrado.");
                // Limpa os campos novamente se der erro
                document.getElementById('logradouro').value = '';
                document.getElementById('bairro').value = '';
                document.getElementById('cidade').value = '';
                document.getElementById('uf').value = '';
            }
        })
        .catch(error => {
            console.error('Erro na consulta de CEP:', error);
            alert("Erro ao consultar a API de CEP.");
        });
}
// OBS: A chamada desta função está no HTML (onblur="buscarCep(this.value)")