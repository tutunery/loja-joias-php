<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'config.php'; 

// Lógica PHP: Busca e Lista todas as joias
try {
    $sql = "SELECT id_produto, nome, preco FROM produtos ORDER BY id_produto DESC";
    $stmt = $pdo->query($sql);
    $joias = $stmt->fetchAll();
    
    // Lógica PHP: Busca todas as categorias para o <select>
    $sql_cat = "SELECT id_categoria, nome_categoria FROM categorias ORDER BY nome_categoria ASC";
    $stmt_cat = $pdo->query($sql_cat);
    $categorias = $stmt_cat->fetchAll();

} catch (PDOException $e) {
    die("Erro ao carregar dados iniciais: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - Joias</title>
    <style>
        /* Estilos CSS simplificados para o painel */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; color: #333; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 1200px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        h1 { color: #a87f5e; border-bottom: 2px solid #a87f5e; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #555; margin-top: 30px; border-left: 5px solid #a87f5e; padding-left: 10px; }
        form { background: #f9f9f9; padding: 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #eee; }
        input[type="text"], input[type="number"], textarea, select, input[type="file"] { width: 100%; padding: 10px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #a87f5e; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; transition: background-color 0.3s; }
        button:hover { background-color: #8c6a4e; }
        .btn-delete { background-color: #e74c3c; }
        .btn-delete:hover { background-color: #c0392b; }
        .btn-buscar { background-color: #5d5d5d; }
        .btn-buscar:hover { background-color: #333; }
        .table-admin { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table-admin th, .table-admin td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .table-admin th { background-color: #a87f5e; color: white; }
        .alerta { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .alerta.sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alerta.erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="container">
    <h1>Painel de Gerenciamento de Joias</h1>

    <?php
    if (isset($_GET['status'])) {
        $status = $_GET['status'];
        $mensagem = '';
        $class = '';

        if ($status == 'sucesso_cadastro') {
            $mensagem = "✅ Joia cadastrada com sucesso!";
            $class = 'sucesso';
        } elseif ($status == 'sucesso_edicao') {
            $mensagem = "✅ Joia atualizada com sucesso!";
            $class = 'sucesso';
        } elseif ($status == 'sucesso_delecao') {
            $mensagem = "✅ Joia deletada com sucesso!";
            $class = 'sucesso';
        } elseif ($status == 'erro') {
            // A mensagem de erro vem do 'msg' na URL
            $mensagem = "❌ Ocorreu um erro: " . htmlspecialchars($_GET['msg'] ?? 'Erro desconhecido.');
            $class = 'erro';
        }

        if ($mensagem) {
            echo "<div class='alerta $class'>$mensagem</div>";
        }
    }
    ?>

    <h2>Cadastrar Nova Joia (CREATE)</h2>
    <form id="form-cadastro" action="processa_joia.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="acao" value="criar">
        
        <label for="nome">Nome da Joia:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="preco">Preço (R$):</label>
        <input type="number" id="preco" name="preco" step="0.01" min="0" required>

        <label for="id_categoria">Categoria:</label>
        <select id="id_categoria" name="id_categoria" required>
            <option value="">Selecione a Categoria</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= htmlspecialchars($cat->id_categoria) ?>">
                    <?= htmlspecialchars($cat->nome_categoria) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" rows="4"></textarea>

        <label for="imagem">Imagem da Joia:</label>
        <input type="file" id="imagem" name="imagem" accept="image/*" required>

        <button type="submit">Cadastrar Joia</button>
    </form>

    <h2>Buscar Joia (READ/UPDATE)</h2>
    <form id="form-busca" onsubmit="event.preventDefault(); buscarJoia();">
        <input type="number" id="busca-id" placeholder="Digite o ID da Joia para buscar e editar..." required>
        <button type="submit" class="btn-buscar">Buscar para Editar</button>
    </form>
    
    <div id="secao-edicao" style="display: none;">
        <h2>Editar Joia Existente (UPDATE/DELETE)</h2>
        <form id="form-edicao" action="processa_joia.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="acao" value="editar">
            <input type="hidden" id="edit-id" name="id_produto">
            
            <label for="edit-nome">Nome da Joia:</label>
            <input type="text" id="edit-nome" name="nome" required>

            <label for="edit-preco">Preço (R$):</label>
            <input type="number" id="edit-preco" name="preco" step="0.01" required>
            
            <label for="edit-id_categoria">Categoria:</label>
            <select id="edit-id_categoria" name="id_categoria" required>
                <option value="">Selecione a Categoria</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= htmlspecialchars($cat->id_categoria) ?>">
                        <?= htmlspecialchars($cat->nome_categoria) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <label for="edit-descricao">Descrição:</label>
            <textarea id="edit-descricao" name="descricao" rows="4"></textarea>

            <label for="edit-imagem">Nova Imagem (Opcional):</label>
            <input type="file" id="edit-imagem" name="imagem" accept="image/*">
            <p>Selecione um arquivo apenas se quiser substituir a imagem atual.</p>


            <button type="submit">Salvar Alterações</button>
            <button type="button" class="btn-delete" onclick="deletarJoia(document.getElementById('edit-id').value)">Deletar Joia</button>
        </form>
    </div>
    
    <h2>Lista de Joias Cadastradas</h2>
    <table class="table-admin">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($joias as $joia): ?>
                <tr id="joia-<?= htmlspecialchars($joia->id_produto) ?>">
                    <td><?= htmlspecialchars($joia->id_produto) ?></td>
                    <td><?= htmlspecialchars($joia->nome) ?></td>
                    <td>R$ <?= number_format($joia->preco, 2, ',', '.') ?></td>
                    <td>
                        <button type="button" onclick="carregarParaEdicao(<?= $joia->id_produto ?>)">Editar</button>
                        <button type="button" class="btn-delete" onclick="confirmarDelecao(<?= $joia->id_produto ?>, '<?= htmlspecialchars($joia->nome) ?>')">Deletar</button>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($joias)): ?>
                <tr><td colspan="4">Nenhuma joia cadastrada ainda.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<script>
    
    // Função para buscar uma joia por ID a partir do formulário de busca
    function buscarJoia() {
        const id = document.getElementById('busca-id').value;
        if (id) {
            carregarParaEdicao(id);
        }
    }

    // Função principal para carregar os dados no formulário de edição via AJAX
    function carregarParaEdicao(id) {
        fetch(`api_detalhe_joia.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const joia = data.data;

                    // 1. Preenche os campos do formulário de edição
                    document.getElementById('edit-id').value = joia.id_produto;
                    document.getElementById('edit-nome').value = joia.nome;
                    document.getElementById('edit-preco').value = parseFloat(joia.preco).toFixed(2);
                    document.getElementById('edit-id_categoria').value = joia.id_categoria;
                    document.getElementById('edit-descricao').value = joia.descricao;
                    
                    // 2. Mostra a seção de edição
                    document.getElementById('secao-edicao').style.display = 'block';

                    // 3. Rola para a seção de edição
                    document.getElementById('secao-edicao').scrollIntoView({ behavior: 'smooth' });

                } else {
                    alert("Erro ao carregar os dados da joia: " + data.message);
                }
            })
            .catch(error => {
                alert("Erro de conexão ao buscar detalhes: Verifique o console para mais detalhes.");
                console.error('Erro na requisição AJAX:', error);
            });
    }

    // Função para confirmar e enviar a requisição de deleção
    function confirmarDelecao(id, nome) {
        if (confirm("Tem certeza que deseja DELETAR a joia: " + nome + " (ID: " + id + ")?")) {
            deletarJoia(id);
        }
    }
    
    function deletarJoia(id) {
        // Envia requisição POST para o mesmo processa_joia.php
        // Usamos FormData para simular o envio de um formulário POST
        const formData = new FormData();
        formData.append('acao', 'deletar');
        formData.append('id_produto', id);

        fetch('processa_joia.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
             // Redireciona para o admin.php para exibir a mensagem de status
             window.location.href = 'admin.php?status=sucesso_delecao';
        })
        .catch(error => {
            alert("Erro ao deletar joia: " + error.message);
            console.error('Erro na requisição AJAX:', error);
        });
    }
</script>

</body>
</html>