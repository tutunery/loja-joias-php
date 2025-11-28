<?php
// admin.php
// 1. Inicia Sessão
session_start();

// 2. Conexão
if (file_exists('config.php')) {
    require_once 'config.php';
} elseif (file_exists('conexao.php')) {
    require_once 'conexao.php';
}

// 3. SEGURANÇA (O PULO DO GATO PARA PARAR O LOOP)
// Se não tiver sessão OU se o nível não for 'admin'
if (!isset($_SESSION['nivel_acesso']) || $_SESSION['nivel_acesso'] !== 'admin') {
    // Manda para a página inicial (INDEX) e não para o header
    header("Location: index.php"); 
    exit();
}

// --- Lógica de Carregamento de Dados (Para a Tabela e Selects) ---
$joias = [];
$categorias = [];

try {
    // Busca Joias
    $sql = "SELECT id_produto, nome, preco, imagem_url FROM produtos ORDER BY id_produto DESC";
    $stmt = $pdo->query($sql);
    $joias = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Busca Categorias
    $sql_cat = "SELECT id_categoria, nome_categoria FROM categorias ORDER BY nome_categoria ASC";
    $stmt_cat = $pdo->query($sql_cat);
    $categorias = $stmt_cat->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo "Erro DB: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Admin - Lumière</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background: #343a40; color: #fff; }
        .sidebar a { color: #ccc; text-decoration: none; display: block; padding: 10px; }
        .sidebar a:hover { color: #fff; background: #495057; }
        .card { box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: none; margin-bottom: 20px; }
        .table img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand ps-3" href="#">Painel Master</a>
    <div class="ms-auto pe-3">
        <a href="header.php" class="btn btn-outline-light btn-sm">Ver Loja</a>
        <a href="logout.php" class="btn btn-danger btn-sm ms-2">Sair</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 p-4">
            
            <?php if (isset($_GET['status'])): ?>
                <?php 
                    $msg = $_GET['msg'] ?? '';
                    if($_GET['status'] == 'sucesso_cadastro') echo '<div class="alert alert-success">Joia cadastrada com sucesso!</div>';
                    if($_GET['status'] == 'sucesso_edicao') echo '<div class="alert alert-success">Joia atualizada com sucesso!</div>';
                    if($_GET['status'] == 'sucesso_delecao') echo '<div class="alert alert-success">Joia removida.</div>';
                    if($_GET['status'] == 'erro') echo '<div class="alert alert-danger">Erro: '.$msg.'</div>';
                ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card p-3">
                        <h5 class="card-title mb-3"><i class="fa-solid fa-plus-circle"></i> Cadastrar Nova Joia</h5>
                        <form action="processa_joia.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="acao" value="criar">
                            
                            <div class="mb-3">
                                <label>Nome</label>
                                <input type="text" name="nome" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Preço (R$)</label>
                                <input type="number" step="0.01" name="preco" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Categoria</label>
                                <select name="id_categoria" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat->id_categoria ?>"><?= $cat->nome_categoria ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Imagem</label>
                                <input type="file" name="imagem" class="form-control" accept="image/*" required>
                            </div>
                            <div class="mb-3">
                                <label>Descrição</label>
                                <textarea name="descricao" class="form-control" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Salvar Produto</button>
                        </form>
                    </div>

                    <div id="box-editar" class="card p-3 mt-3 border-warning" style="display:none;">
                        <h5 class="card-title text-warning"><i class="fa-solid fa-pen"></i> Editando Joia</h5>
                        <form action="processa_joia.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="acao" value="editar">
                            <input type="hidden" name="id_produto" id="edit-id">
                            
                            <div class="mb-2">
                                <label>Nome</label>
                                <input type="text" name="nome" id="edit-nome" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label>Preço</label>
                                <input type="number" step="0.01" name="preco" id="edit-preco" class="form-control" required>
                            </div>
                            <div class="mb-2">
                                <label>Categoria</label>
                                <select name="id_categoria" id="edit-cat" class="form-select" required>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat->id_categoria ?>"><?= $cat->nome_categoria ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label>Nova Imagem (Opcional)</label>
                                <input type="file" name="imagem" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label>Descrição</label>
                                <textarea name="descricao" id="edit-desc" class="form-control"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning w-100">Atualizar</button>
                                <button type="button" onclick="cancelarEdicao()" class="btn btn-secondary">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card p-3">
                        <h5 class="card-title mb-3">Lista de Produtos (<?= count($joias) ?>)</h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Imagem</th>
                                        <th>Nome</th>
                                        <th>Preço</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($joias as $j): ?>
                                    <tr>
                                        <td><?= $j->id_produto ?></td>
                                        <td>
                                            <?php $img = !empty($j->imagem_url) ? $j->imagem_url : 'https://via.placeholder.com/50'; ?>
                                            <img src="<?= $img ?>" alt="Foto">
                                        </td>
                                        <td><?= $j->nome ?></td>
                                        <td>R$ <?= number_format($j->preco, 2, ',', '.') ?></td>
                                        <td>
                                            <button onclick="editar(<?= $j->id_produto ?>)" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></button>
                                            
                                            <form id="form-deletar-<?= $j->id_produto ?>" action="processa_joia.php" method="POST" class="d-inline">
                                                <input type="hidden" name="acao" value="deletar">
                                                <input type="hidden" name="id_produto" value="<?= $j->id_produto ?>">
                                                <button type="button" onclick="confirmarExclusao(<?= $j->id_produto ?>)" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 3. ADICIONADO: Função do SweetAlert
function confirmarExclusao(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Você não poderá reverter isso!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, apagar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-deletar-' + id).submit();
        }
    })
}

function editar(id) {
    // 1. Chama a API que criamos
    fetch('api_detalhe_joia.php?id=' + id)
        .then(response => response.json())
        .then(json => {
            if(json.success) {
                const dado = json.data;
                
                // 2. Preenche os campos do formulário amarelo
                document.getElementById('edit-id').value = dado.id_produto;
                document.getElementById('edit-nome').value = dado.nome;
                document.getElementById('edit-preco').value = dado.preco;
                document.getElementById('edit-cat').value = dado.id_categoria;
                document.getElementById('edit-desc').value = dado.descricao;
                
                // 3. Mostra o formulário e esconde o de cadastro (opcional, aqui mostro ambos)
                document.getElementById('box-editar').style.display = 'block';
                document.getElementById('box-editar').scrollIntoView({behavior: "smooth"});
            } else {
                alert('Erro ao carregar dados.');
            }
        });
}

function cancelarEdicao() {
    document.getElementById('box-editar').style.display = 'none';
}
</script>

</body>
</html>