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