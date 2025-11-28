<?php
// index.php
require_once 'config.php';
require_once 'header.php'; 

// Busca Produtos da Grade Principal (Corpo)
$produtosGrade = [];
try {
    $sqlTodos = "SELECT p.id_produto, p.nome, p.preco, p.imagem_url, c.nome_categoria 
                 FROM produtos p 
                 LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                 ORDER BY p.id_produto DESC";
    $stmtTodos = $pdo->query($sqlTodos);
    $produtosGrade = $stmtTodos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    $produtosGrade = []; 
    echo "Erro: " . $e->getMessage();
}
?>

<style>
    /* --- AJUSTES GERAIS E RODAPÉ --- */
    :root, html, body { height: 100%; margin: 0; padding: 0; }
    
    body { 
        display: flex; 
        flex-direction: column; 
        min-height: 100vh; 
        background-color: var(--bs-body-bg); 
        color: var(--bs-body-color); 
    }
    
    /* Empurra o rodapé para o final */
    footer { margin-top: auto; }

    /* --- ESTILO DOS CARDS DE PRODUTO --- */
    .card-img-wrapper {
        height: 250px; 
        overflow: hidden; 
        position: relative;
    }
    
    .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    /* Efeito de zoom na imagem ao passar o mouse */
    .card:hover .card-img-top {
        transform: scale(1.05);
    }

    /* Botão Dourado */
    .btn-comprar {
        background-color: #d4af37; 
        color: white; 
        border: none; 
        font-weight: bold; 
        width: 100%; 
        transition: 0.3s;
    }
    .btn-comprar:hover { 
        background-color: #bfa34b; 
        color: white; 
        transform: scale(1.02); 
    }
</style>

<div class="container mt-5 text-center">
    <h1 class="display-5" style="font-family: 'Playfair Display', serif;">Coleção Completa</h1>
    <p class="text-muted">Explore a elegância em cada detalhe.</p>
    <hr class="w-25 mx-auto mb-5" style="color: #d4af37; opacity: 1;">
</div>

<div class="container pb-5">
    
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
        
        <?php if(count($produtosGrade) > 0): ?>
            
            <?php foreach($produtosGrade as $prod): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    
                    <div class="card-img-wrapper">
                        <?php 
                            $imgSrc = !empty($prod['imagem_url']) ? $prod['imagem_url'] : 'https://via.placeholder.com/300?text=Lumière'; 
                        ?>
                        <img src="<?= $imgSrc ?>" class="card-img-top" alt="<?= $prod['nome'] ?>">
                        
                        <span class="position-absolute top-0 start-0 badge bg-white text-dark m-2 shadow-sm rounded-0">
                            <?= $prod['nome_categoria'] ?? 'Joia' ?>
                        </span>
                    </div>
                    
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title fs-6 fw-bold text-uppercase text-truncate"><?= $prod['nome'] ?></h5>
                        
                        <p class="card-text fw-bold fs-5 mb-3" style="color: #d4af37;">
                            R$ <?= number_format($prod['preco'], 2, ',', '.') ?>
                        </p>
                        
                        <a href="carrinho_acoes.php?acao=add&id=<?= $prod['id_produto'] ?>" class="btn btn-comprar mt-auto py-2">
                            <i class="fa-solid fa-bag-shopping me-2"></i> Adicionar
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="col-12 text-center text-muted py-5">
                <h4><i class="fa-regular fa-folder-open me-2"></i> Nenhum produto encontrado na loja.</h4>
                <?php if(isset($_SESSION['nivel_acesso']) && $_SESSION['nivel_acesso'] == 'admin'): ?>
                    <a href="admin.php" class="btn btn-outline-warning mt-3">Cadastrar Produtos Agora</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php require_once 'footer.php'; ?>